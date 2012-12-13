<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filters
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
	 * Auto-update columns for updates
	 * @var string
	 */
	protected $_updated_column = array('column' => 'filter_date_add', 'format' => 'Y-m-d H:i:s');

	/**
	 * Auto-update columns for creation
	 * @var string
	 */
	protected $_created_column = array('column' => 'filter_date_modified', 'format' => 'Y-m-d H:i:s');
	
	
	/**
	 * Overload saving to perform additional functions on the channel_filter
	 */
	public function save(Validation $validation = NULL)
	{
		$original_values = $this->original_values();
		$ret = parent::save();
		
		// Run post_save events
		if ( isset($original_values['filter_enabled']) AND 
			! (bool) $original_values['filter_enabled'] AND 
				(bool) $this->filter_enabled)
		{
			Swiftriver_Event::run('swiftriver.channel.enable', $this);
		} 
		else if ( isset($original_values['filter_enabled']) AND 
				(bool) $original_values['filter_enabled'] AND 
				! (bool) $this->filter_enabled)
		{
			Swiftriver_Event::run('swiftriver.channel.disable', $this);
		}
		
		return $ret;
	}

	/**
	 * Overrides the default behaviour to perform
	 * extra tasks before removing the channel filter
	 * entry 
	 */
	public function delete()
	{
		Swiftriver_Event::run('swiftriver.channel.disable', $this);
		
		Database::instance()->query(NULL, 'START TRANSACTION');
		
		foreach ($this->get_quota_usage() as $key => $value) 
		{
			Model_Account_Channel_Quota::decrease_quota_usage($this->river->account->id, $this->channel, $key, $value);
		}
		
		// Delete the channel filter options
		DB::delete('channel_filter_options')
		    ->where('channel_filter_id', '=', $this->id)
		    ->execute();

		// Default
		parent::delete();
		
		Database::instance()->query(NULL, 'COMMIT');
	}

	/**
	 * Get channel filter by channel and river id
	 *
	 * @param string $channel Name of the channel
	 * @param int $river_id Database ID of the river associated with the channel
	 * @return Model_Channel_Filter
	 */
	public static function get_channel_filters($channel, $river_id)
	{
		$channel_filter = ORM::factory('Channel_Filter')
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
	                 ->from('rivers')
					 ->join('channel_filters', 'INNER')
					 ->on('channel_filters.river_id', '=', 'rivers.id')
					 ->where('river_active', '=', 1)
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
    	    if ($success) 
    	    {
    	        $channel_filter->filter_last_successful_run = $run_date;
    	    }
    	    $channel_filter->filter_runs = new Database_Expression('filter_runs + 1');
    	    $channel_filter->update();            
        }
	}
	
	/**
	 * Update channel filter option and creates it if it doesn't exist
	 *
	 * Value is an array with the following fields:
	 *  array(
	 *		'key' => 'url',
	 *		'value' => 'http://ushahidi.com/rss.xml',
	 *		'title' => 'Ushahidi',
	 *		'quota_usage' => 1
	 *	)
	 *
	 * @param int id
	 * @param array  value 
	 * @return Model_Channel_Filter_option
	 */	
	public  function update_option($value, $id = 0)
	{
		$filter_option = ORM::factory('Channel_Filter_Option')
			                ->where('channel_filter_id', '=', $this->id)
			                ->where('id', '=', $id)
							->find();
		
		if ($filter_option->loaded())
		{
			Swiftriver_Event::run('swiftriver.channel.option.pre_delete', $filter_option);
		}
		
		$filter_option->channel_filter_id = $this->id;
		$filter_option->key = $value['key'];		
		unset($value['key']);
		$filter_option->value = json_encode($value);
		$filter_option->save();
				
		return $filter_option;
	}
	
	/**
	 * Delete a channel option
	 *
	 * @param int id
	 * @return void
	 */	
	public  function delete_option($id)
	{
		$filter_option = ORM::factory('Channel_Filter_Option')
							->where('channel_filter_id', '=', $this->id)
							->where('id', '=', $id)
							->find();
		
		if ($filter_option->loaded())
		{
			$filter_option->delete();
		}
	}
	
	/**
	 * Get the total quota usage of the channel options in this channel
	 *
	 * @return array
	 */
	public function get_quota_usage() {
		$quota_usage = array();
			
		$options = $this->channel_filter_options->find_all();		
		foreach ($options as $option)
		{
			if ( ! isset($quota_usage[$option->key]))
			{
				$quota_usage[$option->key] = 0;
			}
			$quota_usage[$option->key] += $option->get_quota_usage();
		}
		
		return $quota_usage;
	}
}
