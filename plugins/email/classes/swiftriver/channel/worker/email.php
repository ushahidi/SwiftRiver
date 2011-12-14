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
		// Get the river id from the job workload
		$river_id = $job->workload();
		
		if (Model_River::is_valid_river_id($river_id))
		{
			// Get the list of configured email accounts for the river
			$accounts = Model_Channel_Filter::get_channel_filter_options('email', $river_id);
			
			// Fetch the configs for each registered email account
			foreach ($accounts as $account)
			{
				foreach ($account['email'] as $email_account)
				{
					// Get the config parameters
					$server_host = $email_account['host']['value'];
					$port = $email_account['port']['value'];
					$username = $email_account['username']['value'];
					$password = $email_account['password']['value'];
					$server_type = $email_account['server_type']['value'];
					$ssl = $email_account['ssl']['value'];
					
					// Check for SSL
					$ssl  = (strtoupper($ssl) == 'YES');
			
					// Connect to the mailbox
					$email_stream = new Swiftriver_Imap($server_host, $server_port, $username, 
					    $password, $server_type, $ssl);
				
					// Fetch the messages
					$email_stream->get_messages();
				}
			}
		}
		else
		{
			Kohana::$log->add(Log::ERROR, 'Invalid river id found in the job workload');
		}
	}
}