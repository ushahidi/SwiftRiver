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
				array('max_length', array(':value', 25)),
			),
			'river_public' => array(
				array('in_array', array(':value', array('0', '1')))
			),
			'default_layout' => array(
				array('in_array', array(':value', array('drops', 'list')))
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
		// Get all the channel filter options
		$channel_options = ORM::factory('channel_filter_option')
		    ->where('channel_filter_id', 'IN', 
		        DB::select('id')
		            ->from('channel_filters')
		            ->where('river_id', '=', $this->id)
		        )
		    ->find_all();

		foreach ($channel_options as $option)
		{
			// Execute pre-delete events
			Swiftriver_Event::run('swiftriver.channel.option.pre_delete', $option);
		}

		// Free the result from memory
		unset ($channel_options);

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

		// Proceed with default behaviour
		parent::delete();
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
		$max_id = PHP_INT_MAX, $sort = 'DESC', $filters = array())
	{
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
			                    array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
			                    array(DB::expr('SUM(all_scores.score)'),'scores'), array('user_scores.score','user_score'))
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities', 'INNER')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->join(array('droplet_scores', 'all_scores'), 'LEFT')
			    ->on('all_scores.droplet_id', '=', 'droplets.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('droplets.droplet_processed', '=', 1);
			
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

			// Apply the river filters
			self::_apply_river_filters($query, $filters);

			// Ordering and grouping
			$query->order_by('droplets.droplet_date_pub', $sort)
			    ->group_by('rivers_droplets.id');	   

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
	public static function get_droplets_since_id($user_id, $river_id, $since_id, $filters = array())
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{
			$query = DB::select(array('droplets.id', 'id'), array('rivers_droplets.id', 'sort_id'), 'droplet_title', 
			    'droplet_content', 'droplets.channel','identity_name', 'identity_avatar', 
			    array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
			    array(DB::expr('SUM(all_scores.score)'),'scores'), array('user_scores.score','user_score'))
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities', 'INNER')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->join(array('droplet_scores', 'all_scores'), 'LEFT')
			    ->on('all_scores.droplet_id', '=', 'droplets.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
			    ->where('droplets.droplet_processed', '=', 1)
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('rivers_droplets.id', '>', $since_id);

			// Apply the river filters
			self::_apply_river_filters($query, $filters);

			// Group, order and limit
			$query->order_by('rivers_droplets.id', 'ASC')
			    ->group_by('rivers_droplets.id')
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
	    // Build River Query
		$query = DB::select(array(DB::expr('MAX(rivers_droplets.id)'), 'id'))
		    ->from('droplets')
		    ->join('rivers_droplets', 'INNER')
		    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
		    ->where('rivers_droplets.river_id', '=', $river_id)
		    ->where('droplets.droplet_processed', '=', 1);
		    
		return $query->execute()->get('id', 0);
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
		if ($this->river_collaborators->where('user_id', '=', $user_orm->id)->find()->loaded())
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
			
			if (! isset($channel_config['options'][$channel_option->key]))
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
	public function get_collaborators()
	{
		$collaborators = array();
		
		foreach ($this->river_collaborators->find_all() as $collaborator)
		{
			$collaborators[] = array(
				'id' => $collaborator->user->id, 
				'name' => $collaborator->user->name,
				'account_path' => $collaborator->user->account->account_path,
				'collaborator_active' => $collaborator->collaborator_active,
				'avatar' => Swiftriver_Users::gravatar($collaborator->user->email, 40)
			);
		}
		
		return $collaborators;
	}

	/**
	 * Applies a set of filters to the specified Database_Query_Select object
	 *
	 * @param Database_Query_Select $query Object to which the filtering predicates shall be added
	 * @param array $filters Set of filters to apply
	 */
	private static function _apply_river_filters(& $query, $filters)
	{
		 // Check if the filter are empty
		 if ( ! empty($filters))
		 {
		 	// Places fitler
		 	if (isset($filters['places']) AND Valid::not_empty($filters['places']))
		 	{
		 		$group_places = FALSE;

			 	// Get the places filter
			 	$places = $filters['places'];

			 	// Get the place ids
			 	if (isset($places['ids']) AND Valid::not_empty($places['ids']))
			 	{
			 		$group_places = TRUE;
			 		$query->and_where_open();

			 		$place_ids = $places['ids'];

			 		// Add subquery filter
			 		$query->where('droplets.id', 'IN', 
			 			DB::select('droplet_id')
			 			    ->from('droplets_places')
			 			    ->where('place_id', 'IN', $place_ids)
			 		);
			 	}

			 	// Get the place names
			 	if (isset($places['names']) AND Valid::not_empty($places['names']))
			 	{
			 		// Determine the where clause to use
			 		$where_clause = ($group_places) ? "or_where" : "where";

			 		$place_names = array_map("strtolower", $places['names']);

			 		// Add subquery filter based on place names
			 		$query->$where_clause('droplets.id', 'IN', 
			 			DB::select('droplet_id')
			 			    ->from('droplets_places')
			 			    ->where('place_id', 'IN',
			 			    	 DB::select('id')
			 			    	     ->from('places')
			 			    	     ->where(DB::expr('LOWER(place_name)'), 'IN', $place_names)
			 			    	)
			 		    );
			 	}

			 	// Close the place grouping
			 	if ($group_places)
			 	{
			 		$query->and_where_close();
			 	}
			 }



			 // Tags filter
		 	if (isset($filters['tags']) AND Valid::not_empty($filters['tags']))
		 	{
		 		$group_tags = FALSE;

			 	// Get the places filter
			 	$tags = $filters['tags'];

			 	// Get the place ids
			 	if (isset($tags['ids']) AND Valid::not_empty($tags['ids']))
			 	{
			 		$group_tags = TRUE;
			 		$query->and_where_open();

			 		$tag_ids = $tags['ids'];

			 		// Add subquery filter
			 		$query->where('droplets.id', 'IN', 
			 			DB::select('droplet_id')
			 			    ->from('droplets_tags')
			 			    ->where('tag_id', 'IN', $tag_ids)
			 		);
			 	}

			 	// Get the tag names
			 	if (isset($tags['names']) AND Valid::not_empty($tags['names']))
			 	{
			 		// Determine the where clause to use
			 		$where_clause = ($group_tags) ? "or_where" : "where";

			 		// Convert all the tag names to lower case
			 		$tag_names = array_map("strtolower", $tags['names']);

			 		// Add subquery filter based on place names
			 		$query->$where_clause('droplets.id', 'IN', 
			 			DB::select('droplet_id')
			 			    ->from('droplets_tags')
			 			    ->where('tag_id', 'IN',
			 			    	 DB::select('id')
			 			    	     ->from('tags')
			 			    	     ->where(DB::expr('LOWER(tag)'), 'IN', $tag_names)
			 			    	)
			 		    );
			 	}

			 	if ($group_tags)
			 	{
			 		$query->and_where_close();
			 	}
			 }

			 // Check for channel name filter
			 if (isset($filters['channel']))
			 {
			 	$query->where('droplets.channel', '=', $filters['channel']);
			 }
		 }
		// END filters check
	}

	/**
	 * Gets the number of drops added to the river in the last x days.
	 * The drops are grouped per date
	 *
	 * @param int $interval How far back (in days) to get the activity
	 * @return array
	 */
	public function get_droplet_activity($interval = 30)
	{
		// Get the interval
		$interval = (empty($interval) AND intval($interval) > 0) 
		    ? 30 
		    : intval($interval);

		// Date arithmetic
		$minus_str = sprintf('-%d day', $interval);
		$start_date = date('Y-m-d H:i:s', strtotime($minus_str, time()));

		// Query to fetch the data
		$query = DB::select(array(DB::expr('DATE_FORMAT(d.droplet_date_add, "%Y-%m-%d")'), 'droplet_date'),
			array(DB::expr('COUNT(rd.droplet_id)'), 'droplet_count'))
		    ->from(array('droplets', 'd'))
		    ->join(array('rivers_droplets', 'rd'), 'INNER')
		    ->on('rd.droplet_id', '=', 'd.id')
		    ->join(array('rivers', 'r'), 'INNER')
		    ->on('rd.river_id', '=', 'r.id')
		    ->where('rd.river_id', '=', $this->id)
		    ->where('d.droplet_date_add', '>=', $start_date)
		    ->group_by('droplet_date')
		    ->order_by('droplet_date', 'ASC');

		// Execute the query and return a row of data
		$rows = $query->execute()->as_array();

		$activity = array();
		foreach ($rows as $row)
		{
			$activity[] = $row['droplet_count'];
		}

		// Return
		return $activity;

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
	 * Searches for the specified search term in the river identified by $river_id
	 *
	 * @param int $river_id ID of the river being searched
	 * @param string $search_term Content to search for in the specified river
	 * @param int $user_id ID of the user performing the search
	 * @param int $page PAge number to be used for calculating the offset
	 *
	 * @return array
	 */
	public static function search($search_term, $river_id, $user_id, $page = 1)
	{
		$droplets = array();

		// The page must always be set
		$page = (empty($page)) ? 1 : $page;

		// Check if the specified river exists
		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{
			// Build the SQL "LIKE" expression
			$search_expr = DB::expr(__("'%:search_term%'", array(':search_term' => $search_term)));

			// Build River Query
			$query = DB::select(array('droplets.id', 'id'), array('rivers_droplets.id', 'sort_id'),
			                    'droplet_title', 'droplet_content', 
			                    'droplets.channel','identity_name', 'identity_avatar', 
			                    array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
			                    array(DB::expr('SUM(all_scores.score)'),'scores'), array('user_scores.score','user_score'))
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities', 'INNER')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->join(array('droplet_scores', 'all_scores'), 'LEFT')
			    ->on('all_scores.droplet_id', '=', 'droplets.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('droplets.droplet_processed', '=', 1)
			    ->where('droplets.droplet_raw', 'LIKE', $search_expr)
			    ->or_where('droplets.droplet_title', 'LIKE', $search_expr)
			    ->group_by('rivers_droplets.id')
			    ->order_by('droplets.droplet_date_add', 'DESC')
				->limit(self::DROPLETS_PER_PAGE)
				->offset(self::DROPLETS_PER_PAGE * ($page - 1));

			$droplets =  $query->execute()->as_array();

			// Populate metadata
			Model_Droplet::populate_metadata($droplets, $river_orm->account_id);

		}

		return $droplets;
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
			// Rivers owned by the user in $user_id
			$owner_rivers = DB::select('id', 'river_name', 'river_name_url')
			    ->from('rivers')
			    ->where_open()
			    ->where('account_id', '=', $user_orm->account->id)
			    ->where('river_name', 'LIKE', $search_expr)
			    ->or_where('river_name_url', 'LIKE', $search_expr)
			    ->where_close();


			// All public rivers not owned by the user
			$all_rivers = DB::select('id', 'river_name', 'river_name_url')
			    ->union($owner_rivers)
			    ->from('rivers')
			    ->where('river_public', '=', 1)
			    ->and_where_open()
			    ->where('account_id', '<>', $user_orm->account->id)
			    ->where('river_name', 'LIKE', $search_expr)
			    ->or_where('river_name_url', 'LIKE', $search_expr)
			    ->and_where_close();

			$rivers = $all_rivers->execute()->as_array();
		}

		return $rivers;
	}
}

?>
