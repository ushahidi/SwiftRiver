<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * RiverID authorization library. Handles login of users when the system's
 * authentication provider is RiverID
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
class Auth_RiverID extends Kohana_Auth_ORM { 
	
	/**
	 * Logs a user in.
	 *
	 * @param   string   email
	 * @param   string   password
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	protected function _login($email, $password, $remember) 
	{
		$riverid_api = RiverID_API::instance();
		
		// Fallback to local auth if user is in the exemption list
		if (in_array($email, Kohana::$config->load('auth.exempt')))
			return parent::_login($email, $password, $remember);
		
		// Check if the email is registered on RiverID
		if ($riverid_api->is_registered($email))
		{
			// Success! Proceed to sign in into RiverID
			$login_response = $riverid_api->signin($email, $password);
			
			if ($login_response AND $login_response['status'])
			{
			
				// Get the user object that matches the provided email and RiverID
				$user = ORM::factory('user')
							 ->where('email', '=', $email)
							 ->where('riverid', '=', $login_response['user_id'])
							 ->find();
							 
				// User does not exist locally but authenticates via RiverID, create user
				if ( ! $user->loaded())
				{
					// Check if the email is already registered locally
					// If so, this will simply append a riverid
					$user = ORM::factory('user')
								 ->where('email', '=', $email)
								 ->find();
								
					// Only auto register if the site allows it
					if
					( 
						! (bool) Model_Setting::get_setting('public_registration_enabled')
						AND ! $user->loaded()
					)
					{
						return FALSE;
					}
					
					$user->username = $user->email = $email;
					$user->riverid = $login_response['user_id'];
					$user->save();
					
					// Allow the user be able to login immediately
					$login_role = ORM::factory('role',array('name'=>'login'));
					
					if ( ! $user->has('roles', $login_role))
					{
						$user->add('roles', $login_role);
					}
				} 
				
				// User exists locally and authenticates via RiverID so complete the login
				if ($user->has('roles', ORM::factory('role', array('name' => 'login'))))
				{
					if ($remember === TRUE)
					{
						// Token data
						$data = array(
							'user_id'    => $user->id,
							'expires'    => time() + $this->_config['lifetime'],
							'user_agent' => sha1(Request::$user_agent),
						);
				
						// Create a new autologin token
						$token = ORM::factory('user_token')
									->values($data)
									->create();
				
						// Set the autologin cookie
						Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
					}
				
					// Finish the login
					$this->complete_login($user);
				
					return TRUE;
				}	            
			
			}	        
		}
		
		return FALSE;
	}
	

}