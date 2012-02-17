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
		if ($option_data['channel'] == 'rss')
		{
			// Include the SimplePie libraries
			include_once Kohana::find_file('vendor', 'simplepie/SimplePie.compiled');
			include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

			// Validate the url value using SimplePie
			$url = $option_data['data']['value'];

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
				$option_data['data']['value'] = $feed->subscribe_url();
				$option_data['data']['title'] = $feed->get_title();

				// Garbage collection
				unset ($feed);
			}
			else
			{
				// Validation failed - Empty the option data
				Kohana::$log->add(Log::ERROR, "Invalid RSS URL - :url", array(':url' => $url));

				$option_data = NULL;
			}
		}

	}
}

new Rss_Init;

?>
