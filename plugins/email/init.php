<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Hook class for the Email plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */

class Email_Init {
	
	public function __construct()
	{
		// Register callback function for the event
		Swiftriver_Event::add('swiftriver.channel.pre_save', array($this, 'channel_validate'));
	}
	
	/**
	 * Callback function for the swiftriver.river.pre_save event
	 */
	public function channel_validate()
	{
		// Get the event data
		$filter_options = & Swiftriver_Event::$data;
		
		// Validate the specifed data
		Swiftriver_Plugins::validate_channel_options($filter_options, 'email');
	}
	
}

new Email_Init;

?>