<?php defined('SYSPATH') OR die('No direct script access');
/**
 * Custom Request wrapper to guard against CSRF for Ajax requests
 * The wrapper adds an 'X-CSRF-Token' header if a HTTP POST request
 * is made via XHR.
 *
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @author     Usahidi Team <team(at)ushahidi.com>
 * @copyright  (c) 2008-2012 Ushahidi Team
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Request extends Kohana_Request {
	
	/**
	 * Processes the request, executing the controller action that handles this
	 * request, determined by the [Route].
	 *
	 * 1. Before the controller action is called, the [Controller::before] method
	 * will be called.
	 * 2. Next the controller action will be called.
	 * 3. After the controller action is called, the [Controller::after] method
	 * will be called.
	 *
	 * By default, the output from the controller is captured and returned, and
	 * no headers are sent.
	 *
	 *     $request->execute();
	 *
	 * @return  Response
	 * @throws  Request_Exception
	 * @throws  HTTP_Exception_404
	 * @uses    [Kohana::$profiling]
	 * @uses    [Profiler]
	 */
	public function execute()
	{
		if ( ! $this->_route instanceof Route)
		{
			throw new HTTP_Exception_404('Unable to find a route to match the URI: :uri', array(
				':uri' => $this->_uri,
			));
		}

		if ( ! $this->_client instanceof Request_Client)
		{
			throw new Request_Exception('Unable to execute :uri without a Kohana_Request_Client', array(
				':uri' => $this->_uri,
			));
		}

		// Add custom header for CSRF protection where an Ajax
		// request is made via HTTP POST
		if ($this->method() === 'POST' AND $this->is_ajax())
		{
			$this->headers('X-CSRF-Token', CSRF::token());
		}

		return $this->_client->execute($this);
	}
}