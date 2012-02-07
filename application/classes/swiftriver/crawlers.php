<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class for handling the channel crawling activity
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
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
	    
		$ret = call_user_func(self::$_crawlers[$channel], $river_id);
		
		// Update the schedule for the channel
		Model_Channel_Filter::update_runs($river_id, $channel, $ret);   		
	}
	
}
?>