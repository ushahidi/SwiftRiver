<?php defined('SYSPATH') OR die('No direct access allowed.');

class Swiftriver_Crawlers {

	// Event callbacks
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
		
		Model_Channel_Filter::update_runs($river_id, $channel, $ret);   		
	}
	
}
?>