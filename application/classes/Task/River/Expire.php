<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Rier Expiry Task
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

class Task_River_Expire extends Minion_Task {

	// Run crawling scheduler
	protected function _execute(array $params)
	{
		Swiftriver_Mutex::obtain(get_class(), 3600);
		Kohana::$log->add(Log::INFO, __("Running river maintenance schedule"));
		
		// Get settings
		$settings = Swiftriver::get_settings(array(
			'default_river_lifetime',
			'river_expiry_notice_period',
			'site_url'
		));

		$notice_period = $settings['river_expiry_notice_period'];
		$site_url = $settings['site_url'];
		
		// Templates for the notifications
		$warning_template = View::factory('emails/text/expiry_warning');
		$notice_template = View::factory('emails/text/expiry_notice');

		// Get the rivers that have expired or are about to expire
		$candidates = ORM::factory('River')
		    ->where('river_expired', '=', 0)
			->where('river_active', '=', 1)
		    ->where('river_date_expiry', '<=', DB::expr('DATE_ADD(NOW(),INTERVAL '.$notice_period.' DAY)'))
		    ->find_all();

		$to_be_expired = array();
		$to_be_flagged = array();
		$rivers = array();

		foreach ($candidates as $river)
		{
			$days_to_expiry = $river->get_days_to_expiry();
			$river_url = $site_url.$river->get_base_url();

			if ($days_to_expiry === 0)
			{
				$to_be_expired[] = $river->id;
				Swiftriver_Event::run('swiftriver.river.disable', $river);
			}
			else
			{
				// Is the river to be flagged for expiry
				if ($days_to_expiry > 0 AND $river->expiry_notification_sent == 0)
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
			DB::update('rivers')
			    ->set(array('river_expired' => 1))
				->set(array('river_active' => 0))
			    ->where('id', 'IN', $to_be_expired)
				->execute();
		}

		// Switch on the expiry flag
		if (count($to_be_flagged) > 0)
		{
			DB::update('rivers')
			    ->set(array('expiry_notification_sent' => 1))
			    ->where('id', 'IN', $to_be_flagged)
			    ->execute();
		}

		// Send out notifications
		Kohana::$log->add(Log::INFO, __("Sending out notifications"));
		foreach ($river_owners as $river_id => $owners)
		{
			$data = $rivers[$river_id];

			// Mail subject
			$subject = __('Your ":river_name" river will expire in :days_to_expiry day(s)!',
			    array(
			    	":river_name" => $data['river_name'],
			    	":days_to_expiry" => ceil($data['days_to_expiry'])
			    ));

			// Mail body - expiry warning is the default
			$mail_body = $warning_template->set(array(
				'river_name' => $data['river_name'],
				'days_to_expiry' => ceil($data['days_to_expiry']),
				'active_duration' => $settings['default_river_lifetime'],
				'river_url' => $data['river_url']
			));

			if ($data['days_to_expiry'] === 0)
			{
				$subject = __('Your ":river_name" river has expired!', 
				    array(":river_name" => $data['river_name']));

				// Expiry notice message
				$mail_body = $notice_template->set(array(
					'river_name' => $data['river_name'],
					'active_duration' => $settings['default_river_lifetime'],
					'activation_url' => $data['river_url'].'/extend'
				));
			}

			// Construct the mail body
			foreach ($owners as $owner)
			{
				Kohana::$log->add(Log::INFO, __("Sending notification to :email", array(":email" => $owner['email'])));
				$mail_body->recipient_name = $owner['name'];
				Swiftriver_Mail::send($owner['email'], $subject, $mail_body);
			}
		}

		Swiftriver_Mutex::release(get_class());
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

}
?>
