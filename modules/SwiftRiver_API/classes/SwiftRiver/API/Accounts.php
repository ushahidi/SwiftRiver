<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Rivers API
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
class SwiftRiver_API_Accounts extends SwiftRiver_API {
	
	/**
	* Get account object for the logged in account.
	*
	* @return Array
	*/
	public function get_logged_in_account()
	{
		return $this->get('/accounts/me');
	}
	
	/**
	* Get account object for the logged in account.
	*
	* @return Array
	*/
	public function get_account_by_name($account_path)
	{
		return $this->get('/accounts',  array('account_path' => $account_path));
	}
	
	/**
	* Search accounts
	*
	* @return Array
	*/
	public function search($query)
	{
		return $this->get('/accounts',  array('q' => $query));
	}
}