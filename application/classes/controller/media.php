<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Media Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Media extends Controller {

	/**
	 * Header Javascript + Hook
	 *
	 * @return	void
	 */
	public function action_js()
	{
		echo View::factory('common/js/header')
			->bind('base_url', $base_url);

		$base_url = URL::site();

		// SwiftRiver Plugin Hook -- Add Custom JS
		Swiftriver_Event::run('swiftriver.header.js');
	}
	
	/**
	 * Header CSS + Hook
	 *
	 * @return	void
	 */
	public function action_css()
	{
		// SwiftRiver Plugin Hook -- Add Custom JS
		Swiftriver_Event::run('swiftriver.header.css');
	}
}