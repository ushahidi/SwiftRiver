<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the RSS plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class Rss_Init {

	public function __construct() 
	{
	    // Register as a crawler
	    Swiftriver_Crawlers::register('rss', array(new Swiftriver_Crawler_Rss(), 'crawl'));

		// Hook into the settings page
		Swiftriver_Event::add('swiftriver.settings.nav', array($this, 'settings_nav'));
		
		// Hook into welcome page new river creation
		Swiftriver_Event::add('swiftriver.welcome.create_river', array($this, 'add_chanel_options'));
	    
		// Validate the channel option data before it's saved to the DB
		Swiftriver_Event::add('swiftriver.channel.option.pre_save', array($this, 'validate'));
	}
	
	/**
	 * Add channel options to a river created via the welcome page
	 * 
	 * @return	void
	 */
	public function add_chanel_options()
	{
		list($river, $user, $keywords) = Swiftriver_Event::$data;
		
		// Get starter URLs from site settings
		$urls = Model_Setting::get_setting(RSS_Util::SETTING_KEY);
		if ( ! $urls) 
		{
			return;
		}
		$urls = json_decode($urls);
		if (empty($urls))
		{
			return;
		}
		
		// Add a rss channel
		$channel_filter = $river->create_channel_filter('rss', $user->id, TRUE);
		$channel_filter->add_option('keyword', array(
			                                'label' => 'Keyword',
			                                'type' => 'text',
			                                'value' => trim($keywords)
		));
		
		foreach ($urls as $url => $title)
		{
			$channel_filter->add_option('url', array(
				                                'label' => $title,
				                                'type' => 'text',
				                                'value' => $url
			));
		}
	}
	
	
	/**
	 * Display link in the settings navigation
	 * 
	 * @return	void
	 */
	public function settings_nav()
	{
		$active = Swiftriver_Event::$data;
		
		echo '<li '.(($active == 'rsswelcome') ? 'class="active"' : '').'>'.
			HTML::anchor(URL::site('settings/rsswelcome'), __('RSS Starter URLs')).
			'</li>';
	}


	/**
	 * Call back method for swiftriver.channel.option.pre_save to validate channel settings
	 */
	public function validate()
	{
		// Get the event data
		$option_data = & Swiftriver_Event::$data;
		

		// Proceed only if the channel is RSS
		if ( ! empty($_FILES) AND $option_data["key"] == "opml_import")
		{
			$file_data = $option_data['opml_import'];

			// Validation
			if (Upload::type($file_data, array('xml')))
			{
				// Begin processing the feed
				$importer = new Swiftriver_OPML_Import();
				$importer->init($file_data['tmp_name']);

				// Load the channel configuration
				$channel_config = Swiftriver_Plugins::get_channel_config('rss');

				// Get the feed entries
				$feed_entries = array();
				foreach ($importer->get_feeds() as $feed)
				{
					// Skip valiation of the RSS URL on the premise that the 
					// URLs were validated at source?
					$entry = array(
						"label" => $channel_config['options']['url']['label'],
						"type" => $channel_config['options']['url']['type'],
						"value" => $feed['url'],
						"title" => $feed['title']
					);
					
					$feed_entries[] = array(
						"channel_filter_id" => $option_data["channel_filter_id"],
						"key" => "url",
						"data" => $entry
					);
				}

				// Reset the option data
				$option_data = $feed_entries;
				$option_data['multiple'] = TRUE;
			}
		}
		elseif ($option_data['channel'] == 'rss')
		{
			$url = $option_data['data']['value'];
			if (($feed = RSS_Util::validate_feed_url($url)) != FALSE)
			{
				$option_data['data']['value'] = $feed['value'];
				$option_data['data']['title'] = $feed['title'];
			}
			else
			{
				// Validation failed - Empty the option data
				Kohana::$log->add(Log::DEBUG, "Invalid RSS URL - :url", array(':url' => $url));

				$option_data = NULL;
			}
		}

	}
}

new Rss_Init;

?>