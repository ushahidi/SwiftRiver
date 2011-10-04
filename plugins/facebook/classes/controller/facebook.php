<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Facebook Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Facebook extends Controller {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * Facebook Oauth Callback
	 * 
	 * @return	void
	 */
	public function action_auth()
	{
		print_r($_GET);

		// Request::current()->redirect('settings/facebook');
	}
	
}