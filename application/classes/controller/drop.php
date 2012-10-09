<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Drop Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
				Model_Droplet::create_from_array($drops);
			break;
		}
	 }
}