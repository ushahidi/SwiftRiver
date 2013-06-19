<?php defined('SYSPATH') or die('No direct script access');

/**
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     Swiftriver - http://github.com/ushahidi/SwiftRiver
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */
class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {
	
	/**
	 * Generates a Response for all Exceptions without a specific override
	 *
	 * @return Response
	 */
	public function get_response()
	{
		// Log the exception
		Kohana_Exception::log($this);

		$response = Response::factory();
		
		$view = Swiftriver::get_base_error_view();
		$view->content = View::factory('pages/errors/404')
			->set('page', $this->request()->uri());

		$response->body($view->render());
		
		return $response;
	}
}