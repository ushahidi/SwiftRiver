<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Buckets
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Bucket extends ORM {
	
	/**
	 * No. of droplets to return on each fetch
	 */
	const DROPLETS_PER_PAGE = 20;
	
	/**
	 * One-to-many relationship definitions
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		// A bucket has many droplets
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'buckets_droplets'
			),
		
		// A bucket has many collaborators
		'bucket_collaborators' => array(),

		// A bucket has many subscribers
		'subscriptions' => array(
			'model' => 'user',
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
	 * Rules for the bucket model. 
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'bucket_name' => array(
				array('not_empty'),
				array('max_length', array(':value', 25)),
			),
			'bucket_publish' => array(
				array('in_array', array(':value', array('0', '1')))
			),
		);
	}
	
	
	/**
	 * Overload saving to perform additional functions on the bucket
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time buckets only
		if ($this->loaded() === FALSE)
		{
			// Save the date the bucket was first added
			$this->bucket_date_add = date("Y-m-d H:i:s", time());
		}

		$bucket = parent::save();

		// Swiftriver Plugin Hook -- execute after saving a bucket
		Swiftriver_Event::run('swiftriver.bucket.save', $bucket);

		return $bucket;
	}
	
	/**
	 * Get the droplets for the specified bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets($bucket_id = NULL, $page = NULL, $max_id = PHP_INT_MAX)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$bucket_orm = ORM::factory('bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{
			// Build Buckets Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 
								array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'))
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')				
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.droplet_processed', '=', 1)
				->where('droplets.id', '<=', $max_id)
				->order_by('buckets_droplets.droplet_date_added', 'DESC');
				
			// Order & Pagination offset
			$query->order_by('droplets.id', 'DESC');
			if ($page)
			{
				$query->limit(self::DROPLETS_PER_PAGE); 
				$query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
			}
			
			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			$droplets['total'] = count($droplets['droplets']);

			// Only fetch the semantics when data has been retuned
			if ($droplets['total'] > 0)
			{
				// Get the metadata
				$tags = self::get_droplets_tags($bucket_id, $bucket_orm->account->id, 'DESC', 0, $max_id, $page);
				$places = self::get_droplets_places($bucket_id, $bucket_orm->account->id, 'DESC', 0, $max_id, $page);
				$links = self::get_droplets_links($bucket_id, $bucket_orm->account->id, 'DESC', 0, $max_id, $page);

				// Add the links, tags and places to each of the droplets
				foreach ($droplets['droplets'] as & $droplet)
				{
					// Get the droplet id
					$id = $droplet['id'];

					$droplet['links'] = isset($links[$id]) ? $links[$id] : array();
					$droplet['places'] = isset($places[$id]) ? $places[$id] : array();
					$droplet['tags'] = isset($tags[$id]) ? $tags[$id] : array();
				}
				
				// Populate buckets array			
				Model_Droplet::populate_buckets($droplets['droplets']);
				
				// Populate the discussions array
				Model_Droplet::populate_discussions($droplets['droplets']);
			}
			
		}

		return $droplets;
	}

	/**
	 * Get the droplets newer than the specified id
	 *
	 * @param int $id ID of the Bucket
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_droplets_since_id($bucket_id, $since_id)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$bucket_orm = ORM::factory('bucket', $bucket_id);
		if ($bucket_orm->loaded())
		{		
			// Build Buckets Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
								'droplet_title', 'droplet_content', 
								'droplets.channel','identity_name', 'identity_avatar', 
								array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'))
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')				
				->where('buckets_droplets.bucket_id', '=', $bucket_id)
				->where('droplets.droplet_processed', '=', 1)
				->where('droplets.id', '>', $since_id)
				->order_by('buckets_droplets.droplet_date_added', 'ASC');
				
			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			$droplets['total'] = count($droplets['droplets']);

			// Only fetch the semantics when data has been retuned
			if ($droplets['total'] > 0)
			{
				// Get the metadata
				$tags = self::get_droplets_tags($bucket_id, $bucket_orm->account->id, 'ASC', $since_id);
				$places = self::get_droplets_places($bucket_id, $bucket_orm->account->id, 'ASC', $since_id);
				$links = self::get_droplets_links($bucket_id, $bucket_orm->account->id, 'ASC', $since_id);

				// Add the links, tags and places to each of the droplets
				foreach ($droplets['droplets'] as & $droplet)
				{
					// Get the droplet id
					$id = $droplet['id'];

					$droplet['links'] = isset($links[$id]) ? $links[$id] : array();
					$droplet['places'] = isset($places[$id]) ? $places[$id] : array();
					$droplet['tags'] = isset($tags[$id]) ? $tags[$id] : array();
				}
				
				// Populate buckets array			
				Model_Droplet::populate_buckets($droplets['droplets']);
				
				// Populate the discussions array
				Model_Droplet::populate_discussions($droplets['droplets']);
			}
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
		$bucket_orm = ORM::factory('bucket');
		$bucket_orm->account_id = $bucket_array['account_id'];
		$bucket_orm->user_id = $bucket_array['user_id'];
		$bucket_orm->bucket_name = $bucket_array['bucket_name'];
		$bucket_orm->save();
		return $bucket_orm;
	}
	
	/**
	 * Gets a buckets's collaborators as an array
	 *
	 * @return array
	 */	
	public function get_collaborators()
	{
		$collaborators = array();		
		foreach ($this->bucket_collaborators->find_all() as $collaborator)
		{
			$collaborators[] = array('id' => $collaborator->user->id, 
			                         'name' => $collaborator->user->name,
			                         'account_path' => $collaborator->user->account->account_path,
			                         'collaborator_active' => $collaborator->collaborator_active
			);
		}
		
		return $collaborators;
	}
	
	/**
	 * Get the max droplet id in a bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return int
	 */
	public static function get_max_droplet_id($bucket_id = NULL)
	{
		// Build Buckets Query
		$query = DB::select(array(DB::expr('MAX(droplets.id)'), 'id'))
			->from('droplets')
			->join('buckets_droplets', 'INNER')
			->on('buckets_droplets.droplet_id', '=', 'droplets.id')
			->join('identities')
			->on('droplets.identity_id', '=', 'identities.id')				
			->where('buckets_droplets.bucket_id', '=', $bucket_id)
			->where('droplets.droplet_processed', '=', 1);
			
		return $query->execute()->get('id', 0);		
	}
	
	/*
	 * Adds a droplet to bucket
	 *
	 * @param int $bucket_id Database ID of the bucket
	 * @param Model_Droplet $droplet Droplet instance to be associated with the river
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public static function add_droplet($bucket_id, $droplet)
	{
		if ( ! $droplet instanceof Model_Droplet)
		{
			// Log the error
			Kohana::$log->add(Log::ERROR, "Expected Model_Droplet in parameter droplet. Found :type instead.", 
				array(":type" => gettype($droplet)));
			return FALSE;
		}
		
		// Get ORM reference for the river
		$bucket = ORM::factory('bucket', $bucket_id);
		
		// Check if the river exists and if its associated with the current droplet
		if ($bucket->loaded() AND ! $bucket->has('droplets', $droplet))
		{
			$bucket->add('droplets', $droplet);
			return TRUE;
		}
		
		return FALSE;
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
		$user_orm = ORM::factory('user', $user_id);
		if ( ! $user_orm->loaded())
		{
			return FALSE;
		}
		
		// Is the user_id the owner?
		if ($this->account->user->id == $user_id)
		{
			return TRUE;
		}
		
		// Is the user id an account collaborator?
		if ($this->account->account_collaborators->where('user_id', '=', $user_orm->id)->find()->loaded())
		{
			return TRUE;
		}
		
				
		// Is the user_id a collaborator
		if ($this->bucket_collaborators->where('user_id', '=', $user_orm->id)->find()->loaded())
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
	 * Gets the tags associated with the droplets in the specified bucket
	 * for the specified account
	 *
	 * @param int $bucket_id ID of the bucket
	 * @param int $account_id ID of the account
	 * @return array
	 */
	public static function get_droplets_tags($bucket_id, $account_id, $sort = 'DESC', $since_id = 0, $max_id = 0, $page = 1)
	{
		// Query to fetch the data
		$sql = "SELECT DISTINCT d.id, dt.tag_id, t.tag "
		    . "FROM droplets d "
		    . "INNER JOIN droplets_tags dt ON (dt.droplet_id = d.id) "
		    . "INNER JOIN tags t ON (dt.tag_id = t.id) "
		    . "INNER JOIN buckets_droplets bd ON (bd.`droplet_id` = d.id) AND (d.droplet_processed = 1) "
		    . "LEFT JOIN account_droplet_tags adt ON (adt.droplet_id = bd.droplet_id) "
		    . "AND (adt.tag_id = t.id) AND (adt.account_id = ".$account_id.") "
		    . "WHERE bd.bucket_id = ".$bucket_id." ";
		
		// Check for since id
		$sql .= ($since_id > 0) ? "AND d.id > ".$since_id." " : "";
		$limit_clause = " LIMIT 20";

		// Check for max id
		$sql .= ($max_id > 0) ? "AND d.id < ".$max_id." " : "";

		// Modify the "LIMIT" clause - only when max_id has been specified
		$limit_clause .= ($max_id > 0 AND $page > 1) 
		    ? ", ".self::DROPLETS_PER_PAGE * ($page - 1) 
		    : "";

		// Add te order by clause
		$sql .= "ORDER BY bd.droplet_date_added ".$sort;
		$sql .= $limit_clause;

		$query = DB::query(Database::SELECT, $sql);

		$result = $query->execute()->as_array();


		// Group the row data by droplet
		$tags = array();
		foreach ($result as $k => $data)
		{
			if ( ! isset($tags[$data['id']]))
			{
				$tags[$data['id']] = array();
			}

			$tags[$data['id']][] = array(
				'id' => $data['tag_id'],
				'tag' => $data['tag']
			);
		}

		return $tags;
	}

	/**
	 * Gets the places associated with the droplets in the speciffied bucket
	 * for the specified account
	 *
	 * @param int $bucket ID of the Bucket
	 * @param int $account_id ID of the account
	 * @return array
	 */
	public static function get_droplets_places($bucket_id, $account_id, $sort = 'DESC', $since_id = 0, $max_id = 0, $page = 1)
	{
		// Query to fetch the data
		$sql = "SELECT DISTINCT d.id, dp.place_id, p.place_name "
		    . "FROM droplets d "
		    . "INNER JOIN droplets_places dp ON (dp.droplet_id = d.id) "
		    . "INNER JOIN places p ON (dp.place_id = p.id) "
		    . "INNER JOIN buckets_droplets bd ON (bd.`droplet_id` = d.id) AND (d.droplet_processed = 1) "
		    . "LEFT JOIN account_droplet_places adp ON (adp.droplet_id = bd.droplet_id) "
		    . "AND (adp.place_id = p.id) AND (adp.account_id = ".$account_id.") "
		    . "WHERE bd.bucket_id = ".$bucket_id." ";
		
		// Check for since id
		$sql .= ($since_id > 0) ? "AND d.id > ".$since_id." " : "";
		$limit_clause = " LIMIT 20";

		// Check for max id
		$sql .= ($max_id > 0) ? "AND d.id < ".$max_id." " : "";

		// Modify the "LIMIT" clause - only when max_id has been specified
		$limit_clause .= ($max_id > 0 AND $page > 1) 
		    ? ", ".self::DROPLETS_PER_PAGE * ($page - 1) 
		    : "";

		// Add te order by clause
		$sql .= "ORDER BY bd.droplet_date_added ".$sort;
		$sql .= $limit_clause;

		$query = DB::query(Database::SELECT, $sql);

		$result =  $query->execute()->as_array();

		$places = array();
		foreach ($result as $k => $data)
		{
			if ( ! isset($places[$data['id']]))
			{
				$places[$data['id']] = array();
			}

			$places[$data['id']][] = array(
				'id' => $data['place_id'],
				'place_name' => $data['place_name']
			);
		}
		return $places;
	}

	/**
	 * Gets the links associated with the droplets in the specified bucket
	 * for the specified account
	 *
	 * @param int $bucket_id ID of the Bucket
	 * @param int $account_id ID of the account
	 * @return array
	 */
	public static function get_droplets_links($bucket_id, $account_id, $sort = 'DESC', $since_id = 0, $max_id = 0, $page = 1)
	{
		// Query to fetch the data
		$sql = "SELECT DISTINCT d.id, dl.link_id, l.link_full "
		    . "FROM droplets d "
		    . "INNER JOIN droplets_links dl ON (dl.droplet_id = d.id) "
		    . "INNER JOIN links l ON (dl.link_id = l.id) "
		    . "INNER JOIN buckets_droplets bd ON (bd.droplet_id = d.id) AND (d.droplet_processed = 1) "
		    . "LEFT JOIN account_droplet_links adl ON (adl.droplet_id = bd.droplet_id) "
		    . "AND (adl.link_id = l.id) AND (adl.account_id = ".$account_id.") "
		    . "WHERE bd.bucket_id = ".$bucket_id." ";
		
		// Check for since id
		$sql .= ($since_id > 0) ? "AND d.id > ".$since_id." " : "";
		$limit_clause = " LIMIT 20";

		// Check for max id
		$sql .= ($max_id > 0) ? "AND d.id < ".$max_id." " : "";

		// Modify the "LIMIT" clause - only when max_id has been specified
		$limit_clause .= ($max_id > 0 AND $page > 1) 
		    ? ", ".self::DROPLETS_PER_PAGE * ($page - 1) 
		    : "";

		// Add the order by clause
		$sql .= "ORDER BY bd.droplet_date_added ".$sort;
		$sql .= $limit_clause;

		$query = DB::query(Database::SELECT, $sql);

		$result =  $query->execute()->as_array();

		// Group the row data per droplet
		$links = array();
		foreach ($result as $k => $data)
		{
			if ( ! isset($links[$data['id']]))
			{
				$links[$data['id']] = array();
			}

			$links[$data['id']][] = array(
				'id' => $data['link_id'],
				'link_full' => $data['link_full']
			);
		}

		return $links;
	}
}
