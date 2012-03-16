<?php defined('SYSPATH') OR die('No direct script access');

/**
 * RSS Utilities
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class RSS_Util {
	
	const SETTING_KEY = 'rss_starter_urls';

	/**
	 * Verifies whether a URL is a valid RSS feed or points to a page
	 * that contains an RSS feed
	 *
	 * @param string $url URL to validate
	 * @return mixed Array of the feed url and title on succeed, FALSE otherwise
	 */
	public static function validate_feed_url($url)
	{
		// Include the SimplePie libraries
		include_once Kohana::find_file('vendor', 'simplepie/SimplePie.compiled');
		include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

		// Validate the url value using SimplePie
		$feed = new SimplePie();
		$feed->set_feed_url($url);

		// Disable caching
		$feed->enable_cache(FALSE);

		// Set the timeout to 30s
		$feed->set_timeout(30);

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

		// Log the SimplePie error
		Kohana::$log->add(Log::DEBUG, "Failed to validate feed - :error", 
			array(':error' => $feed->error()));

		unset ($feed);

		return FALSE;

	}
}

?>