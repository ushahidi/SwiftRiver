<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mail Helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Libraries
 * @copyright  (c) 2012 Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_Messages {

	// Message list
	protected static $_messages = array();

	/**
	 * Add a system message
	 *
	 * @param	string	 $type
	 * @param	string	 $title
	 * @param	string	 $message
	 * @return	null
	 */
	public static function add_message($type, $title, $message, $flash = TRUE)
	{
		self::$_messages[] = array(
			'type' => $type,
			'title' => $title,
			'message' => $message,
			'flash' => $flash
		);
		
		Session::instance()->set("messages", self::$_messages);
	}
}

?>
