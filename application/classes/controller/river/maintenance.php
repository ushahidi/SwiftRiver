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
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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

		// Get settings
		$settings = Model_Setting::get_settings(array(
			'river_active_duration',
			'river_expiry_notice_period',
			'site_url'
		));

		$notice_period = $settings['river_expiry_notice_period'];
		$site_url = $settings['site_url'];


		// Templates for the notifications
		$warning_template = View::factory('emails/expiry_warning');
		$notice_template = View::factory('emails/expiry_notice');

		// Fix the current date to the time when the maintenance
		// is being run
		$current_date_timestamp = time();
		$current_date = date("Y-m-d H:i:s", $current_date_timestamp);

		// Compute the filter date
		$filter_date_timestamp = strtotime(sprintf("+%s day", $notice_period),
		    $current_date_timestamp);

		$filter_date = date("Y-m-d H:i:s", $filter_date_timestamp);

		// Get the rivers that have expired or are about to expire
		$candidates = ORM::factory('river')
		    ->where('river_expired', '=', 0)
		    ->where('river_date_expiry', '<=', $filter_date)
		    ->find_all();

		$to_be_expired = array();
		$to_be_flagged = array();
		$rivers = array();

		foreach ($candidates as $river)
		{
			$days_to_expiry = $river->get_days_to_expiry($current_date);
			$river_url = $site_url.$river->get_base_url();

			// Generate extension token and modify the URL
			if ($days_to_expiry === 0)
			{
				$token = hash("sha256", Text::random('alnum', 32));
				$river_url .= '/extend?token='.$token;
				$to_be_expired[$river->id] = $token;
			}
			else
			{
				// Is the river to be flagged for expiry
				if ($days_to_expiry > 0 AND $river->expiry_candidate == 0)
				{
					$to_be_flagged[] = $river->id;
				}
				else
				{
					continue;
				}
			}

			$rivers[$river->id] = array(
				'river_name' => $river->river_name,
				'river_url' => $river_url,
				'days_to_expiry' => $days_to_expiry
			);
		}

		// If no rivers found, terminate
		if (count($rivers) == 0)
		{
			Kohana::$log->add(Log::INFO, __("No rivers found. Exiting..."));
			return;
		}

		// Get the owners for each of the rivers
		$river_owners = $this->_get_river_owners(array_keys($rivers));

		// Expire rivers
		if (count($to_be_expired) > 0)
		{
			$this->_expire_rivers($to_be_expired);
		}

		// Switch on the expiry flag
		if (count($to_be_flagged) > 0)
		{
			DB::update('rivers')
			    ->set(array('expiry_candidate' => 1))
			    ->where('id', 'IN', $to_be_flagged)
			    ->execute();
		}

		// Send out notifications
		Kohana::$log->add(Log::INFO, __("Sending out notifications"));
		foreach ($river_owners as $river_id => $owners)
		{
			$data = $rivers[$river_id];

			// Mail subject
			$subject = __("Your :river_name river will shutdown in :days_to_expiry day(s)!",
			    array(
			    	":river_name" => $data['river_name'],
			    	":days_to_expiry" => $data['days_to_expiry']
			    ));

			// Mail body - expiry warning is the default
			$mail_body = $warning_template->set(array(
				'river_name' => $data['river_name'],
				'days_to_expiry' => $data['days_to_expiry'],
				'active_duration' => $settings['river_active_duration'],
				'river_url' => $data['river_url']
			));

			if ($data['days_to_expiry'] === 0)
			{
				$subject = __("Your :river_name has shutdown!", 
				    array(":river_name" => $data['river_name']));

				// Expiry notice message
				$mail_body = $notice_template->set(array(
					'river_name' => $data['river_name'],
					'active_duration' => $settings['river_active_duration'],
					'activation_url' => $data['river_url']
				));
			}

			// Construct the mail body
			foreach ($owners as $owner)
			{
				$mail_body->recipient_name = $owner['name'];
				Swiftriver_Mail::send($owner['email'], $subject, $mail_body);
			}
		}

		Kohana::$log->add(Log::INFO, "Completed maintenance schedule");
	}


	/**
	 * Given an array of river ids, returns a key-value
	 * array of the each river and its list of owners
	 *
	 * @param    array $river_ids Database ids of rivers
	 * @return   array
	 */
	private function _get_river_owners($river_ids)
	{
		// Get the river owners
		$union_query = DB::select('river_collaborators.river_id', 'users.name', 'users.email')
		    ->from('users')
		    ->join('river_collaborators', 'INNER')
		    ->on('river_collaborators.user_id', '=', 'users.id')
		    ->where('river_collaborators.collaborator_active', '=', 1)
		    ->where('river_collaborators.river_id', 'IN', $river_ids);

		$final_query = DB::select(array('rivers.id', 'river_id'), 'users.name', 'users.email')
		    ->union($union_query, TRUE)
		    ->from('rivers')
		    ->join('accounts', 'INNER')
		    ->on('rivers.account_id', '=', 'accounts.id')
		    ->join('users', 'INNER')
		    ->on('accounts.user_id', '=', 'users.id')
		    ->where('rivers.id', 'IN', $river_ids);

		$all_owners = $final_query->execute();

		// Key value store of a river and its owners
		$river_owners = array();
		foreach ($all_owners as $owner)
		{
			$river_id = $owner['river_id'];
			if ( ! array_key_exists($river_id, $river_owners))
			{
				$river_owners[$river_id] = array();
			}

			$entry = array(
				'name' => $owner['name'],
				'email' => sprintf("%s <%s>", $owner['name'], $owner['email'])
			);

			$river_owners[$river_id][] = $entry;
		}

		return $river_owners;
	}

	/**
	 * Expires the rivers
	 */
	private function _expire_rivers($expiry_data)
	{
		$river_ids = array_keys($expiry_data);

		// Disable the rivers' channels
		DB::update('channel_filters')
		    ->set(array('filter_enabled' => 0))
		    ->where('filter_enabled', '=', 1)
		    ->where('river_id', 'IN', $river_ids);

		// Update query
		$update_query = "UPDATE rivers SET river_expired = 1, "
		    . "expiry_extension_token = CASE `id` ";

		foreach ($expiry_data as $river_id => $token)
		{
			$condition = sprintf("WHEN %s THEN '%s' ", $river_id, $token);
			$update_query .= $condition;
		}

		$update_query .= sprintf("END WHERE `id` IN (%s)", implode(",", $river_ids));

		// Set the expiry token
		DB::query(Database::UPDATE, $update_query)->execute();
	}

}