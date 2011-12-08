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

		// Validate Channel Settings Input
		Swiftriver_Event::add('swiftriver.river.pre_save', array($this, 'channel_validate'));

		// Save Channel Settings
		//Swiftriver_Event::add('swiftriver.river.save', array($this, 'channel_save'));
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

	/**
	 * Call back method for swiftriver.river.pre_save to validate channel settings
	 */
	public function channel_validate()
	{
		// Get the event data
		$post = Swiftriver_Event::$data;

		// Run it through additional validation
		$post->rule('twitter_keyword', array($this, 'validate_array_length'), array(':validation',':field'))
			->rule('twitter_person', array($this, 'validate_array_length'), array(':validation',':field'))
			->rule('twitter_place', array($this, 'validate_array_length'), array(':validation',':field'));
	}

	/**
	 * The twitter channel input items are arrays (e.g. item[]) so we need to run
	 * through each
	 * 
	 * @param	Validation	Validation object
	 * @param	string field name
	 * @return	void
	 */ 
	public function validate_array_length($validation, $field)
	{
		for ($i=0; $i < count($validation[$field]) ; $i++)
		{
			if ( strlen($validation[$field][$i]) < 255 )
			{
				$validation->error($field, 'max_length', array(':value', 255));
			}			
		}
	}
}

// Initialize the plugin
new Twitter_Init;
?>
