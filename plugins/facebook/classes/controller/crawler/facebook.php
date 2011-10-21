<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Facebook Crawler Controller
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
class Controller_Crawler_Facebook extends Controller_Crawler_Main {

	/**
	 * Facebook Connection
	 */	
	private $facebook = NULL;

	/**
	 * Facebook User
	 */	
	private $fb_user = NULL;	

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
		include_once Kohana::find_file('vendor', 'facebook/facebook');

		// Execute parent::before first
		parent::before();

		// Get Facebook Settings
		$settings = array();
		$params = ORM::factory('facebook_setting')->find_all();
		foreach ($params as $param)
		{
			$settings[$param->key] = $param->value;
		}

		if (isset($settings['application_id']) AND isset($settings['application_secret']))
		{
			// Create our Application instance (replace this with your appId and secret).
			$this->facebook = new Facebook(array(
				'appId' => $settings['application_id'],
				'secret' => $settings['application_secret'],
				'cookie' => true,
			));

			if ( isset($settings['access_token']) AND ! empty($settings['access_token']) )
			{
				// Now use the access token to test the connection
				$this->facebook->setAccessToken($settings['access_token']);
				// Get the user associated with the token
				$this->fb_user = $this->facebook->getUser();
			}
		}
	}

	public function action_index()
	{
		if ($this->fb_user)
		{
			$feeds = ORM::factory('feed')
				->where('service', '=', 'facebook')
				->where('feed_enabled', '=', '1')
				->find_all();
			
			foreach ($feeds as $feed)
			{
				$this->feed = $feed;
				$this->project = $feed->project;

				$options = array();

				// Get the Search parameters
				$params = $feed->feed_options->find_all();
				foreach ($params as $param)
				{
					if ($param->value)
					{
						$options[$param->key] = $param->value;
					}
				}

				if (count($options))
				{
					$this->_search($options);
				}
			}
		}
	}

	/**
	 * Build Facebook Search Query
	 * @param array $options
	 * @return void
	 */
	private function _search($options = array())
	{	
		foreach ($options as $key => $value)
		{
			// Keywords
			if ($key == 'keywords')
			{
				try
				{
					$results = $this->facebook->api('/search?q='.urlencode($value).'&type=post&limit=100');
					if (isset($results['data']))
					{
						foreach ($results['data'] as $post)
						{
							$this->_save($post);
						}
					}
				}
				catch (Exception $e)
				{
					Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
				}
			}
		}		
	}


	/**
	 * Save A Facebook Message/Story
	 * @param object $post
	 * @return void
	 */
	private function _save($post = NULL)
	{
		if (count($post))
		{
			// First Save Source/Author
			// Create a new Source
			$source = ORM::factory('source')
				->where( 'source_orig_id', '=', trim((string) $post['from']['id']) )
				->find();
			if ( ! $source->loaded() )
			{
				$source->source_orig_id = trim((string) $post['from']['id']);
				$source->service = 'facebook';
				$source->source_name = trim((string) $post['from']['name']);
				$source->save();
			}

			// Create a new Item
			$item = ORM::factory('item')
				->where( 'item_orig_id', '=', trim((string) $post['id']) )
				->find();
			if ( ! $item->loaded() )
			{
				$item->service = 'facebook';
				$item->source_id = $source->id; // the source we just saved above
				$item->item_orig_id = trim((string) $post['id']);
				$item->project_id = $this->project->id;
				$item->feed_id = $this->feed->id;
				if (isset($post['story']))
				{
					$item->item_title = trim($post['story']);
				}
				elseif (isset($post['message']))
				{
					$item->item_title = trim($post['message']);
				}

				// If we don't have a description
				// we'll use the [message] as the description
				if (isset($post['description']))
				{
					$item->item_content = trim(strip_tags(str_replace('<', ' <', $post['description'])));
					$item->item_raw = $post['description'];
				}
				else
				{
					$item->item_title = NULL;
					$item->item_content = trim(strip_tags(str_replace('<', ' <', $post['message'])));
					$item->item_raw = $post['message'];
				}
				$item->item_author = trim((string) $post['from']['name']);
				$item->item_locale = 'en';
				$item->item_date_pub = date("Y-m-d H:i:s", strtotime($post['created_time']));
				$item->item_date_add = date("Y-m-d H:i:s", time());
				$item->save();
			}

			// Get attached link
			if ( isset($post['link']) AND ! empty($post['link']) )
			{

				$full_link = Links::full($post['link']);
				if ( $post['link'] == $full_link OR 
					! $full_link )
				{
					$full_link = $post['link'];
				}

				$link = ORM::factory('link')
					->where('link_full', '=', $full_link)
					->find();

				if ( ! $link->loaded() )
				{
					$link->link = $post['link'];
					$link->link_full = $full_link;
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