<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the Swiftcore plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Rss_Init {

	public function __construct() 
	{
		Swiftriver_Event::add('swiftriver.droplet.link_droplet', array($this, 'link_droplet'));

		// Validate Channel Filter Settings Input
		Swiftriver_Event::add('swiftriver.river.pre_save', array($this, 'channel_validate'));
	}

	/**
	 * Call back method for swiftriver.droplet.link_droplet to link droplet to channel filters
	 */
	public function link_droplet() 
	{
		$droplet_arr = Swiftriver_Event::$data;
		
		$droplet = ORM::factory('droplet', $droplet_arr['id']);

		if ($droplet->channel != 'rss')
			return;

		//Link droplet to every channel id that has its url
		//and it the filter has keywords then only associate 
		//the droplet to a channel if any of the keywods match
		$options = $this->_get_options();
		foreach($options as $option)
		{
			if ($option['url'] == $droplet->identity->identity_orig_id)
			{
				//Check if droplet must match a keyword otherwise just associated it
				if ( ! empty($option['keywords']))
				{
					foreach($option['keywords'] as $keyword)
					{
						Kohana::$log->add(Log::DEBUG, "Has keywords" . $option['url'] . " " . $keyword . $option['channel_filter']->id);
						if (preg_match("/\b" . $keyword . "\b/i", $droplet->droplet_content))
						{
							Kohana::$log->add(Log::DEBUG, "Keyword matched" . $keyword);
							$option['channel_filter']->add('droplets', $droplet);
							break;
						}
					}
				}
				else
				{
					//Filter does not have keywords so all droplets will be assocaiated with this filter
					Kohana::$log->add(Log::DEBUG, "No keywords" . $option['url']. ',' . $option['channel_filter']->id);
					if( ! $option['channel_filter']->has('droplets', $droplet))
					{
						$option['channel_filter']->add('droplets', $droplet);
					}
				}
			}
		}
	}

	/**
	 * Format the rss filter options into an array
	 *
	 * @return array
	 */
	private function _get_options()
	{
		$options = array();
		$channel_filters = Model_Channel_Filter::get_channel_filters('rss');
		
		foreach ($channel_filters as $channel_filter)
		{
			$option = array();
			$option['channel_filter'] = $channel_filter;
			$option['keywords'] = array();
			$channel_filter_options = $channel_filter->channel_filter_options->find_all();
			foreach ($channel_filter_options as $channel_filter_option)
			{
				if ($channel_filter_option->key == 'keyword')
				{
					$option['keywords'][] = $channel_filter_option->value;
				}
				else
				{
					$option[$channel_filter_option->key] = $channel_filter_option->value;
				}
			}
			$options[] = $option;
		}
		return $options;
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
			->label('filter', __('RSS/Atom URL'));
	}

	/**
	 * The RSS channel input items are arrays (e.g. item[]) so we need to run
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
			if ($type == 'rss')
			{
				foreach ($options as $key => $inputs)
				{
					foreach ($inputs as $input)
					{
						if ( ! Valid::url($input['value']))
						{
							$validation->error($field, 'url', array(':value', 3));
						}
					}
				}
			}
		}
	}	
}

new Rss_Init;

?>
