<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filters
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
class Model_Channel_Filter extends ORM {
    
    const RUN_INTERVAL = 5; // Default minimum duration between crawls
    
	/**
	 * A channel_filter has many droplets and channel_filter_options
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'channel_filter_options' => array()
		);

	/**
	 * A channel_filter belongs to an account and a user
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array(),
		'user' => array(),
		'river' => array()
		);

	
	/**
	 * Overload saving to perform additional functions on the channel_filter
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time channel_filters only
		if ($this->loaded() === FALSE)
		{
			// Save the date the channel_filter was first added
			$this->filter_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			// Get the channel filter options 
			$channel_options = $this->channel_filter_options->find_all();

			// Determine the event to run against the channel filter options
			// NOTE: This is a bit ghetto
			$event_name = ($this->filter_enabled == 0)
			    ? "swiftriver.channel.option.pre_delete"
			    : "swiftriver.channel.option.post_save";

			foreach ($channel_options as $option)
			{
				// Run the event
				Swiftriver_Event::run($event_name, $option);
			}
			
			// Set the date modified
			$this->filter_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}

	/**
	 * Overrides the default behaviour to perform
	 * extra tasks before removing the channel filter
	 * entry 
	 */
	public function delete()
	{
		// Delete the channel filter options
		DB::delete('channel_filter_options')
		    ->where('channel_filter_id', '=', $this->id)
		    ->execute();

		// Default
		parent::delete();
	}

	/**
	 * Get channel filter by channel and river id
	 *
	 * @param string $channel Name of the channel
	 * @param int $river_id Database ID of the river associated with the channel
	 * @return Database_Result
	 */
	public static function get_channel_filters($channel, $river_id)
	{
		$channel_filter = ORM::factory('channel_filter')
			->where('channel', '=', $channel)
			->where('river_id', '=', $river_id)
			->find();
			
		return $channel_filter;
	}

	/**
	 * Get all filter options by channel
	 *
	 * @param string $channel
	 * @return array
	 */
	public static function get_channel_filter_options($channel, $river_id)
	{
		$channel_filter_options = array();
		$channel_filter = self::get_channel_filters($channel, $river_id);
		
		// Verify that the channel filter for the river is enabled
		if ( ! $channel_filter->loaded())
			return array();
		
		$options = $channel_filter->channel_filter_options->find_all();
		
		foreach ($options as $option)
		{
			$channel_filter_options[] = array(
				"id" => $option->id,
				"key" => $option->key,
				"data" => json_decode($option->value, TRUE)
			);
		}
		
		return $channel_filter_options;
	}


	/**
	 * Get all channels prioritizing new rivers and 
	 * those that had failure in their last run followed
	 * by all other rivers in order of when they last ran.	    
	 *
	 * @return array
	 */
	
	public static function get_channel_filters_by_run_date($since_date = NULL)
	{
	    if ( ! $since_date) 
	    {
	        $since_date = DB::expr('now() - interval '.self::RUN_INTERVAL.' minute');
	    }
	    $query = DB::select('river_id', 'channel')
	                 ->from('channel_filters')
	                 ->where('filter_enabled', '=', 1)
	                 ->where('filter_last_run', '<', $since_date)
	                 ->order_by('filter_last_successful_run', 'ASC')
	                 ->order_by('filter_last_run', 'ASC');

	   return $query->execute()->as_array();
	}
	
	/**
	 * Update channel filter run statistics
	 *
	 * @param integer $river_id
	 * @param string  $channel
	 * @param boolean $success
	 * @param string  $run_date
	 * @return void
	 */	
	public static function update_runs($river_id, $channel, $success, $run_date = NULL)
	{	    
	    if ( ! $run_date)
	    {
	        $run_date =  DB::expr('Now()');
	    }
	    
	    $channel_filter = Model_Channel_Filter::get_channel_filters($channel, $river_id);

        if ($channel_filter and $channel_filter->loaded())
        {
    	    $channel_filter->filter_last_run = $run_date;
    	    if ( $success) 
    	    {
    	        $channel_filter->filter_last_successful_run = $run_date;
    	    }
    	    $channel_filter->filter_runs = new Database_Expression('filter_runs + 1');
    	    $channel_filter->update();            
        }
	}
	
	/**
	 * Add a channel filter option
	 *
	 * @param string key
	 * @param mixed  value
	 * @return Model_Channel_Filter_option
	 */	
	public  function add_option($key, $value)
	{
		$filter_option = ORM::factory('channel_filter_option');
		$filter_option->channel_filter_id = $this->id;
		$filter_option->key = $key;
		if(is_string($value))
		{
			$filter_option->value = $value;
		}
		else
		{
			$filter_option->value = json_encode($value);
		}
		$filter_option->save();
		
		// Run post_save events
		Swiftriver_Event::run('swiftriver.channel.option.post_save', $filter_option);
		
		return $filter_option;
	}
}
