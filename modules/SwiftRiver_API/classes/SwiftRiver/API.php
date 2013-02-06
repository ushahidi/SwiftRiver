<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver API Client
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
class SwiftRiver_API {
	
	private static $_instance;
	
	private $auth;
	private $oauth_client;
	private $base_url;
	private $token_endpoint;
	
	private function __construct()
	{
		$client_id = Kohana::$config->load('auth.client_id');
		$client_secret = Kohana::$config->load('auth.client_secret');
		$this->token_endpoint = Kohana::$config->load('auth.token_endpoint');
		$this->base_url = Kohana::$config->load('swiftriver.base_url');
		
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/Client' );
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/GrantType/IGrantType' );
		include_once Kohana::find_file( 'vendor', 'PHP-OAuth2/GrantType/Password' );
		
		$this->oauth_client = new OAuth2\Client($client_id, $client_secret, 1);
	}
	
	/**
	 * Returns singleton class instance.
	 *
	 * @return SwiftRiver_API
	 */
	public static function instance()
	{
		if ( ! self::$_instance)
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
	/**
	 * Obtain an access token.
	 *
	 * @return Access Token
	 */
	public function get_access_token($grant_type, $params)
	{
		$response = $this->oauth_client->getAccessToken($this->token_endpoint, $grant_type, $params);
		
		if ($response['code'] != 200)
		{
			throw new SwiftRiver_API_Exception("Authorization Failed");
		}
		
		return $response['result'];
	}
	
	/**
	 * Set the access token.
	 *
	 * @return void
	 */
	public function set_access_token($access_token)
	{
		$this->oauth_client->setAccessToken($access_token);
	}

	/**
	 * Get account object for the logged in account.
	 *
	 * @return Array
	 */
	public function get_logged_in_account()
	{
		return $this->_call('/accounts/me');
	}
	
	/**
	 * Get account object for the logged in account.
	 *
	 * @return Array
	 */
	public function get_account_by_name($account_path)
	{
		return $this->_call('/accounts',  array('account_path' => $account_path));
	}
	
	/**
	 * Get river with the given id
	 *
	 * @return Array
	 */
	public function get_river_by_id($id)
	{
		return $this->_call('/rivers/'.$id);
	}

	/**
	 * Send request to an api endpoint
	 *
	 * @param   string   url
	 * @param   array    params
	 * @return  mixed    The api response.
	 */
	private function _call($path, $params = array()) {
		
		$response = $this->oauth_client->fetch($this->base_url.$path, $params);
			
		if ($response['code'] != 200)
		{
			Kohana::$log->add(Log::DEBUG, var_export($response, TRUE));
			throw new SwiftRiver_API_Exception($response['result']['error_description']);
		}

		return $response['result'];
		
	}
	
}