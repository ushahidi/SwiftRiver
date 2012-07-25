<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for User Actions
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_User_Action extends ORM {
	/**
	 * An belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());	
		
	/**
	 * Overload saving to perform additional functions on the action
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time actions only
		if ($this->loaded() === FALSE)
		{
			// Save the date the action was first added
			$this->action_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
	
	/**
	 * Gets actions and notification for the user's follows or the user's activities
	 *
	 * @param    int $user_id Visited user ID
	 * @param    int $visitor_id ID of the user viewing the profile
	 * @param    boolean $self If TRUE, only get the specified user_id's actions otherwise that of his following
	 * @return   array
	 */
	public static function get_activity_stream($user_id, $visitor_id = NULL, $self = FALSE)
	{
		
		// Notifications
		$query = DB::select('id', 
		    array(DB::expr('DATE_FORMAT(action_date_add, "%b %e, %Y %H:%i UTC")'),'action_date'), 
		    'user_id', 'user_name', 'user_email', 'action', 'action_on', 
		    'action_on_id', 'action_on_name', 'action_to_name', 
		    'action_to_id', 'confirmed',
		    array(DB::expr("IF(action_to_id=$user_id, 1, 0)"), 'action_to_self'))
		    ->from('activity_stream');
		
		if ($self)
		{
			// Get the specified user_id's actions
			$query->where('user_id', '=', $user_id);
		}
		else
		{
			// Get the actions of the users user_id is following
			$query->where('user_id', 'IN', 
				DB::expr("(SELECT user_id FROM user_followers WHERE follower_id = $user_id)"))
			        ->or_where('action_to_id','=',$user_id);
		}

		$query->order_by('action_date_add', 'DESC');
		
		$results = $query->execute()->as_array();
		
		// Tracks the action target and the corresponding users
		$actions = array();

		// List of action initiatators
		$initiators = array();

		foreach ($results as $result)
		{
			// Set action url
			$action_on_url = "";
			$action_on_name = "";
			$action_on = $result['action_on'];
			$action_on_id = $result["action_on_id"];

			// Whether to leave out the current activity feed item
			// from the final result
			// An item is left out when:
			//    action_on item is private
			//    visitor does not own action_on item and action_to_self = 0
			$skip_activity_item = FALSE;

			if ($action_on == "account")
			{
				$action_on_url = URL::site().$result["action_on_name"]; 
			}
			elseif ($action_on == "river")
			{
				$river_orm = ORM::factory("river", $action_on_id);
				
				if ( ! $river_orm->river_public)
				{
					$is_owner = $river_orm->is_owner($visitor_id);
					if (( ! $is_owner AND $result['action_to_self'] == 1) OR $is_owner)
					{
						$skip_activity_item = FALSE;
					}
					else
					{
						$skip_activity_item = TRUE;
					}

				}

				$action_on_name = $river_orm->river_name;
				$action_on_url = URL::site().$river_orm->account->account_path.'/river/'.$river_orm->river_name_url; 
			}
			elseif ($action_on == "bucket")
			{
				$bucket_orm = ORM::factory("bucket", $action_on_id);

				if ( ! $bucket_orm->bucket_publish)
				{
					$is_owner = $bucket_orm->is_owner($visitor_id);
					if (( ! $is_owner AND $result['action_to_self'] == 1) OR $is_owner)
					{
						$skip_activity_item = FALSE;
					}
					else
					{
						$skip_activity_item = TRUE;
					}
				}

				$action_on_name = $bucket_orm->bucket_name;
				$action_on_url = URL::site().$bucket_orm->account->account_path
												.'/bucket/'.$bucket_orm->bucket_name_url; 
			}

			// Leave out current activity feed item?
			if ($skip_activity_item)
				continue;


			// Condense the activity stream data
			$user_id = $result['user_id'];

			if ( ! array_key_exists($result['user_id'], $actions))
			{
				$actions[$user_id] = array();

				// Set the user's url
				$user_orm = ORM::factory('user', $result["user_id"]);

				// Populate the initiators array
				$initiators[$user_id] = array(
					'name' => ($user_id === $visitor_id) ? __('You') : $result["user_name"], 
					'avatar' => Swiftriver_Users::gravatar($result["user_email"]),
					'url' => $result["user_url"] = URL::site().$user_orm->account->account_path);
			}

			$action_name = $result['action'];
			if ( ! array_key_exists($action_name, $actions[$user_id]))
			{
				$actions[$user_id][$action_name] = array();
			}

			// Timestamp for the action date
			$action_timestamp = strtotime(strftime("%a %b %e, %Y", 
				strtotime($result['action_date'])));

			if ($result['action_to_self'] == 1 AND $result['confirmed'] == 0)
			{
				// Modify the timestamp of the action
				$action_timestamp = strtotime($result['action_date']);
			}

			if ( ! array_key_exists($action_timestamp, $actions[$user_id][$action_name]))
			{
				$actions[$user_id][$action_name][$action_timestamp] = array();
			}

			// Generate a unique action key (user_id, action_name, timestamp)
			// Use the memory address to avoid having to set the
			// key to the mod'd value
			$action_key = & $actions[$user_id][$action_name][$action_timestamp];

			// Target of the action
			if ( ! array_key_exists($action_on, $action_key))
			{
				$action_key[$action_on] = array('targets' => array());
			}

			// Action targets + users
			if ( ! array_key_exists($action_on_id, $action_key[$action_on]['targets']))
			{
				$action_key[$action_on]['targets'][$action_on_id] = array(
					'action_on_url' => $action_on_url, 
					'action_on_name' => $action_on_name,
					'users' => array());
			}

			// Target user id and name
			$action_to_data = array(
				'action_id' => $result['id'],
				'action_date' => $result['action_date'],
				'action_to_self' => $result['action_to_self'],
				'confirmed' => $result['confirmed'],
				'action_to_id' => $result['action_to_id'], 
				'action_to_name' => ($result['action_to_self'] ==  1) ? __('you') : $result['action_to_name']
			);

			// Grouping step - Add to the targeted user id to the action target
			array_push($action_key[$action_on]['targets'][$action_on_id]['users'], 
				$action_to_data);
		}

		// Garbage collection
		unset ($result);

		// Pack the data for convenient JSON traversal
		$packed = array();
		foreach ($actions as $user_id => $data)
		{
			$entry = array(
				'user_id' => $user_id, 
				'user_name' => $initiators[$user_id]['name'],
				'user_avatar' => $initiators[$user_id]['avatar'],
				'user_url' => $initiators[$user_id]['url'],
				'actions' => array()
			);

			foreach ($data as $action => $action_data)
			{
				$action_entry = array('action_name' => $action, 'action_data' => array());
				foreach ($action_data as $timestamp => $target_data)
				{
					foreach (array_keys($target_data) as $action_on)
					{
						$action_on_id = array_keys($target_data[$action_on]['targets']);
						$action_entry['action_data'][] = array(
							'timestamp' => $timestamp,
							'timestamp_date_str' => gmstrftime("%a %b %e, %Y UTC", $timestamp),
							'action_on' => $action_on,
							'action_on_id' => $action_on_id[0],
							'action_on_target' => $target_data[$action_on]['targets'][$action_on_id[0]]);
					}
				}

				$entry['actions'][] = $action_entry;
			}

			$packed[] = $entry;
		}

		return $packed;
	}
	
	/**
	 * Create a user action
	 *
	 * @param int $user_id User id of the user doing the action
	 * @param string $action_on account/river/bucket
	 * @param int $action_on_id id of the account/river/bucket being acted on
	 * @param int $action_to_id optional id of the user being invited
	 */
	public static function create_action($user_id, $action_on, $action_on_id, $action_to_id = NULL)
	{
		$user_action_orm = Model::factory('user_action');
		$user_action_orm->user_id = $user_id;
		$user_action_orm->action = 'invite';
		$user_action_orm->action_on = $action_on;
		$user_action_orm->action_on_id = $action_on_id;		
		$user_action_orm->action_to_id = $action_to_id;
		$user_action_orm->action_date_add = gmdate("Y-m-d H:i:s", time());
		$user_action_orm->save();
	}
	
	/**
	 * Delete a pending invite
	 *
	 */
	public static function delete_invite($user_id, $action_on, $action_on_id, $action_to_id)
	{
		$user_action_orm = Model::factory('user_action')
		    ->where('action', '=', 'invite')
		    ->where('user_id', '=', $user_id)
		    ->where('action_on', '=', $action_on)
		    ->where('action_on_id', '=', $action_on_id)
		    ->where('action_to_id', '=', $action_to_id)
		    ->where('confirmed', '=', 0)
		    ->find();
		
		if ($user_action_orm->loaded())
		{
			$user_action_orm->delete();
		}
	}
	
	/**
	 * Gets the number of notifications a user has
	 *
	 * @return int
	 */
	public static function count_notifications($user_id)
	{
		$count = DB::select(array(DB::expr('COUNT(*)'),'num_notification'))
			->from('activity_stream')
			->where('action_to_id', '=', $user_id)
			->where('confirmed', '!=', 1)
			->execute()
			->get('num_notification', 0);

		// SwiftRiver Plugin Hook -- Add Notification Count
		Swiftriver_Event::run('swiftriver.user.notification.count', $count);

		return $count;
	}
}
