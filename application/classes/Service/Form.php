<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Forms Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category    Services
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Service_Form extends Service_Base {
	
	/**
	 * Create a form
	 *
	 * @param   Array  $form_array
	 * @return Array
	 */
	public function create_form($form_array) 
	{
		$form_array = $this->api->get_forms_api()->create_form($form_array);		
		return $form_array;
	}
	
	/**
	 * Update the given form
	 *
	 * @return Array
	 */
	public function update_form($form_id, $new_name)
	{
		return $this->api->get_forms_api()->update_form($form_id, $new_name);
	}
	
	/**
	 * Deletes the form specified in $form_id
	 *
	 * @param int form_id
	 */
	public function delete_form($form_id)
	{
		$this->api->get_forms_api()->delete_form($form_id);
	}
	
	/**
	 * Return a form array with subscription and collaboration
	 * status populated for $querying_account
	 *
	 * @param Model_User $user
	 * @param Model_User $querying_account
	 * @return array
	 *
	 */
	public static function get_array($form, $querying_account)
	{
		$form['url'] = self::get_base_url($form);
		$form['is_owner'] = $form['account']['id'] == $querying_account['id'];
		
		// Is the querying account collaborating on the form?
		$form['collaborator'] = FALSE;
		
		return $form;
	}
	
	/**
	 * Return URL to the given Form
	 *
	 * @return	Array
	 */
	public static function get_base_url($form)
	{
		return URL::site($form['account']['account_path'].'/form/'.URL::title($form['name']));
	}
	
	/**
	 * Update the given form field
	 *
	 * @return Array
	 */
	public function update_field($form_id, $field_id, $field_array)
	{
		return $this->api->get_forms_api()->update_field($form_id, $field_id, $field_array);
	}
	
	/**
	 * Delete field
	 *
	 * @param   string  $form_id
	 * @param   string  $field_id	
	 * @return Array
	 */
	public function delete_field($form_id, $field_id)
	{
		$this->api->get_forms_api()->delete_field($form_id, $field_id);
	}
}