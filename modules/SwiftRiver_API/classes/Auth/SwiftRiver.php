<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Handles login of users when the system's authentication provider is SwiftRiver
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
class Auth_SwiftRiver extends Kohana_Auth {
	
	/**
	 * Perfomr login using user credentials.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  remember enable autologin
	 * @return  boolean
	 */
	protected function _login($username, $password, $remember)
	{
		try 
		{
			$token_params = array('username' => $username, 'password' => $password);
			$auth = SwiftRiver_Client::instance()->get_access_token('password', $token_params);
			$this->complete_login($auth);
		}
		catch (Exception $e)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function password($username)
	{
		return NULL;
	}
	
	public function check_password($user)
	{
		return FALSE;
	}
	
	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  mixed
	 */
	public function auto_login()
	{
		return FALSE;
	}
}