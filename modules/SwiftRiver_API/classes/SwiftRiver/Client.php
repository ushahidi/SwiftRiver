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
class SwiftRiver_Client {
	
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
	 * @return SwiftRiver_Client
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
			throw new SwiftRiver_API_Exception_Authorization("Authorization Failed");
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
		$this->oauth_client->setAccessTokenType(1);
	}
	
	/**
	* Get the accounts API
	*
	* @return  SwiftRiver_API_Accounts
	*/
	public function get_accounts_api()
	{
		if ( ! isset($this->apis['accounts']))
		{
			$this->apis['accounts'] = new SwiftRiver_API_Accounts($this);
		}
		
		return $this->apis['accounts'];
	}
	
	/**
	* Get the rivers API
	*
	* @return  SwiftRiver_API_Rivers
	*/
	public function get_rivers_api()
	{
		if ( ! isset($this->apis['rivers']))
		{
			$this->apis['rivers'] = new SwiftRiver_API_Rivers($this);
		}
		
		return $this->apis['rivers'];
	}
	
	/**
	* Get the buckets API
	*
	* @return  SwiftRiver_API_Buckets
	*/
	public function get_buckets_api()
	{
		if ( ! isset($this->apis['buckets']))
		{
			$this->apis['buckets'] = new SwiftRiver_API_Buckets($this);
		}
		
		return $this->apis['buckets'];
	}
	
	/**
	 * Gets and returns the drops API
	 *
	 * @return SwiftRiver_API_Drops
	 */
	public function get_drops_api()
	{
		if ( ! isset($this->apis['drops']))
		{
			$this->apis['drops'] = new SwiftRiver_API_Drops($this);
		}
		
		return $this->apis['drops'];
	}
	
	
	/**
	 * Send request to an api endpoint
	 *
	 * @param   string   url
	 * @param   mixed    params
	 * @param   mixed    method
	 * @param   mixed    headers	 	 
	 * @return  mixed    The api response.
	 */
	private function _call($path, $params = array(), $method = "GET", $headers = array())
	{
		try 
		{
			$response = $this->oauth_client->fetch($this->base_url.$path, $params, $method, $headers);

			$exception_map = array(
				400 => "SwiftRiver_API_Exception_BadRequest",
				403 => "SwiftRiver_API_Exception_Forbidden",
				404 => "SwiftRiver_API_Exception_NotFound"
			);
		
			if (in_array($response['code'], array_keys($exception_map)))
			{
				Kohana::$log->add(Log::DEBUG, var_export($response, TRUE));
				throw new $exception_map[$response['code']]($response['result']);
			}
			else if ($response['code'] == 401)
			{
				throw new SwiftRiver_API_Exception_Authorization($response['result']['error_description']);
			}
			else if ($response['code'] != 200)
			{
				Kohana::$log->add(Log::DEBUG, var_export($response, TRUE));
				throw new SwiftRiver_API_Exception_Unknown($response['result']);
			}
		
			return $response['result'];		
		}
		catch (OAuth2\Exception $e)
		{
			Kohana::$log->add(Log::ERROR, "OAuth2\Exception :message", array(':message' => $e->getMessage()));
			throw new SwiftRiver_API_Exception_Authorization($e->getMessage());
		}	
	}
	
	/**
	 * Call any path, GET method
	 * Ex: $api->get('/v1/rivers/2/drops')
	 *
	 * @param   string  $path            the resource path
	 * @param   mixed   $parameters       GET parameters
	 * @return  array                     data returned
	 */
	public function get($path, $parameters = array(), $headers = array())
	{
		return $this->_call($path, $parameters, "GET", $headers);
	}

	/**
	 * Call any path, PUT method
	 * Ex: $api->put('/v1/rivers/2/channels/1')
	 *
	 * @param   string  $path            the resource path
	 * @param   mixed   $parameters       DELETE parameters
	 * @return  array                     data returned
	 */
	public function post($path, $parameters = array(), $headers = array())
	{
		return $this->_call($path, $parameters, "POST", $headers);
	}
	
	/**
	 * Call any path, PUT method
	 * Example: $api->put('/v1/rivers/1')
	 *
	 * @param  string   $path The resource path
	 * @param  array    $parameters PUT parameters
	 * @return array
	 */
	public function put($path, $parameters = array(), $headers = array())
	{
		return $this->_call($path, $parameters, "PUT", $headers);
	}

	/**
	 * Call any path, DELETE method
	 * Example: $api->delete('/v1/rivers/1')
	 *
	 * @param  string   $path The resource path
	 * @return bool
	 */
	public function delete($path)
	{
		$this->_call($path, NULL, "DELETE");
	}
}