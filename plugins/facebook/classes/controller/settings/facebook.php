<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Facebook Settings Controller
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
class Controller_Settings_Facebook extends Controller_Settings_Main {
	
	/**
	 * Facebook Connection
	 */	
	private $facebook = NULL;

	/**
	 * @return	void
	 */
	public function before()
	{
		include_once Kohana::find_file('vendor', 'facebook/facebook');

		// Execute parent::before first
		parent::before();
		$this->template->header->tab_menu->active = 'facebook';

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
		}
	}
	
	/**
	 * List all the available settings
	 *
	 * @param   string $page - page uri
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('facebook/settings')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('auth_url', $auth_url)
			->bind('authorized', $authorized)
			->bind('access_name', $access_name);
		
		// save the data
		if ($_POST)
		{
			$settings = ORM::factory('facebook_setting');
			$post = $settings->validate($_POST);
			if ($post->check())
			{
				$settings = ORM::factory('facebook_setting')
					->where('key', '=', 'application_id')
					->find();
				$settings->key = 'application_id';
				$settings->value = $post['application_id'];
				$settings->save();

				$settings = ORM::factory('facebook_setting')
					->where('key', '=', 'application_secret')
					->find();
				$settings->key = 'application_secret';
				$settings->value = $post['application_secret'];
				$settings->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('settings/facebook');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('settings');
			}
		}
		else
		{
			$settings = ORM::factory('facebook_setting')->find_all();
			foreach ($settings as $setting)
			{
				$post[$setting->key] = $setting->value;
			}
		}

		$authorized = FALSE;

		if ($this->facebook)
		{
			// Get the Access Token from Facebook
			$access_token = $this->facebook->getAccessToken();

			// Now use the access token to test the connection
			$this->facebook->setAccessToken($access_token);

			// Get the user associated with the token
			$user = $this->facebook->getUser();
			if ($user)
			{
				// Get User ID
				try
				{
					$user_profile = $this->facebook->api('/me');

					// Save Facebook Auth Settings
					$settings = ORM::factory('facebook_setting')
						->where('key', '=', 'access_token')
						->find();
					$settings->key = 'access_token';
					$settings->value = $access_token;
					$settings->save();

					// Save Facebook User ID
					$settings = ORM::factory('facebook_setting')
						->where('key', '=', 'access_user_id')
						->find();
					$settings->key = 'access_user_id';
					$settings->value = $user_profile["id"];
					$settings->save();

					// Save Facebook User Name
					$settings = ORM::factory('facebook_setting')
						->where('key', '=', 'access_name')
						->find();
					$settings->key = 'access_name';
					$settings->value = $access_name = $user_profile["name"];
					$settings->save();
					
					$authorized = TRUE;
				}
				// Failed - Reauthorize
				catch (Exception $e)
				{
					$user = null;

					$authorized = FALSE;
				}
			}
		}

		$auth_url = $this->_get_auth_url();
	}



	/**
	 * Get the Facebook Authorization URL
	 *
	 * @return  string $auth_url
	 */
	private function _get_auth_url()
	{	
		if ($this->facebook)
		{
			return $this->facebook->getLoginUrl(
				array(
					'scope' => 'offline_access,read_stream,user_likes,user_location,user_website,read_friendlists'
				)
			);
		}
		else
		{
			return '#';
		}
	}	
}