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
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'rivers_droplets'
			)					
		);
	
	/**
	 * An account belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('account' => array());
	
	/**
	 * Validation for rivers
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('river_name', 'not_empty')
			->rule('river_name', 'min_length', array(':value', 3))
			->rule('river_name', 'max_length', array(':value', 255));
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
			->select('channel', 'filter_enabled')
			->where('river_id', '=', $this->id)
			->find_all();
		
		$filters = array();
		foreach ($results as $result)
		{
			$filters[$result->channel] = $result->filter_enabled;
		}
		
		return $filters;
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
	 * @param int $river_id Dataabse ID of the river
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
	 * @param int $river_id Database ID of the river
	 * @param int $page Offset to use for fetching the droplets
	 * @param string $sort Sorting order
	 * @return array
	 */
	public static function get_droplets($river_id, $page = 1, $sort = 'DESC')
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);

		if ($river_id)
		{
			// Build River Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
			                    'droplet_title', 'droplet_content', 
			                    'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->where('rivers_droplets.river_id', '=', $river_id)
			    ->where('droplets.droplet_processed', '=', 1);
			
			// Clone query before any filters have been applied
			$pre_filter = clone $query;
			
			// Order the dataset by droplet id
			$pre_filter->order_by('droplets.id', $sort);
			$pre_filter_data = $pre_filter->execute()->as_array();
			
			$droplets['total'] = count($pre_filter_data);

			// SwiftRiver Plugin Hook -- Hook into River Droplet Query
			//++ Allows for adding for more filters via Plugin
			Swiftriver_Event::run('swiftriver.river.filter', $query);

			// Check if we have max droplet id stored from a previous request
			$session = Session::instance();
			$max_droplet_id = $session->get('river_pagination_max_droplet');
			
			// Get the max droplet id from the returned data
			$current_max_id = count($pre_filter_data) > 0 
			    ? $pre_filter_data[0]['id'] 
			    : 0;
			
			if ($max_droplet_id > $current_max_id)
			{
			   $query->where('droplets.id', '<', $max_droplet_id);	   
			}
			else
			{
				$query->where('droplets.id', '<', $current_max_id);
				
				// Set the new max droplet id
				$session->set('river_pagination_max_droplet', $current_max_id);
			}

			// Order & Pagination offset
			$query->order_by('droplets.id', $sort);
			if ($page > 0)
			{
			    $query->limit(self::DROPLETS_PER_PAGE);	
		        $query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
	        }
	
			// Get our droplets as an Array
			$droplets['droplets'] = $query->execute()->as_array();
		}

		return $droplets;
	}
}
