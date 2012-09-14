<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Rivers
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_River extends ORM {
	
	/**
	 * Number of droplets to show per page
	 */
	const DROPLETS_PER_PAGE = 50;
	
	/**
	 * A river has many channel_filters
	 * A river has and belongs to many droplets
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'channel_filters' => array(),
		'river_collaborators' => array(),

		// A river has many droplets
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'rivers_droplets'
			),

		// A river has many subscribers
		'subscriptions' => array(
			'model' => 'user',
			'through' => 'river_subscriptions',
			'far_key' => 'user_id'
			)		
		);
	
	/**
	 * An account belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('account' => array());
	
	
	/**
	 * Rules for the bucket model. 
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'river_name' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
			'river_public' => array(
				array('in_array', array(':value', array('0', '1')))
			),
			'default_layout' => array(
				array('in_array', array(':value', array('drops', 'list', 'photos')))
			)
		);
	}
	

	/**
	 * Override saving to perform additional functions on the river
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date this river was first added
			$this->river_date_add = date("Y-m-d H:i:s", time());

			// Set the expiry date
			$river_lifetime = Model_Setting::get_setting('river_lifetime');
			$expiry_date = strtotime(sprintf("+%s day", $river_lifetime), strtotime($this->river_date_add));
			$this->river_date_expiry = date("Y-m-d H:i:s", $expiry_date);
		}
		
		if ( ! isset($this->public_token))
		{
			$this->public_token = $this->get_token();
		}
		
		// Set river_name_url to the sanitized version of river_name sanitized
		$this->river_name_url = URL::title($this->river_name);

		$river = parent::save();

		// Swiftriver Plugin Hook -- execute after saving a river
		Swiftriver_Event::run('swiftriver.river.save', $river);

		return $river;
	}

	/**
	 * Override the default behaviour to perform
	 * extra tasks before proceeding with the 
	 * deleting the river entry from the DB
	 */
	public function delete()
	{
		// Remove the river's channel options from the crawling schedule
		$this->_toggle_channel_option_status(FALSE);

		// Delete the channel filter options
		DB::delete('channel_filter_options')
		    ->where('channel_filter_id', 'IN', 
		        DB::select('id')
		            ->from('channel_filters')
		            ->where('river_id', '=', $this->id)
		        )
		    ->execute();

		// Delete the channel options
		DB::delete('channel_filters')
		    ->where('river_id', '=', $this->id)
		    ->execute();

		// Delete associated droplets
		DB::delete('rivers_droplets')
		    ->where('river_id', '=', $this->id)
		    ->execute();

		// Delete the subscriptions
		DB::delete('river_subscriptions')
		    ->where('river_id', '=', $this->id)
		    ->execute();

		// Delete collaborators
		DB::delete('river_collaborators')
		    ->where('river_id', '=', $this->id)
		    ->execute();

		// Proceed with default behaviour
		parent::delete();
	}
	
	
	/**
	 * Get bucket as an array with subscription and collaboration
	 * status populated for $visiting_user
	 *
	 */
	public function get_array($user, $visiting_user) {
		$collaborator = $visiting_user->has('river_collaborators', $this);
		return array(
			"id" => $this->id, 
			"name" => $this->river_name,
			"url" => URL::site().$this->account->account_path.'/river/'.$this->river_name_url,
			"account_id" => $this->account->id,
			"user_id" => $this->account->user->id,
			"account_path" => $this->account->account_path,
			"subscriber_count" => $this->get_subscriber_count(),
			"is_owner" => $this->is_owner($user->id),
			"collaborator" => $collaborator,
			// A collaborator is also a subscriber
			"subscribed" => $visiting_user->has('river_subscriptions', $this) OR $collaborator,
			"public" => (bool) $this->river_public
		);
	}
	
	/**
	* Creat a river
	*
	* @return Model_River
	*/
	public static function create_new($river_name, $public, $account, $river_name_url = NULL)
	{
		$river = ORM::factory('river');
		$river->river_name = $river_name;
		if ($river_name_url)
		{
			$river->river_name_url = $river_name_url;
		}
		$river->river_public = $public;
		$river->account_id = $account->id;
		$river->save();
		
		// Force refresh of cached rivers
        Cache::instance()->delete('user_rivers_'.$account->user->id);

		return $river;
	}
	
	
	/**
	 * Gets the base URL of this river
	 *
	 * @return string
	 */
	public function get_base_url()
	{
		return URL::site().$this->account->account_path.'/river/'.$this->river_name_url;
	}
	
	
	/**
	 * Adds a droplet to river
	 *
	 * @param int $river_id Database ID of the river
	 * @param Model_Droplet $droplet Droplet instance to be associated with the river
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public static function add_droplet($river_id, $droplet)
	{
		if ( ! $droplet instanceof Model_Droplet)
		{
			// Log the error
			Kohana::$log->add(Log::ERROR, "Expected Model_Droplet in parameter droplet. Found :type instead.", 
			    array(":type" => gettype($droplet)));
			return FALSE;
		}
		
		// Get ORM reference for the river
		$river = ORM::factory('river', $river_id);
		
		// Check if the river exists and if its associated with the current droplet
		if ($river->loaded() AND ! $river->has('droplets', $droplet))
		{
			$river->add('droplets', $droplet);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if the specified river id exists in the database
	 *
	 * @param int $river_id Database ID of the river to lookup
	 * @return bool
	 */
	public static function is_valid_river_id($river_id)
	{
		return (bool) ORM::factory('river', $river_id)->loaded();
	}
	
	/**
	 * Gets the droplets for the specified river
	 *
	 * @param int $user_id Logged in user id
	 * @param int $river_id Database ID of the river
	 * @param int $page Offset to use for fetching the droplets
	 * @param string $sort Sorting order
	 * @return array
	 */
	public static function get_droplets($user_id, $river_id, $drop_id = 0, $page = 1, 
		$max_id = PHP_INT_MAX, $sort = 'DESC', $filters = array(), $photos = FALSE)
	{
		// Check the cache
		$request_hash = hash('sha256', $user_id.
						$river_id.
						$drop_id.
						$page.
						$max_id.
						$sort.
						var_export($filters,TRUE).
						($photos ? 1 : 0));
		$cache_key = 'river_drops_'.$request_hash;
		
		// If the cache key is available (with default value set to FALSE)
		if ($droplets = Cache::instance()->get($cache_key, FALSE))
		{
			return $droplets;
		}
		
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);

		// Sanity check for the sorting method
		$sort = empty($sort) ? 'DESC' : $sort;

		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{						
			// Build River Query
			$query = DB::select(array('droplets.id', 'id'), array('rivers_droplets.id', 'sort_id'),
			                    'droplet_title', 'droplet_content', 
			                    'droplets.channel','identity_name', 'identity_avatar', 
			                    array(DB::expr('DATE_FORMAT(droplets.droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
			                    array('user_scores.score','user_score'), array('links.url','original_url'), 'comment_count')
			    ->from('rivers_droplets')
			    ->join('droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities', 'INNER')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->where('rivers_droplets.droplet_date_pub', '>', '0000-00-00 00:00:00')
			    ->where('rivers_droplets.river_id', '=', $river_id);
			
			if ($drop_id)
			{
				// Return a specific drop
				$query->where('droplets.id', '=', $drop_id);
			}
			else
			{
				// Return all drops
				$query->where('rivers_droplets.id', '<=', $max_id);
			}
			
			if ($photos)
			{
				$query->where('droplets.droplet_image', '>', 0);
			}

			// Apply the river filters
			Model_Droplet::apply_droplets_filter($query, $filters, $user_id, $river_orm);
			
			// Left joins
			$query->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
				->join('links', 'LEFT')
			    ->on('links.id', '=', 'droplets.original_url');

			// Ordering and grouping
			$query->order_by('rivers_droplets.droplet_date_pub', $sort);	   

			// Pagination offset
			if ($page > 0)
			{
				$query->limit(self::DROPLETS_PER_PAGE);	
				$query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
			}

			// Get our droplets as an Array
			$droplets['droplets'] = $query->execute()->as_array();

			// Encode content and title as utf8 in case they arent
			foreach ($droplets['droplets'] as & $droplet) 
			{
				Model_Droplet::utf8_encode($droplet);
			}
			
			// Populate the metadata arrays
			Model_Droplet::populate_metadata($droplets['droplets'], $river_orm->account_id);

		}
		
		// Cache the drops
		if ( ! empty($droplets['droplets']))
		{
			Cache::instance()->set($cache_key, $droplets);
		}

		return $droplets;
	}
	
	/**
	 * Gets droplets whose database id is above the specified minimum
	 *
	 * @param int $user_id Logged in user id	
	 * @param int $river_id Database ID of the river
	 * @param int $since_id Lower limit of the droplet id
	 * @return array
	 */
	public static function get_droplets_since_id($user_id, $river_id, $since_id, $filters = array(), $photos = FALSE)
	{
		// Check the cache
		$request_hash = hash('sha256', $user_id.
						$river_id.
						$since_id.
						var_export($filters,TRUE).
						($photos ? 1 : 0));
		$cache_key = 'river_drops_since_'.$request_hash;
		
		// If the cache key is available (with default value set to FALSE)
		if ($droplets = Cache::instance()->get($cache_key, FALSE))
		{
			return $droplets;
		}
		
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{
			$query = DB::select(array('droplets.id', 'id'), array('rivers_droplets.id', 'sort_id'), 'droplet_title', 
			    'droplet_content', 'droplets.channel','identity_name', 'identity_avatar', 
			    array(DB::expr('DATE_FORMAT(droplets.droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
			    array('user_scores.score','user_score'), array('links.url','original_url'), 'comment_count')
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities', 'INNER')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('rivers_droplets.id', '>', $since_id);
			
			if ($photos)
			{
				$query->where('droplets.droplet_image', '>', 0);
			}
			
			// Apply the river filters
			Model_Droplet::apply_droplets_filter($query, $filters, $user_id, $river_orm);
			
			// Left join for user scores
			$query->join(array('droplet_scores', 'user_scores'), 'LEFT')
				->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
				->join('links', 'LEFT')
			    ->on('links.id', '=', 'droplets.original_url');

			// Group, order and limit
			$query->order_by('rivers_droplets.id', 'ASC')
			    ->limit(self::DROPLETS_PER_PAGE)
			    ->offset(0);
			
			$droplets['droplets'] = $query->execute()->as_array();
			
			// Encode content and title as utf8 in case they arent
			foreach ($droplets['droplets'] as & $droplet) 
			{
				Model_Droplet::utf8_encode($droplet);
			}

			// Populate the metadata
			Model_Droplet::populate_metadata($droplets['droplets'], $river_orm->account_id);
		}
		
		
		// Cache the drops
		if ( ! empty($droplets['droplets']))
		{
			Cache::instance()->set($cache_key, $droplets);
		}
				
		return $droplets;
	}
	
	/**
	 * Gets the max droplet id in a river
	 *
	 * @param int $river_id Database ID of the river
	 * @return int
	 */
	public static function get_max_droplet_id($river_id)
	{
		$max_droplet_id = 0;
		if ( ! ($max_droplet_id = Cache::instance()->get('river_max_id_'.$river_id, FALSE)))
		{
			// Build River Query
			$query = DB::select(array('max_drop_id', 'id'))
			    ->from('rivers')
			    ->where('id', '=', $river_id);
			
			$max_droplet_id = $query->execute()->get('id', 0);
			
			// Cache for 90s
			Cache::instance()->set('river_max_id_'.$river_id, $max_droplet_id, 90);
		}
	    
		    
		return $max_droplet_id;
	}
	
	/**
	 * Checks if the given user created the river.
	 *
	 * @param int $user_id Database ID of the user	
	 * @return int
	 */
	public function is_creator($user_id)
	{
		return $user_id == $this->account->user_id;
	}
	
	/**
	 * Checks if the given user owns the river, is an account collaborator
	 * or a river collaborator.
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
		
		// Public rivers are owned by everyone
		if ( $this->account->user->username == 'public')
		{
			return TRUE;
		}
		
		// Does the user own the river?
		if ($this->account->user_id == $user_id)
		{
			return TRUE;
		}
		
		// Is the user id a river collaborator?
		if
		(
			$this->river_collaborators
			    ->where('user_id', '=', $user_orm->id)
			    ->where('read_only', '!=', 1)
			    ->find()
			    ->loaded()
		)
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
		$user_orm = ORM::factory('user', $user_id);
		if ( ! $user_orm->loaded())
		{
			return FALSE;
		}
		
		// Is the user id a river collaborator?
		if
		(
			$this->river_collaborators
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
	 * Gets the no. of users subscribed to the current river
	 *
 	 * @return int
 	 */
	public function get_subscriber_count()
	{
		return $this->subscriptions->count_all();
	}
	
	/**
	 * Get a list of channels in this river
	 *
	 * @return array
	 */
	public function get_channels($filter_active = FALSE)
	{
		// Get the channel filters
		$query = ORM::factory('channel_filter')
			->select('id', 'channel', 'filter_enabled')
			->where('river_id', '=', $this->id);
			
		if ($filter_active)
		{
			$query->where('filter_enabled', '=', TRUE);
		}
		
		$channels_orm = $query->find_all();
		
		$channels_array = array();
		foreach ($channels_orm as $channel_orm)
		{
			$channel_config = Swiftriver_Plugins::get_channel_config($channel_orm->channel);
			
			if ( ! $channel_config)
				continue;
				
			$channels_array[] = array(
				'id' => $channel_orm->id,
				'channel' => $channel_orm->channel,
				'name' => $channel_config['name'],
				'enabled' => (bool) $channel_orm->filter_enabled,
				'options' => $this->get_channel_options($channel_orm)
			);
		}
		
		return $channels_array;
	}
	
	/**
	 * Get a river's channel options with configuration added
	 *
	 * @param Model_Channel_Filter $channel_orm 
	 * @param int $id Id of channel filter to be returned
	 * @return array
	 */
	public function get_channel_options($channel_orm, $id = NULL)
	{
		$options = array();
		
		$channel_config = Swiftriver_Plugins::get_channel_config($channel_orm->channel);
		
		$query = $channel_orm->channel_filter_options;
		
		if ($id)
		{
			$query->where('id', '=', $id);
		}
		
		foreach ($query->find_all() as $channel_option)
		{
			$option = json_decode($channel_option->value);
			$option->id = $channel_option->id;
			$option->key = $channel_option->key;
			
			if ( ! isset($channel_config['options'][$channel_option->key]))
				continue;
			$options[] = $option;
		}
		
		return $options;
	}
	
	/**
	 * Get a specific channel
	 *
	 * @return array
	 */
	public function get_channel($channelKey)
	{
		$channel = $this->channel_filters
		                ->where('channel', '=', $channelKey)
		                ->find();
		
		if ( ! $channel->loaded())
		{
			$channel = new Model_Channel_Filter();
			$channel->channel = $channelKey;
			$channel->river_id = $this->id;
			$channel->filter_enabled = TRUE;
			$channel->filter_date_add = gmdate('Y-m-d H:i:s');
			$channel->save();
		}
		
		return $channel;
	}
	
	/**
	 * Get a specific channel
	 *
	 * @return Model_Channel_Filter
	 */
	public function get_channel_by_id($id)
	{
		$channel = $this->channel_filters
		                ->where('id', '=', $id)
		                ->find();
		
		if ($channel->loaded())
		{
			return $channel;
		}
		
		return FALSE;
	}
	
	
	/**
	 * Gets a river's collaborators as an array
	 *
	 * @return array
	 */	
	public function get_collaborators($active_only = FALSE)
	{
		$collaborators = array();
		
		foreach ($this->river_collaborators->find_all() as $collaborator)
		{
			if ($active_only AND ! (bool) $collaborator->collaborator_active)
				continue;
				
			$collaborators[] = array(
				'id' => $collaborator->user->id, 
				'name' => $collaborator->user->name,
				'account_path' => $collaborator->user->account->account_path,
				'collaborator_active' => $collaborator->collaborator_active,
				'read_only' => (bool) $collaborator->read_only,
				'avatar' => Swiftriver_Users::gravatar($collaborator->user->email, 40)
			);
		}
		
		return $collaborators;
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
	 * Given a search term, finds all rivers whose name or url
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

		$rivers = array();

		$user_orm = ORM::factory('user', $user_id);
		if ($user_orm->loaded())
		{
			// Get the rivers collaborating on
			$collaborating = DB::select('river_id')
			    ->from('river_collaborators')
			    ->where('user_id', '=', $user_id)
			    ->where('collaborator_active', '=', 1)
			    ->execute()
			    ->as_array();

			// Rivers owned by the user in $user_id - includes the rivers the
			// user is collaborating on
			$owner_rivers = DB::select('rivers.id', 'rivers.river_name', 
				'rivers.river_name_url', 'accounts.account_path')
			    ->distinct(TRUE)
			    ->from('rivers')
			    ->join('accounts', 'INNER')
			    ->on('rivers.account_id', '=', 'accounts.id')
			    ->where('account_id', '=', $user_orm->account->id)
			    ->where('river_name', 'LIKE', $search_expr)
			    ->or_where_open()
			    ->where('rivers.account_id', '=', $user_orm->account->id)
			    ->where('river_name_url', 'LIKE', $search_expr);
			
			if (count($collaborating) > 0)
			{
				$owner_rivers->where('rivers.id', 'IN', $collaborating);
			}
			$owner_rivers->or_where_close();


			// All public rivers not owned by the user - excludes all the
			// rivers that the user is collaborating on
			$all_rivers = DB::select('rivers.id', 'rivers.river_name', 
				'rivers.river_name_url', 'accounts.account_path')
			    ->distinct(TRUE)
			    ->union($owner_rivers)
			    ->from('rivers')
			    ->join('accounts', 'INNER')
			    ->on('rivers.account_id', '=', 'accounts.id')
			    ->where('account_id', '<>', $user_orm->account->id)
			    ->where('river_public', '=', 1)
			    ->where('river_name', 'LIKE', $search_expr);

			if (count($collaborating) > 0)
			{
				$all_rivers->where('rivers.id', 'NOT IN', $collaborating);
			}

			// Build the predicates for the OR clause
			$all_rivers->or_where_open()
			    ->where('river_name_url', 'LIKE', $search_expr)
			    ->where('account_id', '<>', $user_orm->account->id)
			    ->where('rivers.river_public', '=', 1);

			if (count($collaborating) > 0)
			{
				$all_rivers->where('rivers.id', 'NOT IN', $collaborating);
			}

			$all_rivers->or_where_close();

			$rivers = $all_rivers->execute()->as_array();
		}

		return $rivers;
	}

	/**
	 * Checks if the current river has expired. If the river has expired
	 * and the expiry flag has not been set, all the channel filters for
	 * the river are disabled and the associated channel filter options
	 * purged from the crawling schedule.
	 *
	 * @param bool $is_owner Whether the user accessing the river is an owner
	 * @return bool
	 */
	public function is_expired($is_owner)
	{
		if ($this->river_expired)
			return TRUE;

		// River hasn't yet expired, extend the lifetime
		if ($is_owner AND $this->expiry_candidate)
		{
			$notice_expiry_period = Model_Setting::get_setting('river_expiry_notice_period');

			// Extend the expiry date of the river if accessed within the
			// period allocated for extending its lifetime
			if (intval($notice_expiry_period) >= $this->get_days_to_expiry())
			{
				$this->extend_lifetime(FALSE);
			}
		}		

		return FALSE;
	}

	/**
	 * Gets the number of days remaining before a river expires.
	 *
	 * @param $current_date DateTime Current date
	 * @return int
	 */
	public function get_days_to_expiry($current_date = NULL)
	{
		if ($this->river_expired)
			return 0;

		// Has the current date been specified?
		if ( ! $current_date instanceof DateTime)
		{
			Kohana::$log->add(Log::INFO, 
			    __("Current date not specified. Setting to current date"));
			$current_date = new DateTime(date('Y-m-d H:i:s', time()));
		}
		$expiry_date = new DateTime($this->river_date_expiry);

		if ($current_date > $expiry_date)
			return 0;

		$interval = $expiry_date->diff($current_date);

		return $interval->d;
	}

	/**
	 * Extends the lifetime of the river by pushing forward the expiry date 
	 * of the river - by the no. of days that a river is active. This SHOULD only 
	 * be triggered by an owner of the river. The extension counter is incremented
	 * each time the expiry date is incremented
	 *
	 * @param bool $reactivate_channels When TRUE, reactivates the channels for the 
	 * current river so that the crawlers can resume fetching content from them
	 */
	public function extend_lifetime($reactivate_channels = TRUE)
	{
		$lifetime = Model_Setting::get_setting('river_active_duration');
		
		$expiry_start_date = strtotime($this->river_date_expiry);
		if ($this->get_days_to_expiry() == 0)
		{
			$expiry_start_date = time();
		}
		$expiry_date = strtotime(sprintf("+%s day", $lifetime), $expiry_start_date);

		$this->river_expired = 0;
		$this->expiry_extension_token = NULL;
		$this->river_date_expiry = date("Y-m-d H:i:s", $expiry_date);
		$this->extension_count += 1;
		$this->expiry_candidate = 0;
		parent::save();

		if ($reactivate_channels)
		{
			// Disable the channel filters for the river
			DB::update('channel_filters')
			    ->set(array('filter_enabled' => 1))
			    ->where('river_id', '=', $this->id)
			    ->execute();

			$this->_toggle_channel_option_status(TRUE);
		}
	}

	/**
	 * Toggles the status of the channel options for the current river
	 *
	 * @param bool $activate When TRUE, activates the channel options for
	 * the river. When FALSE, removes the channel options from the crawling
	 * schedule
	 */
	private function _toggle_channel_option_status($activate)
	{
		// Get all the channel filter options
		$channel_options = ORM::factory('channel_filter_option')
		    ->where('channel_filter_id', 'IN', 
		        DB::select('id')
		            ->from('channel_filters')
		            ->where('river_id', '=', $this->id)
		        )
		    ->find_all();

		// Status - determines the action to be taken on the channel options
		$new_status = $activate ? "activate" : "deactivate";

		foreach ($channel_options as $option)
		{
			// Run deactivation events for each option
			Swiftriver_Event::run('swiftriver.channel.option.'.$new_status, $option);
		}

		// Garbage collection
		unset ($channel_options);
	}

	/**
	 * Gets the owners of the river
	 * @return array
	 */
	public function get_owners()
	{
		return $this->river_collaborators
		           ->where('collaborator_active', '=', 1);
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
		return md5(uniqid(mt_rand().$this->account->account_path.$this->river_name, true));
	}
	
	/**
	 * @return void
	 */	
	public function is_valid_token($token)
	{
		return $token == $this->public_token AND isset($this->public_token);
	}

}

?>
