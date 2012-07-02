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

	/**
	 * Twitter API endpoint for looking up screen names
	 */
	const LOOKUP_URL  = "https://api.twitter.com/1/users/show.json?screen_name=%s";

	public function __construct()
	{
	    // Register as a crawler
	    Swiftriver_Crawlers::register('twitter', array(new Swiftriver_Crawler_Twitter(), 'crawl'));
	    	    
		// Validate Channel Filter Settings Input
		Swiftriver_Event::add('swiftriver.channel.option.pre_save', array($this, 'validate'));
		
		// Hook into welcome page new river creation
		Swiftriver_Event::add('swiftriver.welcome.create_river', array($this, 'add_chanel_options'));
	}
	
	/**
	 * Add channel options to a river created via the welcome page
	 * 
	 * @return	void
	 */
	public function add_chanel_options()
	{
		list($river, $user, $keywords) = Swiftriver_Event::$data;
		
		// Add a twitter channel
		$channel_filter = $river->create_channel_filter('twitter', $user->id, TRUE);
		$channel_filter->add_option('keyword', array(
			                                'label' => 'Keyword',
			                                'type' => 'text',
			                                'value' => trim($keywords)
		));
	}

	/**
	 * Call back method for swiftriver.river.pre_save to validate channel settings
	 */
	public function validate()
	{
		// Get the event data
		$option_data =  & Swiftriver_Event::$data;
		
		// Apply validation rules to the options
		if (isset($option_data['channel']) AND $option_data['channel'] == 'twitter')
		{
			$this->_validate_user($option_data);
			$this->_validate_keyword($option_data);
		}
	}

	/**
	 * Validate Twitter User
	 * 
	 * @param array $option_data
	 * @return void
	 */
	private function _validate_user($option_data)
	{
		// Validate user ids - Verify that they exist on twitter
		if ($option_data['key'] == 'user')
		{
			$screen_names = explode(",", str_replace('"', '', $option_data['value']));
			
			foreach ($screen_names as $screen_name)
			{
				// Strip the '@' off the screen name
				if ('@' === substr($screen_name, 0, 1))
				{
					$screen_name = substr($screen_name, 1, strlen($screen_name) - 1);
				}

				$user_lookup_url = sprintf(Twitter_Init::LOOKUP_URL, urlencode($screen_name));
				$request =  Request::factory($user_lookup_url);

				// Execute the response
				$response = Request_Client_Curl::factory()
							    ->execute($request);

				$response_array =  json_decode($response->body(), TRUE);
				if (array_key_exists('errors', $response_array))
				{
					$exception_message = __('Invalid twitter user - @:screen_name',
						array(':screen_name' => $screen_name));
					throw new Swiftriver_Exception_Channel_Option($exception_message);
				}

			}
		}
	}

	/**
	 * Validate Twitter Keyword - Removes Stop Words
	 * 
	 * @param array $option_data
	 * @return void
	 */
	private function _validate_keyword($option_data)
	{
		if ($option_data['key'] == 'keyword')
		{
			// Explode value, and group quoted items
			//preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/',  $option_data['value'], $keywords);
			$keywords = str_getcsv($option_data['value']);
			$keywords = array_map('trim', $keywords);

			$found = array();
			$stops = Twitter_Functions::stop_words();

			foreach ($keywords as $key => $value)
			{
				if (in_array($value, $stops))
				{
					$found[] = $value;
				}
			}

			if (count($found))
			{
				$exception_message = __('Invalid twitter keywords - :keywords',
						array(':keywords' => implode(', ', $found)));
				throw new Swiftriver_Exception_Channel_Option($exception_message);
			}
		}
	}
}

// Initialize the plugin
new Twitter_Init;

?>
