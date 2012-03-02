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
	const DROPLETS_PER_PAGE = 20;
	
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
			'river_name_url' => array(
				array('not_empty'),
				array('max_length', array(':value', 25)),
			),
			'river_public' => array(
				array('in_array', array(':value', array('0', '1')))
			),
		);
	}
	

	/**
	 * Overload saving to perform additional functions on the river
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date this river was first added
			$this->river_date_add = date("Y-m-d H:i:s", time());
		}
		
		// Set river_name_url as river_name sanitized
		$this->river_name_url = preg_replace('/[^\w]/', '-', strtolower(trim($this->river_name)));

		$river = parent::save();

		// Swiftriver Plugin Hook -- execute after saving a river
		Swiftriver_Event::run('swiftriver.river.save', $river);

		return $river;
	}
	
	/**
	 * Gets the list of the channel filters for the current river and returns the
	 * result as an array
	 *
	 * @return array
	 */
	public function get_channel_filters()
	{
		// Get the channel filters
		$results = ORM::factory('channel_filter')
			->select('id', 'channel', 'filter_enabled')
			->where('river_id', '=', $this->id)
			->find_all();
		
		$filters = array();
		foreach ($results as $result)
		{
			$filters[$result->channel] = array(
				'id' => $result->id,
				'enabled' => $result->filter_enabled
				);
		}
		
		return $filters;
	}

	/**
	 * Gets a list of the available channels, their data and configuration options
	 *
	 * @return array
	 */
	public function get_channel_filter_data()
	{
		$filter_data = array();

		// Get the channels for this river
		$river_channels = $this->get_channel_filters();

		// Get the the list channels that are plugins
		$channel_plugins = Swiftriver_Plugins::channels();

		foreach (array_keys($channel_plugins) as $channel)
		{
			$filter_data_entry = array();
			$option_data = array();

			if (array_key_exists($channel, $river_channels))
			{
				$filter_data_entry['id'] = $river_channels[$channel]['id'];
				$filter_data_entry['enabled'] = $river_channels[$channel]['enabled'];
				
				// Get the filter options for the current channel
				$option_data = Model_Channel_Filter::get_channel_filter_options($channel, $this->id);
			}
			else
			{
				$filter_data_entry['enabled'] = 0;
			}

			$filter_data_entry = array_merge($filter_data_entry, array(
				'channel' => $channel,

				'channel_name' => $channel_plugins[$channel]['name'],

				'grouped' => isset($channel_plugins[$channel]['group']),
				
				'group_key' => isset($channel_plugins[$channel]['group']) 
				    ? $channel_plugins[$channel]['group']['key'] 
				    : "",
				
				'group_label' => isset($channel_plugins[$channel]['group']) 
				    ? $channel_plugins[$channel]['group']['label'] 
				    : "",

				'options' => array($channel_plugins[$channel]['options']),

				'data' => $option_data
			));
			

			$filter_data[] = $filter_data_entry;
		}

		return $filter_data;
	}
	
	/**
	 * Modifies the status of a channel associated with the river. If the 
	 * channel is not associated with the river, it is created.
	 *
	 * @param string $channel Name of the channel
	 * @param int $user_id ID of the user modifying the status of the channel
	 * @param int $enabled Status flag of the channel
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public function modify_channel_status($channel, $user_id, $enabled)
	{
		// Check if the channel exists
		$filter = ORM::factory('channel_filter')
			->where('channel', '=', $channel)
			->where('user_id', '=', $user_id)
			->where('river_id', '=', $this->id)
			->find();
	
		if ($filter->loaded())
		{
			// Modify existing channel fitler
			$filter->filter_enabled = $enabled;
			$filter->filter_date_modified = date('Y-m-d H:i:s');
			$filter->save();
		
			return TRUE;
		}
		else
		{
			try {
				// Create a new channel fitler
				$filter = new Model_Channel_Filter();
				$filter->channel = $channel;
				$filter->river_id = $this->id;
				$filter->user_id = $user_id;
				$filter->filter_enabled = $enabled;
				$filter->filter_date_add = date('Y-m-d H:i:s');
				$filter->save();
				
				return TRUE;
			}
			catch (Kohana_Exception $e)
			{
				// Catch and log exception
				Kohana::$log->add(Log::ERROR, 
				    "An error occurred while enabling/disabling the channel: :error",
				    array(":error" => $e->getMessage())
				);
			
				return FALSE;
			}
		}
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
	public static function get_droplets($user_id, $river_id, $page = 1, $max_id = PHP_INT_MAX, $sort = 'DESC')
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);

		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{						
			// Build River Query
			$query = DB::select(array('droplets.id', 'id'), 
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
			    ->where('droplets.id', '<=', $max_id)
			    ->order_by('droplets.droplet_date_pub', $sort)
			    ->group_by('droplets.id');	   

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
				$droplet['droplet_content'] = utf8_encode($droplet['droplet_content']);
				$droplet['droplet_title'] = utf8_encode($droplet['droplet_title']);
			}
			
			// Populate buckets array			
			Model_Droplet::populate_buckets($droplets['droplets']);
			
			// Populate tags array			
			Model_Droplet::populate_tags($droplets['droplets'], $river_orm->account_id);
			
			// Populate links array			
			Model_Droplet::populate_links($droplets['droplets']);
			
			// Populate places array			
			Model_Droplet::populate_places($droplets['droplets']);
			
			// Populate the discussions array
			Model_Droplet::populate_discussions($droplets['droplets']);
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
	public static function get_droplets_since_id($user_id, $river_id, $since_id)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		
		$river_orm = ORM::factory('river', $river_id);
		if ($river_orm->loaded())
		{
			$query = DB::select(array('droplets.id', 'id'), 'droplet_title', 'droplet_content', 
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
			    ->where('droplets.droplet_processed', '=', 1)
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('droplets.id', '>', $since_id)
			    ->order_by('droplets.droplet_date_pub', 'ASC')
			    ->group_by('droplets.id')
			    ->limit(self::DROPLETS_PER_PAGE)
			    ->offset(0);
			
			$droplets['droplets'] = $query->execute()->as_array();
			
			// Encode content and title as utf8 in case they arent
			foreach ($droplets['droplets'] as & $droplet) 
			{
				$droplet['droplet_content'] = utf8_encode($droplet['droplet_content']);
				$droplet['droplet_title'] = utf8_encode($droplet['droplet_title']);
			}
			
			// Populate buckets array			
			Model_Droplet::populate_buckets($droplets['droplets']);
        	
			// Populate tags array			
			Model_Droplet::populate_tags($droplets['droplets'], $river_orm->account_id);
			
			// Populate links array			
			Model_Droplet::populate_links($droplets['droplets']);
        	
			// Populate places array			
			Model_Droplet::populate_places($droplets['droplets']);
			
			// Populate the discussions array
			Model_Droplet::populate_discussions($droplets['droplets']);
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
		$query = DB::select(array(DB::expr('MAX(droplets.id)'), 'id'))
		    ->from('droplets')
		    ->join('rivers_droplets', 'INNER')
		    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
		    ->join('identities')
		    ->on('droplets.identity_id', '=', 'identities.id')
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
		
		// Does the user own the river?
		if ($this->account->user_id == $user_id)
		{
			return TRUE;
		}

		
		// Is the user id an account collaborator?
		if ($this->account->account_collaborators->where('user_id', '=', $user_orm->id)->find()->loaded())
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
	 * Get a list of channel names in this river
	 *
	 * @param integer $river_id
	 * @return array
	 */
	public function get_channels()
	{
		$ret = array();
		
		foreach ($this->channel_filters->find_all() as $channel_filter)
		{
			$ret[] = $channel_filter->channel;
		}
		
		return $ret;
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
			$collaborators[] = array('id' => $collaborator->user->id, 
			                         'name' => $collaborator->user->name,
			                         'account_path' => $collaborator->user->account->account_path,
			                         'collaborator_active' => $collaborator->collaborator_active,
			                         'avatar' => Swiftriver_Users::gravatar($collaborator->user->email, 40)
			);
		}
		
		return $collaborators;
	}
	
}

?>
