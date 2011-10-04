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
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		$this->template->header->tab_menu->active = 'facebook';
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
			->bind('errors', $errors);
		
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

		echo $this->_get_auth_url();
	}



	/**
	 * Get the Facebook Authorization URL
	 *
	 * @return  string $auth_url
	 */
	private function _get_auth_url()
	{
		include_once Kohana::find_file('vendor', 'facebook/facebook');

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
			$facebook = new Facebook(array(
				'appId' => $settings['application_id'],
				'secret' => $settings['application_secret'],
			));

			return $facebook->getLoginUrl(
				array(
					'redirect_uri' => URL::site(NULL, 'http').'facebook/auth'
				)
			);
		}
		else
		{
			return '#';
		}
	}	
}