<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for User Actions
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_User_Action extends ORM {
	/**
	 * An belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'action_date_add', 'format' => 'Y-m-d H:i:s');
	
	/**
	 * Gets actions and notification for the user's follows or the user's activities
	 *
	 * @param    int $user_id Visited user ID
	 * @param    int $visitor_id ID of the user viewing the profile
	 * @param    boolean $self If TRUE, only get the specified user_id's actions otherwise that of his following
	 * @return   array
	 */
	public static function get_activity_stream($user_id, $visitor_id = NULL, $self = FALSE, $last_id = NULL, $since_id = NULL, $limit = 10)
	{
		// Check the cache
		$request_hash = hash('sha256', $user_id.
						$visitor_id.
						$self.
						$last_id.
						$since_id.
						$limit);
		$cache_key = 'activity_stream_'.$request_hash;
		
		// If the cache key is available (with default value set to FALSE)
		if ($results = Cache::instance()->get($cache_key, FALSE))
		{
			return $results;
		}
		
		// Notifications
		$query = DB::select('id',  
							array(DB::expr('DATE_FORMAT(action_date_add, "%b %e, %Y %H:%i:%S UTC")'),'action_date_add'), 
							'user_id', 'action',
		                    'action_on', 'action_on_id', 'action_to_id',
							'confirmed')
		    ->from('user_actions');
		
		if ($self)
		{
			// Get the specified user_id's actions
			$query->where('user_id', '=', $user_id);
		}
		else
		{
			// Get the actions of the users user_id is following
			$query->where_open()
				  ->where('user_id', 'IN', 
				DB::expr("(SELECT user_id FROM user_followers WHERE follower_id = $user_id)"))
					->or_where('action_to_id','=',$user_id)
					->or_where_open()
					->where("action_on", "=", "user")
					->where("action_on_id", "=", $user_id)
					->or_where_close()
					->where_close();
		}
		
		if (isset($last_id))
		{
			$query->where("id", "<", $last_id);
		}
		
		if (isset($since_id))
		{
			$query->where("id", ">", $since_id);
		}
		
		$results = $query->order_by('id', 'DESC')
						 ->limit($limit)
						 ->execute()
						 ->as_array();
		
		self::populate_action_data($results, $visitor_id);
		
		// Using array_values here to reindex the array in case the populate_action_data
		// call unset some array elements. A json_encode on an array with non sequential
		// array elements gives a javascript object instead of array.
		$results = array_values($results);
		
		// Cache the results
		if ( ! empty($results))
		{
			Cache::instance()->set($cache_key, $results);
		}
		return $results;
	}
	
	/**
	 * Populates the bucket/river/user to data which the 
	 * action_on_id/action_to_id/user_id points to
	 *
	 * @param    string $$user_actions Model_User_Actions array
	 * @return   null
	 */
	private static function populate_action_data(& $user_actions, $visitor_id) 
	{
		// Hash that will hold the unique bucket/river/user ids in the actions array
		$ids = array(
			'user_id' => array(),
			'action_to' => array(),
			'user' => array(),
			'bucket' => array(),
			'river' => array(),
		);
		
		// Collect the user/bucket/river etc IDs to query in single batches
		foreach ($user_actions as $key => & $user_action)
		{
			// Create all fields in each element
			$user_action['avatar'] = NULL;
			$user_action['user_url'] = NULL;
			$user_action['user_name'] = NULL;
			$user_action['username'] = NULL;
			$user_action['email'] = NULL;
			$user_action['action_to_name'] = NULL;
			$user_action['action_on_name'] = NULL;
			$user_action['action_on_url'] = NULL;
			
			// Get user IDs
			$user_id = $user_action['user_id'];
			if ( ! isset($ids['user_id'][$user_id])) 
			{
				$ids['user_id'][$user_id] = Array();
			}
			
			// Store the array elements where this user_id was found
			$ids['user_id'][$user_id][] = $key;
			
			// Get action_on IDs
			$action_on = $user_action['action_on'];
			$action_on_id = $user_action['action_on_id'];
			if ( ! isset($ids[$action_on][$action_on_id])) 
			{
				$ids[$action_on][$action_on_id] = Array();
			}
			
			// Store the array elements where this user_id was found
			$ids[$action_on][$action_on_id][] = $key;
			
			// Get action_to IDs
			$action_to_id = $user_action['action_to_id'];
			if ( ! isset($ids['action_to'][$action_to_id])) 
			{
				$ids['action_to'][$action_to_id] = Array();
			}
			
			// Store the array elements where this user_id was found
			$ids['action_to'][$action_to_id][] = $key;
			
			// Make confirmed field boolean
			$user_action['confirmed'] = (bool)$user_action['confirmed'];
		}
		
		
		/* There can be 3 users_ids to populate:
		 *     --> The actioner given in the user_id column
		 *     --> The action_on_id can be a user i.e. xx is following yy
		 *     --> action_to_id is a user_id in the case of an invite.
		 * So collect all three arrays into one here and query the user table once
		 */
		 $user_ids = array_merge(array_keys($ids['user_id']), 
		 						 array_keys($ids['user']),
								 array_keys($ids['action_to']));
		
		foreach (Model_User::get_users($user_ids) as $user)
		{
			
			if (array_key_exists($user->id, $ids['user_id']))
			{
				foreach ($ids['user_id'][$user->id] as $key)
				{
					$user_actions[$key]['avatar'] = Swiftriver_Users::gravatar($user->email, 80);
					$user_actions[$key]['user_url'] = URL::site($user->account->account_path);
					$user_actions[$key]['user_name'] = $user->name;
					$user_actions[$key]['username'] = $user->username;
					$user_actions[$key]['email'] = $user->email;
				}
			}
			
			if (array_key_exists($user->id, $ids['user']))
			{
				foreach ($ids['user'][$user->id] as $key)
				{
					$user_actions[$key]['action_on_name'] = $user->name;
					$user_actions[$key]['action_on_url'] = $user->get_profile_url();
				}
			}
			
			if (array_key_exists($user->id, $ids['action_to']))
			{
				foreach ($ids['action_to'][$user->id] as $key)
				{
					$user_actions[$key]['action_to_name'] = $user->name;
				}
			}
		}
		
		// Get bucket details
		foreach (Model_Bucket::get_buckets(array_keys($ids['bucket'])) as $bucket)
		{
			foreach ($ids['bucket'][$bucket->id] as $key)
			{
				if ( ! $bucket->bucket_publish AND 
					 ! $bucket->is_owner($visitor_id) AND 
					 ! $bucket->is_collaborator($visitor_id))
				{
					unset($user_actions[$key]);
				}
				else
				{
					$user_actions[$key]['action_on_name'] = $bucket->bucket_name;
					$user_actions[$key]['action_on_url'] = $bucket->get_base_url();
				}
			}
		}
		
		// Get river details
		foreach (Model_River::get_rivers(array_keys($ids['river'])) as $river)
		{
			foreach ($ids['river'][$river->id] as $key)
			{
				if ( ! $river->river_public AND 
					 ! $river->is_owner($visitor_id) AND 
					 ! $river->is_collaborator($visitor_id))
				{
					unset($user_actions[$key]);
				}
				else
				{
					$user_actions[$key]['action_on_name'] = $river->river_name;
					$user_actions[$key]['action_on_url'] = $river->get_base_url();
				}
			}
		}
	}
	
	/**
	 * Create a user action
	 *
	 * @param int $user_id User id of the user doing the action
	 * @param string $action_on account/river/bucket/drop
	 * @param string $action create/invite/commment...
	 * @param int $action_on_id id of the account/river/bucket being acted on
	 * @param int $action_to_id optional id of the user being invited
	 */
	public static function create_action($user_id, $action_on, $action, $action_on_id, $action_to_id = NULL)
	{
		$user_action_orm = Model::factory('user_action');
		$user_action_orm->user_id = $user_id;
		$user_action_orm->action = $action;
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
			->from('user_actions')
			->where('action_to_id', '=', $user_id)
			->where('confirmed', '!=', 1)
			->execute()
			->get('num_notification', 0);

		// SwiftRiver Plugin Hook -- Add Notification Count
		Swiftriver_Event::run('swiftriver.user.notification.count', $count);

		return $count;
	}
}
