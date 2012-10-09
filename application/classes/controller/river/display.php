<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Display Settings Controller
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
class Controller_River_Display extends Controller_River_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->river->river_name.' ~ '.__('Display Settings');
		
		$this->active = 'display';
		$this->settings_content = View::factory('pages/river/settings/display');
		$this->settings_content->river = $this->river;
		
		$session = Session::instance();		
		if ($this->request->method() == "POST")
		{
			try
			{
				$this->river->river_name = $this->request->post('river_name');
				$this->river->river_public = $this->request->post('river_public');
				$this->river->default_layout = $this->request->post('default_layout');
				$this->river->save();
				
				// Force refresh of cached rivers
		        Cache::instance()->delete('user_rivers_'.$this->user->id);
				
				// Redirect to the new URL with a success messsage
				$session->set("messages", array(__("Display settings were saved successfully.")));
				$this->request->redirect($this->river->get_base_url().'/settings/display');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->settings_content->errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$this->settings_content->errors = array(
					__("A river with the name ':name' name already exists", 
						array(":name" => $this->request->post('river_name'))
					));
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
		$this->river->set_token();
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json');
		echo json_encode($this->river->public_token);
	}
	
}