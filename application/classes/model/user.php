<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_User extends Model_Auth_User
{
	/**
	 * A user has many roles, tokens, buckets,
	 * actions, followers, subscriptions,
	 * channel_filters, accounts, discussions and
	 * user identities
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		// auth
		'roles' => array('through' => 'roles_users'),
		'user_tokens' => array(),
		'buckets' => array(),
		'user_actions' => array(),
		'user_followers' => array(),
		'user_subscriptions' => array(),
		'channel_filters' => array(),
		'accounts' => array(),
		'discussions' => array(),
		// for RiverID and other OpenID identities
		'user_identities' => array(),
		'account_collaborators' => array(),
		'river_collaborators' => array(),
		);
		
	protected $_has_one = array(
		'account' => array()
	);
	
	/**
	 * Rules for the user model. Because the password is _always_ a hash
	 * when it's set,you need to run an additional not_empty rule in your controller
	 * to make sure you didn't hash an empty string. The password rules
	 * should be enforced outside the model or with a model helper method.
	 *
	 * @return array Rules
	 * @see Model_Auth_User::rules
	 */
	public function rules()
	{
		return array(
			'username' => array(
				array('not_empty'),
			),
			'email' => array(
				array('not_empty'),
				array('email'),
				array(array($this, 'unique'), array('email', ':value')),
			),
		);
	}

	/**
	 * Given a string, this function will try to find an unused username by appending a number.
	 * Ex. username2, username3, username4 ...
	 *
	 * @param string $base
	 */
	public function generate_username($base = '') 
	{
		$base = $this->transcribe($base);
		$username = $base;
		$i = 2;
		// check for existent username
		while( $this->username_exist($username) ) 
		{
			$username = $base.$i;
			$i++;
		}
		return $username;
	}
	
	/**
	 * Gets all the buckets accessible to the user - the ones
	 * they've created and the ones they're collaborating on
	 *
	 * @return array
	 */
	public function get_buckets()
	{
		$buckets = array();
		
		// Directly owned buckets
		foreach ($this->buckets->find_all() as $bucket)
		{
			$buckets[] = array(
				"id" => $bucket->id, 
				"bucket_name" => $bucket->bucket_name
			);
		}
		
		// Buckets the user is collaborating in
		$collaborating = ORM::factory('bucket_collaborator')
		    ->where('user_id', '=', $this->id)
		    ->where('collaborator_active', '=', 1)
		    ->find_all();
		
		foreach ($collaborating as $item)
		{
			$bucket = ORM::factory('bucket', $item->bucket_id);
			$buckets[] = array(
				"id" => $item->bucket_id,
				"bucket_name" => $bucket->bucket_name
			);
		}
		
		// Free memory
		unset ($collaborating);
		
		return $buckets;
	}
	
	/**
	 * Get a list of rivers this user has access to from the given other_user_id
	 *
	 * @param int $other_user_id Database ID of the other user
	 * @return array
	 */
	public function get_other_user_visible_rivers($other_user_id)
	{
		$other_user_orm = ORM::factory('user', $other_user_id);
		
		if (!$other_user_orm->loaded())
		{
			return array();
		}
		
		$rivers = array();
		
		// First the public rivers.		
		foreach ($other_user_orm->account->rivers->find_all() as $river)
		{
			if($river->river_public OR $river->is_owner($this->id)) {
				$rivers[] = $river;
			}
		}
		
		return $rivers;
	}


	/**
	 * Get a list of buckets this user has access to from the given other_user_id
	 *
	 * @param int $other_user_id Database ID of the other user
	 * @return array
	 */
	public function get_other_user_visible_buckets($other_user_id)
	{
		$other_user_orm = ORM::factory('user', $other_user_id);
		
		if (!$other_user_orm->loaded())
		{
			return array();
		}
		
		$buckets = array();
		
		// First the public rivers.		
		foreach ($other_user_orm->account->buckets->find_all() as $bucket)
		{
			if($bucket->bucket_publish OR $bucket->is_owner($this->id)) {
				$buckets[] = $bucket;
			}
		}
		
		return $buckets;
	}
	
	/**
	 * Get a list of users whose name/email begins with the provided string
	 *
	 * @param $search_string
	 * @return array
	 */
	public static function get_like($search_string)
	{
		$users = array();
		$search_string = strtolower(trim($search_string));
		$users_orm = ORM::factory('user')
		                ->where(DB::expr('lower(email)'),'like', "$search_string%")
		                ->or_where(DB::expr('lower(name)'),'like', "$search_string%")
		                ->find_all();
		
		foreach ($users_orm as $user)
		{
			$users[] = array('id' => $user->id, 
			                         'name' => $user->name,
			                         'account_path' => $user->account->account_path
			);
		}
		
		return $users;
	}
	
}
