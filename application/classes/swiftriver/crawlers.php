<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class for handling the channel crawling activity
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver_Crawlerer - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Swiftriver_Crawlers {

	protected static $crawl_mutex = 'SwiftRiver_Crawler';	

	/**
	 * Registry for channel crawlers and their respective callbacks
	 * @var array
	 */
	protected static $_crawlers = array();

	/**
	 * Register a crawler
	 *
	 * @param   string   channel name
	 * @param   array    http://php.net/callback
	 * @return  void
	 */
	public static function register($channel, $callback)
	{
		self::$_crawlers[$channel] = $callback;
	}
	
	/**
	 * Execute all crawlers or a specific crawler on a river
	 * or a specific crawler if name is provided.
	 *
	 * @param   int      river_id	 
	 * @param   string   channel name
	 * @return  void
	 */
	public static function run($river_id, $channel)
	{
	    Kohana::$log->add(Log::INFO, "Crawl requested for river with id :id and channel :channel",
	                                 array(':id' => $river_id, ':channel' => $channel));
	    
		$ret = call_user_func(self::$_crawlers[$channel], $river_id);
		
		// Update the schedule for the channel
		Model_Channel_Filter::update_runs($river_id, $channel, $ret);   		
	}
	
	/**
	 * Run the crawlers
	 */
	public static function do_crawl($river_id, $channel)
	{	
		
		// Only one instance of the crawler is allowed
		// to run at any time system wide
		if ( ! Swiftriver_Mutex::obtain(self::$crawl_mutex)) 
		{
			Kohana::$log->add(Log::ERROR, "Crawler unable to obtain lock");			
			return;
		}
		
		Kohana::$log->add(Log::INFO, "Crawler started");								
				
		// If a river_id or plugin is provided then only do a run for that
		// selection
		if ($river_id AND $channel) 
		{
			Swiftriver_Crawlers::run($river_id, $channel);			
		}
		else if ($river_id)
		{
			self::do_crawl_river($river_id);
		}
		else
		{
			// We create and run a schedule
			self::do_schedule();
		}
				
		Swiftriver_Mutex::release(self::$crawl_mutex);
		
		// If we got some drops, process them
		if ( ! Swiftriver_Dropletqueue::isempty()) {
			Kohana::$log->add(Log::INFO, "Crawler post processing");	
			Swiftriver_Dropletqueue::process();	  
		}		
		
		Kohana::$log->add(Log::INFO, "Crawler completed");	
	}
	
	/**
	 * Run the crawlers in order of their run dates
	 */ 
	public static function do_schedule()
	{		
		$jobs = Model_Channel_Filter::get_channel_filters_by_run_date();		
		foreach ($jobs as $job)
		{
			self::run($job['river_id'], $job['channel']);
		}
	}
	
	/**
	 * Run the crawlers on a specific river
	 */ 
	public static function do_crawl_river($river_id)
	{	
		$river_orm = ORM::factory('river', $river_id);
		if ( ! $river_orm->loaded())
		{
			Kohana::$log->add(Log::ERROR, "River with id :id does not exist", array(':id' => $river_id));
			return;
		}
		
		$channels =  $river_orm->get_channels();		
		foreach ($channels as $channel)
		{
			self::run($river_id, $channel);
		}
	}
	
}
?>