<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Rss Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Crawler_Rss extends Controller_Crawler_Main {

	/**
	 * This Project
	 */
	protected $project;

	/**
	 * This Feed
	 */
	protected $feed;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}

	public function action_index()
	{
		$feeds = ORM::factory('feed')
			->where('service', '=', 'rss')
			->where('feed_enabled', '=', '1')
			->find_all();
		
		foreach ($feeds as $feed)
		{
			$this->feed = $feed;
			$this->project = $feed->project;

			// Which RSS Option are we going to use?
			$service_option = $feed->service_option;

			$options = array();

			// Get the Search parameters
			$params = $feed->feed_options->find_all();
			foreach ($params as $param)
			{
				$options[$param->key] = $param->value;
			}

			$this->$service_option($options);
		}
	}

	/**
	 * Retrieve RSS from URL
	 * @param array $options
	 * @return void
	 */
	private function url($options = array())
	{
		include_once Kohana::find_file('vendor', 'simplepie/SimplePieAutoloader');
		include_once Kohana::find_file('vendor', 'simplepie/idn/idna_convert.class');

		if ( isset($options['url']) AND $this->_is_url($options['url']) )
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

			if ( $success )
			{
				$locale = '';
				if ($feed->get_language())
				{
					$locale_array = explode('-', $feed->get_language());
					$locale = $locale_array[0];
				}

				// Create a new Source
				$source = ORM::factory('source')
					->where( 'source_link', '=', $feed->get_link() )
					->find();
				$source->source_link = $feed->get_link();
				$source->service = 'rss';
				$source->source_name = $feed->get_title();
				$source->source_description = $feed->get_description();
				$source->source_username = $feed->get_author();
				$source->save();

				foreach($feed->get_items() as $feed_item)
				{
					//$latitude = $item->get_latitude();
					//$longitude = $item->get_longitude();

					if ($feed_item->get_link())
					{
						// Create a new Item
						$item = ORM::factory('item')
							->where( 'item_orig_id', '=', trim((string) $feed_item->get_link()) )
							->find();
						$item->service = 'rss';
						$item->source_id = $source->id; // the source we just saved above
						$item->item_orig_id = trim((string) $feed_item->get_link());
						$item->project_id = $this->project->id;
						$item->feed_id = $this->feed->id;
						$item->item_title = trim(strip_tags(str_replace('<', ' <', $feed_item->get_title())));
						$item->item_content = trim(strip_tags(str_replace('<', ' <', $feed_item->get_description())));
						$item->item_raw = $feed_item->get_description();
						$item->item_author = $feed->get_title();
						$item->item_locale = $locale;
						$item->item_date_pub = date("Y-m-d H:i:s", strtotime($feed_item->get_date()));
						$item->item_date_add = date("Y-m-d H:i:s", time());
						$item->save();

						// Save Link separately
						$link = ORM::factory('link')
							->where('link_full', '=', $feed_item->get_link())
							->find();

						if ( ! $link->loaded() )
						{
							$link->link = $feed_item->get_link();
							$link->link_full = $feed_item->get_link();
							$link->save();
						}

						if ( ! $item->has('links', $link))
						{
							$item->add('links', $link);
						}
					}
				}		
			}
		}
	}

	private function _is_url($url = NULL)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
}