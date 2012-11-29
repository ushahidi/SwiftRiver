<?php defined('SYSPATH') or die('No direct script access');
/**
 * CSRF Helper class
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    CSRF - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */
class CSRF {

	/**
	 * Session key for the CSRF token
	 * @var string
	 */
	private static $_csrf_session_key = 'csrf-token';

	/**
	 * Generates an returns a randon token for CSRF
	 * prevention
	 *
	 * @return string
	 */	
	public static function token()
	{
		$token = Session::instance()->get(self::$_csrf_session_key);

		if ( ! $token)
		{
			// Generates a hash of variable length random alpha-numeric string
			$token = hash('sha256', Text::random('alnum', rand(25, 32)));
			Session::instance()->set(self::$_csrf_session_key, $token);
		}

		return $token;
	}

	/**
	 * Validates the specified token against the current
	 * session value
	 *
	 * @return bool TRUE if match, FALSE otherwise
	 */
	public static function valid($token)
	{
		// Get the current token and destroy the session value
		$current_token = self::token();
		Session::instance()->delete(self::$_csrf_session_key);

		return $token == $current_token;
	}
}

?>