<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Settings Controller
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
class Controller_Bucket_Settings extends Controller_Bucket {
	
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

		$this->template->header->title = $this->bucket['name'].' '.__("Settings");
		$this->template->header->js .= HTML::script('themes/default/media/js/collaborators.js');

		$this->template->content = View::factory('pages/bucket/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content)
			->bind('bucket_base_url', $this->bucket_base_url)
			->bind('bucket', $this->bucket);
		$this->template->content->nav = $this->get_nav();
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content->active = "options";
		$session = Session::instance();

		// Check for post
		if ($this->request->method() === "POST")
		{
			$bucket_name = trim($this->request->post('bucket_name'));

			// Check for updates to the bucket name
			if (Valid::not_empty($bucket_name) AND strcmp($bucket_name, $this->bucket['name']) !== 0)
			{
				$bucket_id = $this->bucket['id'];
				$parameters = array(
					'name' => $bucket_name,
					'public' => (bool) $this->request->post('bucket_publish')
				);

				// 
				if (($bucket = $this->bucket_service->modify_bucket($bucket_id, $parameters, $this->user)) != FALSE)
				{
					$session->set('message', __("Bucket settings successfully saved"));

					// Reload the settings page using the updated bucket name
					$this->redirect($bucket['url'].'/settings', 302);
				}
				else
				{
					$session->set('error', __("The bucket settings could not be updated"));
				}
			}
		}
		
		// Set the messages and/or error messages
		$this->template->content->set('message', $session->get('message'))
			->set('error', $session->get('error'));

		$this->settings_content = View::factory('pages/bucket/settings/display')
			->bind('bucket', $this->bucket)
			->bind('collaborators_view', $collaborators_view);
		
		// Collaboraotors view
		$collaborators_view = View::factory('/template/collaborators')
			->bind('fetch_url', $fetch_url)
			->bind('collaborator_list', $collaborators);
		$fetch_url = $this->bucket_base_url.'/collaborators';
		$collaborators = json_encode($this->bucket_service->get_collaborators($this->bucket['id']));
		$session->delete('message');
		$session->delete('error');
		
	}
	
	/**
	 * Bucket Settings Navigation
	 * 
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

		// Collaborators
		$nav[] = array(
			'id' => 'collaborators-navigation-link',
			'active' => 'collaborators',
			'url' => '/settings/collaborators',
			'label' => __('Collaborators')
		);
			
		return $nav;
	}
		
}