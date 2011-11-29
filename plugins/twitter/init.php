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
		$droplet_arr = Swiftriver_Event::$data;
		
		$droplet = ORM::factory('droplet', $droplet_arr['id']);
		
		if ($droplet->channel != 'twitter')
			return;
	
		Kohana::$log->add(Log::DEBUG, "Droplet -->" . $droplet->droplet_content);

		$channel_filter_options = Model_Channel_Filter::get_channel_filter_options('twitter');
		foreach ($channel_filter_options as $option )
		{
			if ($option->key == 'keyword' AND preg_match("/\b" . $option->value . "\b/i", $droplet->droplet_content))
			{
				//Kohana::$log(Log::DEBUG, "Matches --> " . $option->value .  "," . $option->id);
				if(!$option->channel_filter->has('droplets', $droplet))
				{
					$option->channel_filter->add('droplets', $droplet);
				}
			}
		}
	}
}

// Initialize the plugin
new Twitter_Init;

?>
