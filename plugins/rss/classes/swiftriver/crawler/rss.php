<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * RSS crawler
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Libraries
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Crawler_Rss  {

	/**
	 * Fetch feeds attached to a river id
	 *
	 * @param   int   $river_id	 
	 * @return  bool
	 */
	public function crawl($river_id)
	{
		// If the river ID is NULL or non-existent, exit
		if (empty($river_id) OR ! ORM::factory('river', $river_id)->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Invalid database river id: :river_id', 
			    array(':river_id' => $river_id));
			
			return FALSE;
		}
		
		// Get the links to crawl from the DB
		$filter_options = Model_Channel_Filter::get_channel_filter_options('rss', $river_id);
		foreach ($filter_options as $option)
		{
			if ($option['key'] == 'url')
			{
			    $url = $option['data']['value'];
    			$this->_parse_url(array('url' => $url), $river_id);
			}
		}
		
		return TRUE;
		
	}

	/**
	 * Retrieve RSS from URL
	 *
	 * @param array $options
	 * @param int $river_id
	 * @return void
	 */
	private function _parse_url($options = array(), $river_id)
	{
		include_once Kohana::find_file('vendor', 'simplepie/SimplePie.compiled');
		include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

		if
		(
			isset($options['url']) AND
			($feed_url = Rss_Util::validate_feed_url($options['url']))
		)
		{
			// Log file writes have to be immediate because of fork()
			// otherwise the log shall be written when process exits
			Kohana::$log->add(Log::INFO, "RSS Crawler fetching :url",
			    array(':url' => $feed_url['value']));
			Kohana::$log->write();
		    
			$feed = new SimplePie();
			
			// Set which feed to process - use the feed URL returned
			// by the feed validation
			$feed->set_feed_url($feed_url['value']);
			
			// Allow us to choose to not re-order the items by date.
			$feed->enable_order_by_date(TRUE);

			// Set Simplepie Cache Location
			$feed->set_cache_location(Kohana::$cache_dir);
			
			// Run SimplePie.
			$success = $feed->init();

			// This makes sure that the content is sent to the browser as text/html
			// and the UTF-8 character set (since we didn't change it).
			$feed->handle_content_type();

			if ($success)
			{
				$locale = '';
				if ($feed->get_language())
				{
					$locale_array = explode('-', $feed->get_language());
					$locale = $locale_array[0];
				}
				
				// Create and queue a droplet for each item in the feed				
				foreach($feed->get_items() as $feed_item)
				{					
					$droplet = Swiftriver_Dropletqueue::get_droplet_template();
					$droplet['channel'] = 'rss';
					$droplet['river_id'] = array($river_id);
					$droplet['identity_orig_id'] = $feed_url['value'];
					$droplet['identity_username'] = $feed->get_link();
					$droplet['identity_name'] = $feed->get_title();
					$droplet['identity_avatar'] = $feed->get_image_url();
					$droplet['droplet_orig_id'] = trim((string) $feed_item->get_link());
					$droplet['droplet_type'] = 'original';
					$droplet['droplet_title'] = trim(strip_tags(str_replace('<', ' <', $feed_item->get_title())));
					$droplet['droplet_raw'] = $droplet['droplet_content'] = $feed_item->get_content();
					$locales = explode('-', $feed->get_language());
					$droplet['droplet_locale'] = $locales[0];
					$droplet['droplet_date_pub'] = gmdate("Y-m-d H:i:s", strtotime($feed_item->get_date()));
					
					// Add droplet to the queue
					Swiftriver_Dropletqueue::add($droplet);
				}		
			}
		}		
	}
}
