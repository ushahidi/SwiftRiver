<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River maintenance controller
 * This controller performs river maintenance by checking which
 * rivers are about to expire, flagging them as expiry candidates
 * and notifying their owners about the impending shutdown (expiration)
 *
 * Where a river has already been flagged for expiry, but the owner has
 * not extended its expiry date, the channel options for that river are
 * removed from the crawling schedule, and the user is notified of the
 * action.
 * 
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Controller_River_Maintenance extends Controller {

	/**
	 * Perform the maintenance
	 */
	public function action_run()
	{
		if (php_sapi_name() !== 'cli')
		{
			Kohana::$log->add(Log::ERROR, __("Maintenance must be run in CLI mode"));
			exit;
		}

		Kohana::$log->add(Log::INFO, __("Running river maintenance schedule"));

		// Get the expiry candidates
		$notice_period = Model_Setting::get_setting('river_expiry_notice_period');
		$expiry_timestamp = strtotime(sprintf("+%s day", $notice_period), time());
		$expiry_date_limit = date("Y-m-d H:i:s", $expiry_timestamp);

		// Candidate-selection
		$expiry_candidates = ORM::factory('river')
		    ->where('river_expired', '=', 0)
		    ->where('river_date_expiry', '<=', $expiry_date_limit)
		    ->find_all();

		// Get the active duration setting
		$active_duration = Model_Setting::get_setting('river_active_duration');

		// Template for the email to be sent out prior to expiration
		$expiry_warning = View::factory('emails/expiry_warning');

		// Template for the email to be sent out when the river expires
		$expiry_notice = View::factory('emails/expiry_notice');

		// Base URL for the links
		$base_url = substr(URL::base(TRUE, TRUE), 0, -1)

		foreach ($expiry_candidates as $candidate)
		{
			$river_owners = $candidate->get_owners();
			if ($candidate->expiry_candidate AND $candidate->get_days_to_expiry() == 0)
			{
				// Mark the river as expired and generate an extension token
				$extension_token = hash_hmac("sha256", Text::random('alnum', 32), $candidate->river_name_url);
				$candidate->river_expired = 1;
				$candidate->expiry_extension_token = $extension_token;
				$candidate->expiry_candidate = 0;

				// Deactive the channels for the river
				DB::update('channel_filters')
				    ->set(array('filter_enabled' => 0))
				    ->where('river_id', '=', $candidate->id)
				    ->execute();

				// URL for extending the expiry date
				$expiry_notice->set(array(
					'river_name' => $candidate->river_name,
					'duration' => $active_duration,
					'activation_url' => $base_url.$candidate->get_base_url().'/extend?token='.$extension_token
				));

				// Subject of the expiry notice
				$subject = __("Your :river river has shutdown!", array(":river" => $candidate->river_name));

				// Notify each of the owner-collaborators
				foreach ($river_owners as $owner)
				{
					$expiry_notice->recipient_name = $owner->user->name;
					Swiftriver_Mail::send($owner->user->email, $subject, $expiry_notice);					
				}

				// Notify the creator
				$expiry_notice->recipient_name = $candidate->account->user->name;
				Swiftriver_Mail::send($candidate->account->user->email, $subject, $expiry_notice);
			}
			elseif ( ! $candidate->expiry_candidate)
			{
				// Mark river as expiry candidate
				$candidate->expiry_candidate = 1;

				// No. of days remaining before the river expires
				$days_to_expiry = $candidate->get_days_to_expiry();

				// Subject of the email
				$subject = __("Your :river river will shutdown in :days days",
					array(":river" => $candidate->river_name, ":days" => $days_to_expiry));

				// Set common values for the email message
				$expiry_warning->set(array(
					'river_name' => $candidate->river_name,
					'duration' => $active_duration,
					'days_to_expiry' => $days_to_expiry,
					'river_url' => $base_url.$candidate->get_base_url()
				));

				// Notify each of the owner-collaborators
				foreach ($river_owners as $owner)
				{
					$expiry_warning->recipient_name = $owner->user->name;					
					Swiftriver_Mail::send($owner->user->email, $subject, $expiry_warning);
				}

				// Notify the creator
				$expiry_warning->recipient_name = $candidate->account->user->name;
				Swiftriver_Mail::send($candidate->account->user->email, $subject, $expiry_warning);
			}

			// Save the changes
			$candidate->save();
		}

		Kohana::$log->add(Log::INFO, "Completed maintenance schedule");
	}
}