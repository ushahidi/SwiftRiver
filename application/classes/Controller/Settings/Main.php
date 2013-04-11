<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_Settings_Main extends Controller_Swiftriver {
	
	// Active settings menu
	protected $active;

	/**
	 * Access privileges for this controller and its children
	 */
	public $auth_required = 'admin';
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->content = View::factory('pages/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content);
	}
	
	/**
	 * List all the available settings
	 *
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->header->title = __('Application Settings');
		$this->settings_content = View::factory('pages/settings/main')
		    ->bind('action_url', $action_url);

		$this->active = 'main';	
		$action_url = URL::site('settings/main/manage');
		
		// Setting items
		$settings = array(
			'site_name' => '',
			'site_locale' => '',
			'email_domain' => '',
			'comments_email_domain' => '',
			'public_registration_enabled' => '',
			'anonymous_access_enabled' => '',
			'general_invites_enabled' => '',
			'default_river_lifetime' => '',
			'river_expiry_notice_period' => '',
			'default_river_quota' => '',
			'default_river_drop_quota' => ''
		);

		if ($this->request->post())
		{
			// Setup validation for the application settings
			$validation = Validation::factory($this->request->post())
				->rule('site_name', 'not_empty')
				->rule('site_locale', 'not_empty')
				->rule('email_domain', 'not_empty')
				->rule('comments_email_domain', 'not_empty')
				->rule('default_river_lifetime', 'not_empty')
				->rule('default_river_lifetime', 'digit')
				->rule('river_expiry_notice_period', 'not_empty')
				->rule('river_expiry_notice_period', 'digit')
				->rule('form_auth_token', array('CSRF', 'valid'))
				->rule('default_river_quota', 'digit')
				->rule('default_river_drop_quota', 'digit');
			
			if ($validation->check())
			{
				// Set the setting key values
				$settings = array(
					'site_name' => $this->request->post('site_name'),
					'site_locale' => $this->request->post('site_locale'),
					'email_domain' => $this->request->post('email_domain'),
					'comments_email_domain' => $this->request->post('comments_email_domain'),
					'public_registration_enabled' => $this->request->post('public_registration_enabled') == 1,
					'anonymous_access_enabled' => $this->request->post('anonymous_access_enabled') == 1,
					'general_invites_enabled' => $this->request->post('general_invites_enabled') == 1,
					'default_river_lifetime' => $this->request->post('default_river_lifetime'),
					'river_expiry_notice_period' => $this->request->post('river_expiry_notice_period'),
					'default_river_quota' => $this->request->post('default_river_quota'),
					'default_river_drop_quota' => $this->request->post('default_river_drop_quota')
				);

				// Update the settings
				Swiftriver::update_settings($settings);
				
				$this->settings_content->set('messages', 
					array(__('The site settings have been updated.')));
			}
			else
			{
				$this->settings_content->set('errors', $validation->errors('user'));
			}
		}
		
		$this->settings_content->settings = Swiftriver::get_settings(array_keys($settings));
	}

}
