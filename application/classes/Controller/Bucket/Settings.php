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
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$session = Session::instance();

		// Check for post
		if ($this->request->method() === "POST")
		{
			$bucket_name = trim($this->request->post('bucket_name'));

			// Check for updates to the bucket name
			if (Valid::not_empty($bucket_name) AND strcmp($bucket_name, $this->bucket['name']) !== 0)
			{
				$bucket_id = $this->bucket['id'];
				$parameters = array('name' => $bucket_name);
				if (($bucket = $this->bucket_service->modify_bucket($bucket_id, $parameters, $this->user)) != FALSE)
				{
					$session->set('message', __("The bucket has been renamed to \":name\"",
						array(':name' => $bucket_name)));

					// Reload the settings page using the updated bucket name
					$this->redirect($bucket['url'].'/settings', 302);
				}
			}
			elseif
			(
				// Only the display settings are being updated
				Valid::not_empty($this->request->post('default_layout')) AND 
				Valid::not_empty($this->request->post('bucket_publish'))
			)
			{
				// Update parameters
				$parameters = array(
					// Use the current bucket name
					'name' => $this->bucket['name'],
					'default_layout' => $this->request->post('default_layout'),
					'public' => (bool) $this->request->post('bucket_publish')
				);
		
				// Get thet bucket id
				$bucket_id = $this->bucket['id'];
				
				// Update the display settings
				if (($bucket = $this->bucket_service->modify_bucket($bucket_id, $parameters, $this->user)) != FALSE)
				{
					$this->bucket = $bucket;
					$session->set('message', __("The display settings have been successfully updated"));
				}
				else
				{
					$session->set('error', __("The display settings could not be updated"));
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
		
}