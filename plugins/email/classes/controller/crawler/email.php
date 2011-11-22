<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Email Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Crawler_Email extends Controller_Crawler_Main {

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}

	public function action_index()
	{
		// Get the user ID from the session
		$user_id = Session::instance()->get('user_id');
		
		// Get the configured email accounts
		$accounts = ORM::factory('email')->where('user_id', '=', $user_id)->find_all();
		
		// Fetch the email for each of the accounts
		foreach ($accounts as $account)
		{
			// Check for SSL
			$ssl  = ($account->server_ssl == 1);
			
			// Connect to the email
			$email_stream = Swiftriver_Imap($account->server_host, $account->server_port, 
				$account->username, $account->password, $ssl);
			
			// Fetch the messages
			$email_stream->get_messages();
		}
	}
}