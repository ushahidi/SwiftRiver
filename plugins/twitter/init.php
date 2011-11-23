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
		Swiftriver_Event::add('swiftriver.droplet.link_droplet', array($this, 'link_droplet'));
	}

	/**
	 * Call back method for swiftriver.droplet.link_droplet to link droplet to channel filters
	 */
	public function link_droplet()
	{
		// Get the event data
		$droplet = Swiftriver_Event::$data;
		
		// Get the channel filter options
		$channel_filter_options = Util_Channel_Filter::get_filter_options();
		
		// Link the droplets to the channel filter options
		foreach ($channel_filter_options as $option)
		{
			if ($option->key == 'keyword' AND preg_match("/\b" . $option->value . "\b/i", $droplet->droplet_content))
			{
				$option->channel_filter->add('droplets', $droplet);
			}
		}
	}
}

// Initialize the plugin
new Twitter_Init;

?>
