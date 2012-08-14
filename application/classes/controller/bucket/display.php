<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Display Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Bucket_Display extends Controller_Bucket_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->bucket->bucket_name.' ~ '.__('Display Settings');
		
		$this->active = 'display';
		$this->settings_content = View::factory('pages/bucket/settings/display');
		$this->settings_content->bucket = $this->bucket;
		
		$session = Session::instance();		
		if ($this->request->method() == "POST")
		{
			try
			{
				$this->bucket->bucket_name = $this->request->post('bucket_name');
				$this->bucket->bucket_publish = $this->request->post('bucket_publish');
				$this->bucket->default_layout = $this->request->post('default_layout');
				$this->bucket->save();
				
				// Force refresh of cached buckets
				Cache::instance()->delete('user_buckets_'.$this->user->id);
				
				// Redirect to the new URL with a success messsage
				$session->set("messages", array(__("Display settings were saved successfully.")));
				$this->request->redirect($this->bucket->get_base_url($this->bucket).'/settings/display');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->settings_content->errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$this->settings_content->errors = array(
					__("A bucket with the name ':name' name already exists", 
						array(":name" => $this->request->post('bucket_name')))
				);
			}
		}
		
		// Check for messages
		$this->settings_content->messages = $session->get('messages');
		$session->delete('messages');
	}

	/**
	 * Create, save, and echo a new key for this bucket in JSON
	 */
	public function action_create_token()
	{
		$this->bucket->set_token();
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json');
		echo json_encode($this->bucket->public_token);
	}
}
