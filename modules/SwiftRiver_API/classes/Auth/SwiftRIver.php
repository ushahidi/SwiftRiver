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
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/Client' );
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/GrantType/IGrantType' );
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/GrantType/Password' );
		
		$client_id = Kohana::$config->load('auth.client_id');
		$client_secret = Kohana::$config->load('auth.client_secret');
		$token_endpoint = Kohana::$config->load('auth.token_endpoint');
		
		$client = new OAuth2\Client($client_id, $client_secret, 1);
		$params = array('username' => $username, 'password' => $password);
		$response = $client->getAccessToken($token_endpoint, 'password', $params);//
		
		if ($response['code'] == 200)
		{
			$client->setAccessToken($response['result']['access_token']);
			$response = $client->fetch('http://localhost:8080/swiftriver-core/v1/accounts/me');
			
			if ($response['code'] == 200)
			{
				$this->complete_login($response['result']);
			}

			return TRUE;
		}
		
		return FALSE;
	}
	
	public function password($username)
	{
		return NULL;
	}
	
	public function check_password($user)
	{
		return FALSE;
	}
}