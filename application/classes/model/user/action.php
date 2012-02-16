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
class Model_User_Action extends ORM
{
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
	 * Gets actions and notification for the user's follows
	 */
	public static function get_activity_stream($user_id)
	{
		
		// Notifications
		$query = DB::select('id', array(DB::expr('DATE_FORMAT(action_date_add, "%H:%i %b %e, %Y")'),'action_date'), 'user_id', 'user_name', 
		                    'user_email', 'action', 'action_on', 'action_on_id', 'action_on_name',
		                    'action_to_name', 'action_to_id', 'confirmed',
		                    array(DB::expr("if(action_to_id=$user_id, 1, 0)"), 'action_to_self'))
		             ->from('activity_stream')
		             ->where('user_id', 'in', DB::expr("(select user_id from user_followers where follower_id = $user_id)"))
		             ->or_where('action_to_id','=',$user_id)
		             ->order_by('action_date_add', 'DESC');
		
		$results = $query->execute()->as_array();
		
		foreach ($results as & $result) {
			
			$result["user_avatar"] = Swiftriver_Users::gravatar($result["user_email"]);
			
			// Set the user's url
			$user_orm = ORM::factory('user', $result["user_id"]);
			$result["user_url"] = URL::site().'user/'.$user_orm->account->account_path; 
			
			
			// Set action url
			$result["action_on_url"] = "";
			if ($result["action_on"] == "account") {
				$result["action_on_url"] = URL::site().'user/'.$result["action_on_name"]; 
			}
			if ($result["action_on"] == "river") {
				$result["action_on_url"] = URL::site().'river/index/'.$result["action_on_id"]; 
			}
			if ($result["action_on"] == "bucket") {
				$result["action_on_url"] = URL::site().'bucket/index/'.$result["action_on_id"]; 
			}			
		}
		
		return $results;
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
		$user_action_orm->action_date_add = date("Y-m-d H:i:s", time());
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
		$query = DB::select(array(DB::expr('COUNT(*)'),'num_notification'))
		             ->from('activity_stream')
		             ->where('action_to_id', '=', $user_id)
		             ->where('confirmed', '!=', 1);
		
		return $query->execute()->get('num_notification', 0);
	}
}
