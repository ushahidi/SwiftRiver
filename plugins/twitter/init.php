<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the Swiftcore plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Twitter_Init {

	public function __construct() {
		Swiftriver_Event::add('swiftriver.droplet.link_droplet', array($this, 'link_droplet'));
	}

	/**
	 * Call back method for swiftriver.droplet.link_droplet to link droplet to channel filters
	 */
	public function link_droplet() {
		$droplet = Swiftriver_Event::$data;
		print "Droplet -->" . $droplet->droplet_content . "<br/>";

		$channel_filter_options = Util_Channel_Filter::get_filter_options();
		foreach($channel_filter_options as $option ) {
			if($option->key == 'keyword' and preg_match("/\b" . $option->value . "\b/i", $droplet->droplet_content)) {
				print "Matches --> " . $option->value .  "," . $option->id . "<br/>";
				$option->channel_filter->add('droplets', $droplet);
			}
		}
	}
}

new Twitter_Init;

?>
