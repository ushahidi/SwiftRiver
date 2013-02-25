<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_River_Settings extends Controller_River {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Only owners allowed here
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template->content = View::factory('pages/river/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content)
			->bind('river_base_url', $this->river_base_url)
			->bind('river', $this->river);
		$this->template->content->nav = $this->get_nav();
	}
	
	/**
	 * River Settings Navs
	 * 
	 * @param obj $river - the loaded river object
	 * @param string $active - the active menu
	 * @return	array $nav
	 */
	public static function get_nav()
	{
		$nav = array();

		// List
		$nav[] = array(
			'id' => 'options-navigation-link',
			'active' => 'options',
			'url' => '/settings',
			'label' => __('Options')
		);

		// Rules
		$nav[] = array(
			'id' => 'rules-navigation-link',
			'active' => 'rules',
			'url' => '/settings/rules',
			'label' => __('Rules')
		);

		// Collaborators
		$nav[] = array(
			'id' => 'collaborators-navigation-link',
			'active' => 'collaborators',
			'url' => '/settings/collaborators',
			'label' => __('Collaborators')
		);
			
		return $nav;
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content->active = "options";
		$this->template->header->title = $this->river['name'].' ~ '.__('Settings');
		$this->template->content->settings_content = View::factory('pages/river/settings/options');
		$this->template->content->settings_content->river = $this->river;
		
		$session = Session::instance();		
		if ($this->request->method() == "POST")
		{
			try 
			{
				$river_name = $this->request->post('river_name');
				$river_description = $this->request->post('river_description');
				$river_public = $this->request->post('river_public');
				$river = $this->riverService->update_river(
						$this->river['id'], 
						$river_name, 
						$river_description, 
						$river_public
					);
				
				// Redirect to the new URL with a success messsage
				Swiftriver_Messages::add_message(
					'success', 
					'Success', 
					__("River display settings were saved successfully.")
				);
				$this->redirect($this->riverService->get_base_url($river).'/settings', 302);
			}
			catch (SwiftRiver_API_Exception_BadRequest $e)
			{
				Kohana::$log->add(Log::DEBUG, var_export($e->get_errors(), TRUE));
				
				foreach ($e->get_errors() as $error) {
					if ($error['field'] == 'name' && $error['code'] == 'duplicate') {
						Swiftriver_Messages::add_message(
							'failure', 
							'Failure', 
							__("A river with the name ':name' already exists.", array(':name' => $river_name)),
							false
						);
					}
				}
				$this->redirect($this->river_base_url.'/settings', 302);
			}
		}
	}
}