<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class for handling the channel crawling activity
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category    Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Swiftriver_Crawlers {

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
	    Kohana::$log->write();
	
		$ret = call_user_func(self::$_crawlers[$channel], $river_id);
		
		// Update the schedule for the channel
		Model_Channel_Filter::update_runs($river_id, $channel, $ret);   		
	}
	
	/**
	 * Run the crawlers
	 */
	public static function do_crawl()
	{
		Kohana::$log->add(Log::INFO, "Crawler started");								
		Kohana::$log->write();		
		self::do_schedule();
		
		// If we got some drops, process them
		if ( ! Swiftriver_Dropletqueue::isempty())
		{
			Kohana::$log->add(Log::INFO, "Crawler post processing");	
			Kohana::$log->write();
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
		$river_orm = ORM::factory('River', $river_id);
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