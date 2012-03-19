<?php defined('SYSPATH') or die('No direct script access');
/**
 * CSRF Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_CSRF extends Controller_Template {

	/**
	 * Disable auto rendering
	 * @var bool
	 */
	public $auto_render = FALSE;

	/**
	 * View template for the controller
	 * @var string
	 */
	public $template = '';

	/**
	 * Generates a CSRF token. Only honours AJAX requests
	 */
	public function action_generate_token()
	{
		header("Content-Type: application/json; charset=utf-8");

		$result = array('token' => '');

		if ($this->request->is_ajax())
		{
			$token = Swiftriver_CSRF::token();

			$result['token'] = $token;

		}

		echo json_encode($result);
	}
}

?>