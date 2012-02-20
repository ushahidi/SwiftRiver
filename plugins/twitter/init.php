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
	    // Register as a crawler
	    Swiftriver_Crawlers::register('twitter', array(new Swiftriver_Crawler_Twitter(), 'crawl'));
	    	    
		// Validate Channel Filter Settings Input
		Swiftriver_Event::add('swiftriver.channel.option.pre_save', array($this, 'validate'));
	}

	/**
	 * Call back method for swiftriver.river.pre_save to validate channel settings
	 */
	public function validate()
	{
		// Get the event data
		$option_data =  & Swiftriver_Event::$data;
		
		// Apply validation rules to the options
		if ($option_data['channel'] == 'twitter')
		{
			// TODO: Sanity checks for the values
		}
	}

}

// Initialize the plugin
new Twitter_Init;

?>
