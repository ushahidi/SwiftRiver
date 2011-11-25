<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * RSS Channel worker
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
class Swiftriver_Worker_Channel_Rss extends Swiftriver_Worker_Channel {

	/**
	 * @see Swiftriver_Task_Channel->channel_worker
	 */
	public function channel_worker($job)
	{
		$urls = array(); //This will be a set of URLs from the DB to process

		// Get the links to crawl from the DB
		$channel_filters = Model_Channel_Filter::get_channel_filters('rss');
		
		foreach($channel_filters as $filter)
		{
			foreach($filter->channel_filter_options->find_all() as $option)
			{
				if ($option->key == 'url' and !in_array($option->value, $urls))
				{
					$urls[] = $option->value;
				}
			}
		}
		
		// Submit URLs for content fetch
		foreach($urls as $url)
		{
			$this->_parse_url(array('url' => $url));
		}
		
	}

	/**
	 * Retrieve RSS from URL
	 * @param array $options
	 * @return void
	 */
	private function _parse_url($options = array())
	{
		include_once Kohana::find_file('vendor', 'simplepie/SimplePieAutoloader');
		include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

		if (isset($options['url']) AND $this->_is_url($options['url']))
		{
			$feed = new SimplePie();
			
			// Set which feed to process.
			$feed->set_feed_url( $options['url'] );
			
			// Allow us to choose to not re-order the items by date.
			$feed->enable_order_by_date(true);

			// Set Simplepie Cache Location
			$feed->set_cache_location( Kohana::$cache_dir );
			
			// Run SimplePie.
			$success = $feed->init();

			// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
			$feed->handle_content_type();

			if ($success)
			{
				$locale = '';
				if ($feed->get_language())
				{
					$locale_array = explode('-', $feed->get_language());
					$locale = $locale_array[0];
				}
				
				foreach($feed->get_items() as $feed_item)
				{
					$droplet = Swiftriver_Dropletqueue::get_droplet_template();
					$droplet['channel'] = 'rss';
					$droplet['identity_orig_id'] = $options['url'];
					$droplet['identity_username'] = $feed->get_link();
					$droplet['identity_name'] = $feed->get_title();
					$droplet['droplet_orig_id'] = trim((string) $feed_item->get_link());
					$droplet['droplet_type'] = 'original';
					$droplet['droplet_title'] = trim(strip_tags(str_replace('<', ' <', $feed_item->get_title())));
					$droplet['droplet_content'] = trim(strip_tags(str_replace('<', ' <', $feed_item->get_description())));
					$droplet['droplet_raw'] = $feed_item->get_description();
					$locales = explode('-', $feed->get_language());
					$droplet['droplet_locale'] = $locales[0];
					$droplet['droplet_date_pub'] = date("Y-m-d H:i:s", strtotime($feed_item->get_date()));
					
					// Add droplet to the queue
					Swiftriver_Dropletqueue::add($droplet, FALSE);
				}		
			}
		}
	}

	private function _is_url($url = NULL)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
}
