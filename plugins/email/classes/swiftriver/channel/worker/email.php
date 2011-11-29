<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Email Channel worker
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Channel_Worker_Email extends Swiftriver_Channel_Worker {

	/**
	 * @see Swiftriver_Channel_Worker->channel_worker
	 */
	public function channel_worker($job)
	{
		// Get the user id from the job workload
		$user_id = $job->workload();
		
		if ( ! empty($user_id))
		{
			// Get the configured email accounts
			$accounts = ORM::factory('email_setting')->where('user_id', '=', $user_id)->find_all();
			
			// Fetch the configs for each registered email account
			foreach ($accounts as $account)
			{
				// Check for SSL
				$ssl  = ($account->server_ssl == 1);
			
				// Connect to the mailbox
				$email_stream = new Swiftriver_Imap($account->server_host, $account->server_port, 
					$account->username, $account->password, $account->server_type, $ssl,
					$account->mailbox_name);
				
				// Fetch the messages
				$email_stream->get_messages();
			}
		}
		else
		{
			Kohana::$log->add(Log::ERROR, 'No user id found in the job workload');
		}
	}
}