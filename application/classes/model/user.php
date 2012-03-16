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
		'bucket_collaborators' => array(),
		'river_subscriptions' => array(
			'model' => 'river',
			'through' => 'river_subscriptions',
			'far_key' => 'river_id'
			),
		'bucket_subscriptions' => array(
			'model' => 'bucket',
			'through' => 'bucket_subscriptions',
			'far_key' => 'bucket_id'
			),
		'following' => array(
			'model' => 'user',
			'through' => 'user_followers',
			'far_key' => 'user_id',
			'foreign_key' => 'follower_id'
			),		
		'followers' => array(
			'model' => 'user',
			'through' => 'user_followers',
			'far_key' => 'follower_id'
			),
		'droplet_scores' => array()
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
	 * Overload saving to perform additional functions
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Generate an api token
			$this->api_key = Text::random('alnum', 32);
			$this->api_key = hash_hmac('sha256', Text::random('alnum', 32), $this->email);
		}

		$user = parent::save();

		return $user;
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
	public static function get_like($search_string, $exclude_ids = array(), $limit = 10)
	{
		$users = array();
		$search_string = strtolower(trim($search_string));
		$users_query = ORM::factory('user')
		                ->where('username', '!=', 'public')
		                ->where_open()
		                ->where(DB::expr('lower(email)'),'like', "$search_string%")
		                ->or_where(DB::expr('lower(name)'),'like', "$search_string%")
		                ->or_where(DB::expr('lower(name)'),'like', "% $search_string%")
		                ->where_close();
		
		if (! empty($exclude_ids)) {
			$users_query->and_where('id', 'not in', $exclude_ids);
		}
		
		$users_query->limit($limit);
	
		
		$users_orm = $users_query->find_all();
		
		foreach ($users_orm as $user)
		{
			$users[] = array(
				'id' => $user->id, 
				'name' => $user->name,
				'account_path' => $user->account->account_path,
				'avatar' => Swiftriver_Users::gravatar($user->email, 40)
			);
		}
		
		return $users;
	}
	
	/**
	 * Gets all the rivers accessible to the user - the ones
	 * they've created and the ones they're collaborating on
	 *
	 * @return array
	 */
	public function get_rivers()
	{
		$ret = array();
		
		// First rivers belonging to this user's account
		$ret = array_merge($ret, $this->account->rivers->order_by('river_name', 'ASC')->find_all()->as_array());
		
		// Next the rivers belonging to an account this user is collaborating on
		$account_collaborations = $this->account_collaborators
		                               ->where("collaborator_active", "=", 1)
		                               ->find_all();
		foreach ($account_collaborations as $collabo)
		{
			$ret = array_merge($ret, $collabo->account->rivers->order_by('river_name', 'ASC')->find_all()->as_array());
		}
		
		// Add individual rivers this user is collaborating on
		$river_collaborations = $this->river_collaborators
		                               ->where("collaborator_active", "=", 1)
		                               ->find_all();
		
		foreach ($river_collaborations as $collabo)
		{
			$ret[] = $collabo->river;
		}
		
		// Finally, the rivers the user has subscribed to
		$ret = array_merge($ret, $this->river_subscriptions->order_by('river_name', 'ASC')->find_all()->as_array());
		
		return array_unique($ret);
	}

	/**
	 * Gets all the buckets accessible to the user
	 * @return array
	 */
	public function get_rivers_array()
	{
		$ret = array();
		
		$rivers = $this->get_rivers();
		
		foreach ($rivers as $river)
		{
			$ret[] = array(
				"id" => $river->id, 
				"river_name" => $river->river_name,
				"account_id" => $river->account->id,
				"user_id" => $river->account->user->id,
				"account_path" => $river->account->account_path,
				"subscriber_count" => $river->get_subscriber_count()
			);
		}
		
		return $ret;
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
		
		// Buckets belonging to this user's account
		$buckets = array_merge($buckets, 
			$this->account->buckets->order_by('bucket_name', 'ASC')->find_all()->as_array());
		
		// Add buckets belonging to an account this user is collaborating on
		$account_collaborations = $this->account_collaborators
		    ->where("collaborator_active", "=", 1)		                               
		    ->find_all();

		foreach ($account_collaborations as $collabo)
		{
			$buckets = array_merge($buckets, 
				$collabo->account->buckets->order_by('bucket_name', 'ASC')->find_all()->as_array());
		}
		
		// Add individual buckets this user is collaborating on
		$bucket_collaborations = $this->bucket_collaborators
		                               ->where("collaborator_active", "=", 1)
		                               ->find_all();
		
		foreach ($bucket_collaborations as $collabo)
		{
			$buckets[] = $collabo->bucket;
		}
		
		// Add the buckets this user has subscribed to
		$buckets = array_merge($buckets, 
			$this->bucket_subscriptions->order_by('bucket_name', 'ASC')->find_all()->as_array());
		
		
		return array_unique($buckets);
	}


	/**
	 * Gets all the buckets accessible to the user - the ones
	 * they've created and the ones they're collaborating on
	 *
	 * @return array
	 */
	public function get_buckets_array()
	{
		$ret = array();
		
		$buckets = $this->get_buckets();
		
		foreach ($buckets as $bucket)
		{
			$ret[] = array(
				"id" => $bucket->id, 
				"bucket_name" => $bucket->bucket_name,
				"account_id" => $bucket->account->id,
				"user_id" => $bucket->account->user->id,
				"account_path" => $bucket->account->account_path,
				"subscriber_count" => $bucket->get_subscriber_count()
			);
		}
		
		return $ret;
	}
}
