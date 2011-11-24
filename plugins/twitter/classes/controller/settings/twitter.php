<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Twitter extends Controller_Settings_Main {

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		$this->template->header->tab_menu->active = 'twitter';
	}
	
	/**
	 * List all the available settings
	 *
	 * @param   string $page - page uri
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('twitter/settings')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('auth_url', $auth_url)
			->bind('authorized', $authorized);
		
		// save the data
		if ($_POST)
		{
			$settings = ORM::factory('twitter_setting');
			$post = $settings->validate($_POST);
			if ($post->check())
			{
				$settings = ORM::factory('twitter_setting')
					->where('key', '=', 'consumer_key')
					->find();
				$settings->key = 'consumer_key';
				$settings->value = $post['consumer_key'];
				$settings->save();
				
				$settings = ORM::factory('twitter_setting')
					->where('key', '=', 'consumer_secret')
					->find();
				$settings->key = 'consumer_secret';
				$settings->value = $post['consumer_secret'];
				$settings->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('settings/twitter');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('settings');
			}
		}
		else
		{
			$settings = ORM::factory('twitter_setting')->find_all();
			foreach ($settings as $setting)
			{
				$post[$setting->key] = $setting->value;
			}

			// Are we already authorized?
			if ( ! empty($post['oauth_token']) AND ! empty($post['oauth_token_secret']) )
			{
				$authorized = TRUE;
			}
			else
			{
				$authorized = FALSE;
			}

			// Generate Twitter Auth URL
			$auth_url = $this->_get_auth_url();
		}
	}

	/**
	 * Get the Twitter Authorization URL
	 *
	 * @return  string $auth_url
	 */
	private function _get_auth_url()
	{
		include_once Kohana::find_file('vendor', 'twitteroauth/twitteroauth');

		// Get Twitter Settings
		$settings = array();
		$params = ORM::factory('twitter_setting')->find_all();
		foreach ($params as $param)
		{
			$settings[$param->key] = $param->value;
		}

		if (isset($settings['consumer_key']) AND isset($settings['consumer_secret']))
		{
			$connection = new TwitterOAuth($settings['consumer_key'],
				$settings['consumer_secret']);
			
			$request_token = $connection->getRequestToken();

			$this->session->set('oauth_token', $request_token['oauth_token']);
			$this->session->set('oauth_token_secret', $request_token['oauth_token_secret']);

			return $connection->getAuthorizeURL($request_token);
		}
		else
		{
			return '#';
		}
	}
}