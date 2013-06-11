<?php defined('SYSPATH') or die('No direct access');

/**
 * Overrides the default exception handler
 *
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
class Kohana_Exception extends Kohana_Kohana_Exception {
 
	/**
	 * Exception handler, logs the exception and generates a 
	 * Response object for display
	 *
	 * @param   Exception $e
	 * @return  boolean
	 */
	public static function _handler(Exception $e)
	{
		// Generate the response
		$response = Response::factory();

		$view = View::factory('template/layout')
			->set('footer', View::factory('template/footer'))
			->set('content', View::factory('pages/errors/default'))
			->bind('header', $header);

		// Header
		// Params for the <head> section
		$dashboard_url =  URL::site('/');
		$_head_params = array(
			'meta' => "",
			'js'=> "",
			'css' => "",
			'messages' => json_encode(array()),
			'dashboard_url' => $dashboard_url,
		);
		$header = View::factory('template/header')
			->set('show_nav', TRUE)
			->set('site_name', Swiftriver::get_setting('site_name'))
			->set($_head_params)
			->bind('nav_header', $nav_header);
		
		// Navigation header
		$nav_header = View::factory('template/nav/header')
			->set('user', NULL)
			->set('anonymous', FALSE)
			->set('dashboard_url', $dashboard_url)
			->set('controller', NULL);
		
		$response->body($view->render());

		return $response;
	}
}