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

		// Run the filter options set through validation
		$post->rule('filter', array($this, 'validate_input'), array(':validation',':field'))
			->label('filter', __('Twitter Channel Item'));
	}

	/**
	 * The twitter channel input items are arrays (e.g. item[]) so we need to run
	 * through each
	 * 
	 * @param	Validation	Validation object
	 * @param	string field name
	 * @return	void
	 */ 
	public function validate_input($validation, $field)
	{
		foreach ($validation[$field] as $type => $options)
		{
			if ($type == 'twitter')
			{
				foreach ($options as $key => $inputs)
				{
					foreach ($inputs as $input)
					{
						// Perform validation on the returned $values

						// String length should be less than 255
						if ( $input['value'] AND strlen($input['value']) > 255 )
						{
							$validation->error($field, 'max_length', array(':value', 255));
						}
						// String length should be greater than 3
						if ( $input['value'] AND strlen($input['value']) < 3 )
						{
							$validation->error($field, 'min_length', array(':value', 3));
						}
					}
				}
			}
		}
	}
}

// Initialize the plugin
new Twitter_Init;
?>
