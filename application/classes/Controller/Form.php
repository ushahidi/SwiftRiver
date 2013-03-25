<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Controller
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
class Controller_Form extends Controller_Swiftriver {

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the form id form the url
		$form_id = $this->request->param('name');
		

		// Find the matching form from the visited account's forms.
		foreach($this->visited_account['forms'] as $form)
		{
			if ($form['id'] == $form_id)
			{
				$this->form = $this->form_service->get_array($form, $this->user);
			}
		}
		
		if (!isset($this->form))
			throw new HTTP_Exception_404();
		
		$this->owner = $this->form['is_owner'];
		$this->collaborator = $this->form['collaborator'];
		
		// If this river is not public and no ownership...
		if ( ! $this->owner AND 
			 ! $this->collaborator)
		{
			throw new HTTP_Exception_403();
		}

		$this->form_base_url = $this->form_service->get_base_url($this->form);
	}

	
	public function action_fields()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
			case "PUT":
				$field_id = intval($this->request->param('id', 0));
				$field_array = json_decode($this->request->body(), TRUE);
				$field = $this->form_service->update_field($this->form['id'], $field_id, $field_array);
			
				echo json_encode($field);
			break;
			
			case "DELETE":
				$field_id = intval($this->request->param('id', 0));
				$field_array = json_decode($this->request->body(), TRUE);
				$field = $this->form_service->delete_field($this->form['id'], $field_id);
			break;
		}
		
	}
}