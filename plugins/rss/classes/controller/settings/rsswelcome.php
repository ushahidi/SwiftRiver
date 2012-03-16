<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * RSS Welcome Page Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_RSSWelcome extends Controller_Settings_Main {
	
	
	/**
	 * River collaborators restful api
	 * 
	 * @return	void
	 */	
	public function action_index()
	{
		$this->template->header->title = __('RSS Starter URLs');
		$this->settings_content = View::factory('settings/welcome');
		$this->active = 'rsswelcome';
		
		$urls = Model_Setting::get_setting(RSS_Util::SETTING_KEY);
		if (!$urls)
		{
			$urls = array();
		}
		else
		{
			$urls = json_decode($urls, TRUE);
		}
		
		// Format the urls for the listing bootstrap
		$bootstrap = array();
		foreach ($urls as $url => $title)
		{
			$bootstrap[] = array(
				'id' => hash('sha256', $url),
				'url' => $url,
				'title' => $title
			);
		}
		
		$this->settings_content->urls = json_encode($bootstrap);
	}
	
	/**
	 * Restful api
	 * 
	 * @return	void
	 */
	public function action_urls()
	{
		$this->auto_render = FALSE;
		
		// Initialize urls if the setting doesn't exist otherwise json decode
		$urls = Model_Setting::get_setting(RSS_Util::SETTING_KEY);
		if (!$urls) 
		{
			$urls = array();
		}
		else
		{
			$urls = json_decode($urls, TRUE);
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				$id = $this->request->param('id');
				
				foreach ($urls as $url => $title)
				{
					if ($id == hash('sha256', $url))
					{
						unset($urls[$url]);
						Model_Setting::update_setting(RSS_Util::SETTING_KEY, json_encode($urls));
						break;
					}
				}
				
			break;
			case "POST":
				$url_obj = json_decode($this->request->body(), TRUE);
				$url = trim($url_obj['url']);
				if (substr($url, 0, 4) != 'http')
				{
					$url = 'http://' . $url;
				}
				if ( empty($url) OR ! Valid::url($url) OR ! ($feed = RSS_Util::validate_feed_url($url)))
				{
					$this->response->status(400);
					echo __('The URL provided is invalid');
					return;
				}
				// Get the real feed url
				$url = $feed['value'];
				
				$urls = Model_Setting::get_setting(RSS_Util::SETTING_KEY);
				
				// Initialize urls if the setting doesn't exist otherwise json decode
				if (!$urls) {
					$urls = array();
				}
				else
				{
					$urls = json_decode($urls, TRUE);
				}
				
				if ( ! array_key_exists($url, $urls)) 
				{
					$urls[$url] = $feed['title'];
					Model_Setting::update_setting(RSS_Util::SETTING_KEY, json_encode($urls));
					echo json_encode(array(
						'id' => hash('sha256', $url),
						'url' => $url,
						'title' => $feed['title']
					));
				}
				else
				{
					$this->response->status(400);
					echo __('The URL provided is a duplicate');
				}
				
			break;
		}
	}

}
