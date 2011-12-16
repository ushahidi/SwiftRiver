<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the RSS plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Rss_Init {

	public function __construct() 
	{
		// Validate Channel Filter Settings Input
		Swiftriver_Event::add('swiftriver.river.pre_save', array($this, 'channel_validate'));
	}


	/**
	 * Call back method for swiftriver.river.pre_save to validate channel settings
	 */
	public function channel_validate()
	{
		// Get the event data
		$filter_options = & Swiftriver_Event::$data;
		
		Swiftriver_Plugins::validate_channel_options($filter_options, 'rss');
	}
}

new Rss_Init;

?>
