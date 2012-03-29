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
		
		if ( ! $other_user_orm->loaded())
		{
			return array();
		}
		
		$rivers = array();
		
		// First the public rivers.		
		foreach ($other_user_orm->account->rivers->find_all() as $river)
		{
			if ($river->river_public OR $river->is_owner($this->id))
			{
				$rivers[] = array(
					'id' => $river->id,
					'type' => 'river',
					'river_name' => $river->river_name, 
					'river_url' => $river->account->account_path.'/river/'.$river->river_name_url,
					'subscribed' => $river->is_subscriber($this->id)
				);
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
		
		if ( ! $other_user_orm->loaded())
		{
			return array();
		}
		
		$buckets = array();
		
		// First the public rivers.		
		foreach ($other_user_orm->account->buckets->find_all() as $bucket)
		{
			if ($bucket->bucket_publish OR $bucket->is_owner($this->id))
			{
				$buckets[] = array(
					'id' => $bucket->id,
					'type' => 'bucket',
					'bucket_name' => $bucket->bucket_name, 
					'bucket_url' => $bucket->account->account_path.'/bucket/'.$bucket->bucket_name_url,
					'subscribed' => $bucket->is_subscriber($this->id)
				);
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
		// Rivers collaborating on
		$collaborating = DB::select(array('rc.river_id', 'id'),
			array(DB::expr('"river"'), 'type'),
			array(DB::expr('"FALSE"'), 'subscribed'), 'r.river_name', 
			array(DB::expr('CONCAT(a.account_path, "/river/", r.river_name_url)'), 'river_url'))
		    ->from(array('river_collaborators', 'rc'))
		    ->join(array('rivers', 'r'), 'INNER')
		    ->on('rc.river_id', '=', 'r.id')
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('r.account_id', '=', 'a.id')
		    ->where('rc.user_id', '=', $this->id)
		    ->where('rc.collaborator_active', '=', 1);


		// Rivers subscribed to
		$subscribed = DB::select(array('rs.river_id', 'id'), 
			array(DB::expr('"river"'), 'type'),
			array(DB::expr('"TRUE"'), 'subscribed'), 'r.river_name', 
			array(DB::expr('CONCAT(a.account_path, "/river/", r.river_name_url)'), 'river_url'))
		    ->union($collaborating, TRUE)
		    ->from(array('river_subscriptions', 'rs'))
		    ->join(array('rivers', 'r'), 'INNER')
		    ->on('rs.river_id', '=', 'r.id')
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('r.account_id', '=', 'a.id')
		    ->where('rs.user_id', '=', $this->id);

		// Get all the data at once
		$query_rivers = DB::select(array('r.id', 'id'), 
			array(DB::expr('"river"'), 'type'),
			array(DB::expr('"FALSE"'), 'subscribed'), 'r.river_name', 
			array(DB::expr('CONCAT(a.account_path, "/river/", r.river_name_url)'), 'river_url'))
		    ->union($subscribed, TRUE)
		    ->from(array('rivers', 'r'))
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('r.account_id', '=', 'a.id')
		    ->where('a.user_id', '=', $this->id);
		
		$result = $query_rivers->execute()->as_array();
		$this->_sanitize_bool_strings($result);
		return $result;
	}



	/**
	 * Gets all the buckets accessible to the user - the ones
	 * they've created and the ones they're collaborating on
	 *
	 * @return array
	 */
	public function get_buckets()
	{
		// Buckets collaborating on
		$collaborating = DB::select(array('bc.bucket_id', 'id'), 
			array(DB::expr('"bucket"'), 'type'),
			array(DB::expr('"FALSE"'), 'subscribed'), 'b.bucket_name', 
			array(DB::expr('CONCAT(a.account_path, "/bucket/", b.bucket_name_url)'), 'bucket_url'))
		    ->from(array('bucket_collaborators', 'bc'))
		    ->join(array('buckets', 'b'), 'INNER')
		    ->on('bc.bucket_id', '=', 'b.id')
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('a.user_id', '=', 'b.user_id')
		    ->where('bc.user_id', '=', $this->id)
		    ->where('bc.collaborator_active', '=', 1);


		// Buckets subscribed to
		$subscribed = DB::select(array('bs.bucket_id', 'id'), 
			array(DB::expr('"bucket"'), 'type'),
			array(DB::expr('"TRUE"'), 'subscribed'), 'b.bucket_name', 
			array(DB::expr('CONCAT(a.account_path, "/bucket/", b.bucket_name_url)'), 'bucket_url'))
		    ->union($collaborating, TRUE)
		    ->from(array('bucket_subscriptions', 'bs'))
		    ->join(array('buckets', 'b'), 'INNER')
		    ->on('bs.bucket_id', '=', 'b.id')
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('a.user_id', '=', 'b.user_id')
		    ->where('bs.user_id', '=', $this->id);

		// Get all the data at once
		$query_buckets = DB::select(array('b.id', 'id'), 
			array(DB::expr('"bucket"'), 'type'),
			array(DB::expr('"FALSE"'), 'subscribed'), 'b.bucket_name',
			array(DB::expr('CONCAT(a.account_path, "/bucket/", b.bucket_name_url)'), 'bucket_url'))
		    ->union($subscribed, TRUE)
		    ->from(array('buckets', 'b'))
		    ->join(array('accounts', 'a'), 'INNER')
		    ->on('a.user_id', '=', 'b.user_id')
		    ->where('b.user_id', '=', $this->id);

		$result = $query_buckets->execute()->as_array();
		$this->_sanitize_bool_strings($result);
		return $result;
	}

	/**
	 * Coverts boolean strings to actual boolean values
	 */
	private function _sanitize_bool_strings(& $result)
	{
		foreach ($result as & $k)
		{
			$subscribed = $k["subscribed"];
			$k["subscribed"] = ($subscribed == "FALSE") ? FALSE : TRUE;
		}
	}

}
