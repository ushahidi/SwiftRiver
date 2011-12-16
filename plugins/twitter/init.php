<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the Swiftcore plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Twitter_Init {

	public function __construct()
	{
		// Validate Channel Filter Settings Input
		Swiftriver_Event::add('swiftriver.river.pre_save', array($this, 'channel_validate'));

		// Save Channel Settings
		//Swiftriver_Event::add('swiftriver.river.save', array($this, 'channel_save'));
	}

	/**
	 * Call back method for swiftriver.river.pre_save to validate channel settings
	 */
	public function channel_validate()
	{
		// Get the event data
		$filter_options =  & Swiftriver_Event::$data;
		
		// Get the data for the twitter plugin
		Swiftriver_Plugins::validate_channel_options($filter_options, 'twitter');
	}

}

// Initialize the plugin
new Twitter_Init;

?>
