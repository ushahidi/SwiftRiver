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
	    
		// Validate the channel option data before it's saved to the DB
		Swiftriver_Event::add('swiftriver.channel.option.pre_save', array($this, 'validate'));
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
			if (($feed = $this->_validate_feed_url($url)) != FALSE)
			{
				$option_data['data']['value'] = $feed['value'];
				$option_data['data']['title'] = $feed['title'];
			}
			else
			{
				// Validation failed - Empty the option data
				Kohana::$log->add(Log::ERROR, "Invalid RSS URL - :url", array(':url' => $url));

				$option_data = NULL;
			}
		}

	}

	/**
	 * Verifies whether a URL is a valid RSS feed or points to a page
	 * that contains an RSS feed
	 *
	 * @param string $url URL to validate
	 * @return mixed Array of the feed url and title on succeed, FALSE otherwise
	 */
	private function _validate_feed_url($url)
	{
		// Include the SimplePie libraries
		include_once Kohana::find_file('vendor', 'simplepie/SimplePie.compiled');
		include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

		// Validate the url value using SimplePie
		$feed = new SimplePie();
		$feed->set_feed_url($url);

		// Disable caching
		$feed->enable_cache(FALSE);

		// Attempt to initialize the feed, parsing it etc
		if ($feed->init())
		{
			// Success!

			// Ensure that SimplePie is being served with the correct mime-type
			$feed->handle_content_type();
			
			// Grabe the subscribe URL from SimplePie
			$feed_data= array(
				'value' => $feed->subscribe_url(),
				'title' => $feed->get_title()
			);

			// Garbage collection
			unset ($feed);

			return $feed_data;
		}

		return FALSE;
		
	}
}

new Rss_Init;

?>