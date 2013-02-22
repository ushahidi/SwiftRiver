<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Abstract class for SwiftRiver API classes
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
abstract class SwiftRiver_API {

	 /**
	 * The client
	 * @var SwiftRiver_Client
	 */
	 private $client;

	 public function __construct(SwiftRiver_Client $client)
	 {
		 $this->client = $client;
	 }

	 /**
	 * Call any path, GET method
	 * Ex: $api->get('/v1/rivers/2/drops')
	 *
	 * @param   string  $path            the resource path
	 * @param   mixed   $parameters       GET parameters
	 * @return  array                     data returned
	 */
	 protected function get($path, $parameters = array(), $headers = array())
	 {
		 return $this->client->get($path, $parameters, $headers);
	 }

	/**
	 * Call any path, POST method
	 * Ex: $api->post('/v1/rivers/2/drops', array('count' => 10))
	 *
	 * @param   string  $path            the resource path
	 * @param   mixed   $parameters       POST parameters
	 * @return  array                     data returned
	 */
	protected function post($path, $parameters = array())
	{
		$headers = array(
			'Content-Type' => 'application/json;charset=UTF-8',
			'Accept' => 'application/json'
		);
		
		if ( ! empty($parameters))
		{
			$parameters = json_encode($parameters);
		}

		return $this->client->post($path, $parameters, $headers);
	}
	
	
	/**
	 * Call any path, PUT method
	 * Ex: $api->post('/v1/rivers/2', array('name' => 'New River Name'))
	 *
	 * @param   string  $path            the resource path
	 * @param   array   $parameters      PUT parameters
	 * @return  array                    data returned
	 */
	protected function put($path, $parameters = array())
	{
		$headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json'
		);
		
		if ( ! empty($parameters))
		{
			$parameters = json_encode($parameters);
		}

		return $this->client->put($path, $parameters, $headers);
	}
	
	/**
	 * Call any path, DELETE method
	 * Example: $api->delete('/v1/rivers/1')
	 *
	 * @param  string   $path The resource path
	 * @return bool
	 */
	protected function delete($path)
	{
		return $this->client->delete($path);
	}
}