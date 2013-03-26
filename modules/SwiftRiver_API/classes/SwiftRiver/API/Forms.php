<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Forms API
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class SwiftRiver_API_Forms extends SwiftRiver_API {
	
	/**
	 * Create a form
	 *
	 * @param   string  $form_array
	 * @return Array
	 */
	public function create_form($form_array) {		
		return $this->post('/forms', $form_array);
	}
	
	/**
	 * Update the given form
	 *
	 * @return Array
	 */
	public function update_form($form_id, $new_name)
	{
		$request_body = array(
			"name" => $new_name
		);

		return $this->put('/forms/'.$form_id, $request_body);
	}
	
	/**
	 * Deletes the form specified in $form_id
	 *
	 * @param int form_id
	 */
	public function delete_form($form_id)
	{
		return $this->delete('/forms/'.$form_id);
	}
	
	/**
	 * Create a form field
	 *
	 * @param   string  $form_id
	 * @param   string  $field_id
	 * @param   string  $field_array
	 * @return Array
	 */
	public function create_field($form_id, $field_array)
	{
		return $this->post('/forms/'.$form_id.'/fields', $field_array);
	}
	
	/**
	 * Modify a form field
	 *
	 * @param   string  $form_id
	 * @param   string  $field_id
	 * @param   string  $field_array
	 * @return Array
	 */
	public function update_field($form_id, $field_id, $field_array)
	{
		return $this->put('/forms/'.$form_id.'/fields/'.$field_id, $field_array);
	}
	
	/**
	 * Delete field
	 *
	 * @param   long  $form_id
	 * @param   long  $field_id
	 * @return Array
	 */
	public function delete_field($form_id, $field_id)
	{
		return $this->delete('/forms/'.$form_id.'/fields/'.$field_id);
	}
}