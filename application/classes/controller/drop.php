<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Drop Controller
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
class Controller_Drop extends Controller_Swiftriver {
	
	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 */
	public $auto_render = FALSE;
	
	 /**
	  * Single drop restful api
	  */ 
	 public function action_api()
	 {
		$this->template = "";
		
		if ( ! $this->admin)
		{
			throw new HTTP_Exception_403();
		}

		switch ($this->request->method())
		{
			case "POST":
				// Get the POST data
				$drops = json_decode($this->request->body(), TRUE);
				Kohana::$log->add(Log::DEBUG, ":count Drops received", array(':count' => count($drops)));
				list(, $new_drops) = Model_Droplet::create_from_array($drops);
				echo json_encode($new_drops);
			break;
		}
	 }
}