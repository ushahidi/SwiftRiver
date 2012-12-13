<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Buckets
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Bucket extends ORM {
	
	/**
	 * One-to-many relationship definitions
	 * @var array Relationhips
	 */
	protected $_has_many = array( 
		// A bucket has many droplets
		'droplets' => array(
			'model' => 'Droplet',
			'through' => 'buckets_droplets'
			),
		
		// A bucket has many collaborators
		'bucket_collaborators' => array(),

		// A bucket has many collaborators
		'bucket_comments' => array(),		

		// A bucket has many subscribers
		'subscriptions' => array(
			'model' => 'User',
			'through' => 'bucket_subscriptions',
			'far_key' => 'user_id'
			)
	);

	/**
	 * A bucket belongs to an account and a user
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array(),
		'user' => array()
	);
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'bucket_date_add', 'format' => 'Y-m-d H:i:s');

	/**
	 * Rules for the bucket model. 
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'bucket_name' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
			'bucket_publish' => array(
				array('in_array', array(':value', array('0', '1')))
			),
		);
	}	
	
	/**
	 * Override saving to perform additional functions on the bucket
	 */
	public function save(Validation $validation = NULL)
	{
		if ( ! isset($this->public_token))
		{
			$this->public_token = $this->get_token();
		}
		
		// Set river_name_url as river_name sanitized
		$this->bucket_name_url = URL::title($this->bucket_name);

		$bucket = parent::save();

		// Swiftriver Plugin Hook -- execute after saving a bucket
		Swiftriver_Event::run('swiftriver.bucket.save', $bucket);

		return $bucket;
	}

	/**
	 * Override default delete behaviour to delete subscriptions
	 * and bucket droplets before deleting the bucket entry
	 * from the DB
	 */
	public function delete()
	{
		// Delete the bucket's droplets
		DB::delete('buckets_droplets')
		   ->where('bucket_id', '=', $this->id)
		   ->execute();

		// Remove the subscriptions
		DB::delete('bucket_subscriptions')
		    ->where('bucket_id', '=', $this->id)
		    ->execute();

		// Remove collaborators
		DB::delete('bucket_collaborators')
		    ->where('bucket_id', '=', $this->id)
		    ->execute();

		parent::delete();
	}
	
	/**
	 * Get bucket as an array with subscription and collaboration
	 * status populated for $visiting_user
	 *
	 * @param Model_User $user
	 * @param Model_User $visiting_user
	 * @return array
	 *
	 */
	public function get_array($user, $visiting_user) {
		$collaborator = $visiting_user->has('bucket_collaborators', $this);
		return array(
			"id" => (int)$this->id, 
			"name" => $this->bucket_name,
			"type" => 'bucket',
			"url" => $this->get_base_url(),
			"account_id" => (int)$this->account->id,
			"user_id" => (int)$this->account->user->id,
			"account_path" => $this->account->account_path,
			"subscriber_count" => (int)$this->get_subscriber_count(),
			"is_owner" => $this->is_owner($user->id),
			"collaborator" => $collaborator,
			// A collaborator is also a subscriber
			"subscribed" => $visiting_user->has('bucket_subscriptions', $this) OR $collaborator,
			"public" => (bool) $this->bucket_publish
		);
	}

	/**
	 * Gets the base URL of this bucket
	 *
	 * @return string
	 */
	public function get_base_url()
	{
		return URL::site($this->account->account_path.'/bucket/'.$this->bucket_name_url);
	}	
	
	/**
	 * Get the droplets for the specified bucket
	 *
	 * @param int $user_id Logged in user id	
	 * @param int $bucket_id ID of the Bucket
	 * @param int $drop_id
	 * @param int $page Page number - determines the offset for the result set
	 * @param int $max_id Upper limit of the droplet ids to be returned
	 * @param array $filters Set of predicates to filter the desired result
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets($user_id, $bucket_id = NULL,
	    $drop_id = 0, $page = NULL, $max_id = PHP_INT_MAX, $photos = FALSE,
	    $filters = array(), $limit = 50)
	{
		// Check the cache
		$request_hash = hash('sha256', $user_id.
						$bucket_id.
						$drop_id.
						$page.
						$max_id.
						$limit.
						var_export($filters,TRUE).
						($photos ? 1 : 0));
		$cache_key = 'bucket_drops_'.$request_hash;
		
		// If the cache key is available (with default value set to FALSE)
		if ($droplets = Cache::instance()->get($cache_key, FALSE))
		{
			return $droplets;
		}
		
		$droplets = array();
		
		$bucket_orm = ORM::factory('Bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{
			// Build Buckets Query
			$query = DB::select(array('droplets.id', 'id'), array('buckets_droplets.id', 'sort_id'),
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 
								array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i:%S UTC")'),'droplet_date_pub'),
								array('user_scores.score','user_score'), array('links.url','original_url'), 'comment_count')
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
				->join('links', 'LEFT')
			    ->on('links.id', '=', 'droplets.original_url')
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.parent_id', '=', 0);
				
			if ($drop_id)
			{
				// Return a specific drop
				$query->where('droplets.id', '=', $drop_id);
			}
			else
			{
				// Return all drops
				$query->where('buckets_droplets.id', '<=', $max_id);
			}
			
			if ($photos)
			{
				$query->where('droplets.droplet_image', '>', 0);
			}

			// Apply filters
			Model_Droplet::apply_droplets_filter($query, $filters, $user_id, $bucket_orm);
				
			// Order & Pagination offset
			$query->order_by('buckets_droplets.droplet_date_added', 'DESC');
			$query->order_by('droplets.id', 'DESC');
			if ($page)
			{
				$query->limit($limit); 
				$query->offset($limit * ($page - 1));
			}
			else
			{
			    $query->limit($limit);
			}
			
			// Get our droplets as an Array		
			$droplets = $query->execute()->as_array();
			
			// Populate the metadata
			Model_Droplet::populate_metadata($droplets, $bucket_orm->account_id);
		}
		
		// Cache the drops
		if ( ! empty($droplets))
		{
			Cache::instance()->set($cache_key, $droplets);
		}

		return $droplets;
	}

	/**
	 * Get the droplets newer than the specified id
	 *
	 * @param int $user_id Logged in user id	
	 * @param int $id ID of the Bucket
	 * @param int $since_id
	 * @param bool $photos Return on drops with photos if true
	 * @param int $limit Maximum number of drops to return
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets_since_id($user_id, $bucket_id, $since_id, $photos = FALSE, $limit = 100)
	{
		// Check the cache
		$request_hash = hash('sha256', $user_id.
						$bucket_id.
						$since_id.
						($photos ? 1 : 0));
		$cache_key = 'bucket_drops_since_'.$request_hash;
		
		// If the cache key is available (with default value set to FALSE)
		if ($droplets = Cache::instance()->get($cache_key, FALSE))
		{
			return $droplets;
		}
		
		$droplets = array();
		
		$bucket_orm = ORM::factory('Bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{		
			// Build Buckets Query
			$query = DB::select(array('droplets.id', 'id'), array('buckets_droplets.id', 'sort_id'),
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 
								array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i:%S UTC")'),'droplet_date_pub'),
			                    array('user_scores.score','user_score'), array('links.url','original_url'), 'comment_count')
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
				->join('links', 'LEFT')
			    ->on('links.id', '=', 'droplets.original_url')
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.parent_id', '=', 0)
				->where('buckets_droplets.id', '>', $since_id)
				->limit($limit)
				->order_by('buckets_droplets.id', 'ASC');
				
			if ($photos)
			{
				$query->where('droplets.droplet_image', '>', 0);
			}
				
			// Get our droplets as an Array		
			$droplets = $query->execute()->as_array();
			
			// Populate buckets array			
			Model_Droplet::populate_metadata($droplets, $bucket_orm->account_id);
		}
		
		// Cache the drops
		if ( ! empty($droplets))
		{
			Cache::instance()->set($cache_key, $droplets);
		}

		return $droplets;
	}	
	
	/**
	 * Create a bucket from an array
	 *
	 * @param array
	 * @return Model_Bucket
	 */
	public static function create_from_array($bucket_array)
	{		
		$bucket_orm = ORM::factory('Bucket');
		$bucket_orm->account_id = $bucket_array['account_id'];
		$bucket_orm->user_id = $bucket_array['user_id'];
		$bucket_orm->bucket_name = $bucket_array['name'];
		$bucket_orm->save();
		return $bucket_orm;
	}
	
	/**
	 * Gets a buckets's collaborators as an array
	 *
	 * @return array
	 */	
	public function get_collaborators($active_only = FALSE)
	{
		$collaborators = array();		
		foreach ($this->bucket_collaborators->find_all() as $collaborator)
		{
			if ($active_only AND ! (bool) $collaborator->collaborator_active)
				continue;
			
			$collaborators[] = array(
				'id' => (int) $collaborator->user->id, 
				'name' => $collaborator->user->name,
				'email' => $collaborator->user->email,
				'account_path' => $collaborator->user->account->account_path,
				'collaborator_active' => (bool) $collaborator->collaborator_active,
				'read_only' => (bool) $collaborator->read_only,
				'avatar' => Swiftriver_Users::gravatar($collaborator->user->email, 40)
			);
		}
		
		return $collaborators;
	}

	/**
	 * Gets a buckets's discussion
	 *
	 * @param int - signed in user
	 * @return array
	 */	
	public function get_comments($user_id = 0)
	{
		$comments = array();
		$i = 0;	
		foreach ($this->bucket_comments->find_all() as $comment)
		{
			$comments[$i] = array(
				'id' => (int) $comment->id, 
				'name' => $comment->user->name,
				'user_id' => (int) $comment->user->id,
				'comment_content' => $comment->comment_content,
				'date' => $comment->comment_date_add,
				'avatar' => Swiftriver_Users::gravatar($comment->user->email, 40),
				'score' => 0
			);

			// Attach [signed in] users score
			if ($user_id)
			{
				foreach ($comment->bucket_comment_scores
					->where('user_id', '=', $user_id)
					->find_all() as $score)
				{
					$comments[$i]['score'] = $score->score;
				}				
			}

			$i++;
		}

		return $comments;
	}	
	
	/**
	 * Get the max droplet id in a bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return int
	 */
	public function get_max_droplet_id()
	{
		// Build Buckets Query
		$query = DB::select(array(DB::expr('MAX(buckets_droplets.id)'), 'id'))
			->from('buckets_droplets')
			->where('buckets_droplets.bucket_id', '=', $this->id);
			
		return $query->execute()->get('id', 0);
	}
	
	/*
	 * Adds a drop to bucket
	 *
	 * @param Model_Droplet $droplet Droplet instance to be associated with the river
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public function add_drop($drop)
	{
		if ( ! $drop instanceof Model_Droplet OR ! $drop->loaded())
		{
			return FALSE;
		}
		
		// Check if the river exists and if its associated with the current droplet
		if ($this->loaded() AND ! $this->has('droplets', $drop))
		{
			$this->add('droplets', $drop);
			
			$event_data = array('droplet_id' => $drop->id, 'bucket_id' => $this->id);
			Swiftriver_Event::run('swiftriver.bucket.droplet.add', $event_data);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/*
	 * Remove drop from bucket
	 *
	 * @param Model_Droplet $droplet Droplet instance to be associated with the river
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public function remove_drop($drop)
	{
		if ( ! $drop instanceof Model_Droplet OR ! $drop->loaded())
		{
			return FALSE;
		}
		
		// Check if the river exists and if its associated with the current droplet
		if ($this->loaded() AND $this->has('droplets', $drop))
		{
			$this->remove('droplets', $drop);
			
			$event_data = array('droplet_id' => $drop->id, 'bucket_id' => $this->id);
			Swiftriver_Event::run('swiftriver.bucket.droplet.remove', $event_data);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if the given user created the bucket.
	 *
	 * @param int $user_id Database ID of the user	
	 * @return int
	 */
	public function is_creator($user_id)
	{
		return $user_id == $this->user_id;
	}
	
	/**
	 * Checks if the given user owns the bucket, is an account collaborator
	 * or a bucket collaborator.
	 *
	 * @param int $user_id Database ID of the user	
	 * @return int
	 */
	public function is_owner($user_id)
	{
		// Does the user exist?
		$user_orm = ORM::factory('User', $user_id);
		if ( ! $user_orm->loaded())
		{
			return FALSE;
		}
		
		// Is the user_id the owner?
		if ($this->account->user->id == $user_id)
		{
			return TRUE;
		}
		
				
		// Is the user_id a collaborator
		if ($this->bucket_collaborators
				->where('user_id', '=', $user_orm->id)
				->where('read_only', '!=', 1)
				->where('collaborator_active', '=', 1)
				->find()
				->loaded())
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * @param int $user_id Database ID of the user	
	 * @return int
	 */
	public function is_collaborator($user_id)
	{
		// Does the user exist?
		$user_orm = ORM::factory('User', $user_id);
		if ( ! $user_orm->loaded())
		{
			return FALSE;
		}
		
		// Is the user id a bucket collaborator?
		if
		(
			$this->bucket_collaborators
			    ->where('user_id', '=', $user_orm->id)
			    ->find()
			    ->loaded()
		)
		{
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Gets the no. of users subscribed to the current bucket
	 *
	 * @return int
	 */
	public function get_subscriber_count()
	{
		return $this->subscriptions->count_all();
	}

	
	/**
	 * Verifies whether the user with the specified id has subscribed
	 * to this river
	 * @return bool
	 */
	public function is_subscriber($user_id)
	{
		return $this->subscriptions
		    ->where('user_id', '=', $user_id)
		    ->find()
		    ->loaded();
	}


	/**
	 * Given a search term, finds all buckets whose name or url
	 * contains the term
	 *
	 * @param string $search_term  The term to use for matching
	 * @param int $user_id ID of the user initiating the search
	 */
	public static function get_like($search_term, $user_id)
	{
		// Search expression
		$search_expr = DB::expr(__("'%:search_term%'", 
			array(':search_term' => $search_term)));

		// Get the buckets accessible t
		$buckets = array();

		$user_orm = ORM::factory('User', $user_id);
		if ($user_orm->loaded())
		{
			// Get the bucketst the user is collaborating on
			$collaborating = DB::select('bucket_id')
			    ->from('bucket_collaborators')
			    ->where('user_id', '=', $user_id)
			    ->where('collaborator_active', '=', 1)
			    ->execute()
			    ->as_array();

			// Buckets owned by the user in $user_id - includes the buckets
			// the user is collaborating on
			$owner_buckets = DB::select('buckets.id', 'buckets.bucket_name', 
				    'buckets.bucket_name_url', 'accounts.account_path')
			    ->distinct(TRUE)
			    ->from('buckets')
			    ->join('accounts', 'INNER')
			    ->on('buckets.account_id', '=', 'accounts.id')
			    ->where('account_id', '=', $user_orm->account->id)
			    ->where('bucket_name', 'LIKE', $search_expr)
			    ->or_where_open()
			    ->where('buckets.account_id', '=', $user_orm->account->id)
			    ->where('bucket_name_url', 'LIKE', $search_expr);
			
			if (count($collaborating) > 0)
			{
				$owner_buckets->where('buckets.id', 'IN', $collaborating);
			}
			$owner_buckets->or_where_close();


			// All public buckets not owned by the user - excludes all buckets
			// that the user is collaborating on
			$all_buckets = DB::select('buckets.id', 'buckets.bucket_name', 
				    'buckets.bucket_name_url', 'accounts.account_path')
			    ->distinct(TRUE)
			    ->union($owner_buckets)
			    ->from('buckets')
			    ->join('accounts', 'INNER')
			    ->on('buckets.account_id', '=', 'accounts.id')
			    ->where('bucket_publish', '=', 1)
			    ->where('account_id', '<>', $user_orm->account->id)
			    ->where('bucket_name', 'LIKE', $search_expr);

			if (count($collaborating) > 0)
			{
				$all_buckets->where('buckets.id', 'NOT IN', $collaborating);
			}

			// Build the predicates for the OR clause
			$all_buckets->or_where_open()
			    ->where('bucket_name_url', 'LIKE', $search_expr)
			    ->where('account_id', '<>', $user_orm->account->id)
			    ->where('buckets.bucket_publish', '=', 1);
			
			if (count($collaborating) > 0)
			{
				$all_buckets->where('buckets.id', 'NOT IN', $collaborating);
			}

			$all_buckets->or_where_close();

			$buckets = $all_buckets->execute()->as_array();
		}

		return $buckets;
	}
	
	/**
	 * Sets the bucket's access token overwriting the pre-existing one
	 *
	 * @return void
	 */
	public function set_token()
	{
		$this->public_token = $this->get_token();
		$this->save();
	}
	
	/**
	 * @return void
	 */
	private function get_token()
	{
		return md5(uniqid(mt_rand().$this->account->account_path.$this->bucket_name, true));
	}
	
	/**
	 * @return void
	 */	
	public function is_valid_token($token)
	{
		return $token == $this->public_token AND isset($this->public_token);
	}
	
	/**
	 * Return the buckets that have the given IDs
	 *
	 * @param    Array $ids List of bucket ids
	 * @return   Array
	 */
	
	public static function get_buckets($ids)
	{
		$buckets = array();
		
		if ( ! empty($ids))
		{
			$query = ORM::factory('Bucket')
						->where('id', 'IN', $ids);

			// Execute query and return results
			$buckets = $query->find_all()->as_array();
		}
		
		return $buckets;
	}

}
