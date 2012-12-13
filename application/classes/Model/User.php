<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
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
class Model_User extends Model_Auth_User {
	/**
	 * A user has many roles, tokens, buckets,
	 * actions, followers, subscriptions,
	 * channel_filters, accounts, comments and
	 * user identities
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		// auth
		'roles' => array('through' => 'roles_users', 'model' => 'role', 'far_key' => 'role_id'),
		'user_tokens' => array(),
		'buckets' => array(),
		'user_actions' => array(),
		'user_followers' => array(),
		'user_subscriptions' => array(),
		'channel_filters' => array(),
		'accounts' => array(),
		'comments' => array(),
		// for RiverID and other OpenID identities
		'user_identities' => array(),
		'account_collaborators' => array(),
		'river_collaborators' => array(
			'model' => 'River',
			'through' => 'river_collaborators',
			'far_key' => 'river_id'
		),
		'bucket_collaborators' => array(
			'model' => 'Bucket',
			'through' => 'bucket_collaborators',
			'far_key' => 'bucket_id'
		),
		'river_subscriptions' => array(
			'model' => 'River',
			'through' => 'river_subscriptions',
			'far_key' => 'river_id'
			),
		'bucket_subscriptions' => array(
			'model' => 'Bucket',
			'through' => 'bucket_subscriptions',
			'far_key' => 'bucket_id'
			),
		'following' => array(
			'model' => 'User',
			'through' => 'user_followers',
			'far_key' => 'user_id',
			'foreign_key' => 'follower_id'
			),		
		'followers' => array(
			'model' => 'User',
			'through' => 'user_followers',
			'far_key' => 'follower_id'
			),
		'droplet_scores' => array(),
		'comment_scores' => array()
		);
		
	protected $_has_one = array(
		'account' => array()
	);
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'created_date', 'format' => 'Y-m-d H:i:s');
	
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
				array('max_length', array(':value', 255)),
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
	 * Get a list of users whose name/email begins with the provided string
	 *
	 * @param $search_string
	 * @return array
	 */
	public static function get_like($search_string, $exclude_ids = array(), $limit = 10)
	{
		$users = array();
		$search_string = strtolower(trim($search_string));
		$users_query = ORM::factory('User')
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
		$rivers = array();
		
		// Rivers belonging to this user's account
		$rivers = array_merge($rivers, 
			$this->account->rivers->order_by('river_name', 'ASC')->find_all()->as_array());
		
		// Add rivers belonging to an account this user is collaborating on
		$account_collaborations = $this->account_collaborators
		    ->where("collaborator_active", "=", 1)		                               
		    ->find_all();
		
		foreach ($account_collaborations as $collabo)
		{
			$rivers = array_merge($rivers, 
				$collabo->account->rivers->order_by('river_name', 'ASC')->find_all()->as_array());
		}
		
		// Add individual rivers this user is collaborating on
		$river_collaborations = $this->river_collaborators
		                               ->where("collaborator_active", "=", 1)
		                               ->find_all();
		
		foreach ($river_collaborations as $river)
		{
			$rivers[] = $river;
		}
		
		// Add the rivers this user has subscribed to
		$rivers = array_merge($rivers, 
			$this->river_subscriptions->order_by('river_name', 'ASC')->find_all()->as_array());


		return array_unique($rivers);
	}
	
	/**
	* Get all a user's rivers that are visible to $visiting_user
	*
	* @return array
	*/
	public function get_rivers_array($visiting_user)
	{
		$ret = array();

		$rivers = $this->get_rivers();

		foreach ($rivers as $river)
		{
			$river_array = $river->get_array($this, $visiting_user);
			
			// Only return rivers $visiting_user has access to
			if ($river->account->user->id == $visiting_user->id OR 
				$river_array['collaborator'] OR 
				(bool) $river->river_public)
			{
				$ret[] = $river_array;
			}
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
		
		foreach ($bucket_collaborations as $bucket)
		{
			$buckets[] = $bucket;
		}
		
		// Add the buckets this user has subscribed to
		$buckets = array_merge($buckets, 
			$this->bucket_subscriptions->order_by('bucket_name', 'ASC')->find_all()->as_array());


		return array_unique($buckets);
	}
	
	/**
	 * Get all a user's bucekts that are visible to $visiting_user
	 *
	 * @param Model_User $visiting_user
	 * @return array
	 */
	public function get_buckets_array($visiting_user)
	{
		$ret = array();

		$buckets = $this->get_buckets();

		foreach ($buckets as $bucket)
		{
			$bucket_array = $bucket->get_array($this, $visiting_user);
			
			// Only return buckets $visiting_user has access to
			if ($bucket->user_id == $visiting_user->id OR 
				$bucket_array['collaborator'] OR 
				(bool) $bucket->bucket_publish)
			{
				$ret[] = $bucket_array;
			}
		}

		return $ret;
	}

	/**
	 * Gets the list of users following the currently loaded user 
	 * @return array
	 */
	public function get_followers()
	{
		$query = DB::select(array('uf.follower_id', 'id'), 
			array('u.name', 'user_name'), 'u.username', 'a.account_path')
		    ->from(array('users', 'u'))
		    ->join(array('user_followers', 'uf'))
		    ->on('uf.follower_id', '=', 'u.id')
		    ->join(array('accounts', 'a'))
		    ->on('a.user_id', '=', 'uf.follower_id')
		    ->where('uf.user_id', '=', $this->id);

		// Execute query and return results
		return $query->execute()->as_array();
	}

	/**
	 * Gets the list of users the currently loaded user is following
	 * @return array
	 */
	public function get_following()
	{
		$query = DB::select(array('u.id', 'id'), 
			array('u.name', 'user_name'), 'u.username', 'a.account_path')
		    ->from(array('users', 'u'))
		    ->join(array('user_followers', 'uf'))
		    ->on('uf.user_id', '=', 'u.id')
		    ->join(array('accounts', 'a'))
		    ->on('a.user_id', '=', 'uf.user_id')
		    ->where('uf.follower_id', '=', $this->id);

		// Execute query and return results
		return $query->execute()->as_array();

	}
	
	public static function new_user($email, $riverid_auth, $invite = FALSE)
	{
		$messages = array();
		
		// Check if an admin user is logged in
		$admin = FALSE;
		if (Auth::instance()->logged_in())
		{
			$admin = (Auth::instance()->get_user()->is_admin() || Model_Setting::get_setting('general_invites_enabled'));
		}
		
		if ( ! (bool) Model_Setting::get_setting('public_registration_enabled') AND ! $admin)
		{
			$messages['errors'] = array(__('This site is not open to public registration'));
		}
		else
		{
			if ( ! Valid::email($email))
			{
				$messages['errors'] = array(__('The email address provided is invalid'));
			} 
			else
			{
				if ($riverid_auth)
				{
					$messages = self::new_user_riverid($email, $invite);
				}
				else
				{
					$messages = self::new_user_orm($email, $invite);
				}
			}
		}		  
		
		return $messages;
	}
	
	/**
	* Send a river id registration request
	*
	*/
	private static function new_user_riverid($email, $invite = FALSE) 
	{
		$riverid_api = RiverID_API::instance();
		
		if ( $riverid_api->is_registered($email) AND ! $invite) 
		{
			return array('errors' => array(__('The email address provided is already registered.')));
		}
		
		$ret = array();
		$mail_body = NULL;
		if ($invite)
		{
			$mail_body = View::factory('emails/text/invite')
						 ->bind('secret_url', $secret_url);
			$mail_body->site_name = Model_Setting::get_setting('site_name');
			$mail_subject = __(':sitename Invite!', array(':sitename' => Model_Setting::get_setting('site_name')));
		}
		else
		{
			$mail_body = View::factory('emails/text/createuser')
						 ->bind('secret_url', $secret_url);
			$mail_subject = __(':sitename: Please confirm your email address', 
				array(':sitename' => Model_Setting::get_setting('site_name')));
		}
		$secret_url = URL::site('login/create/'.urlencode($email).'/%token%', TRUE, TRUE);
		$site_email = Swiftriver_Mail::get_default_address();
		
		$response = $riverid_api->request_password($email, $mail_body, $mail_subject, $site_email);
		
		if ($response['status']) 
		{
			$ret['messages'] = array(__('An email has been sent with instructions to complete the registration process.'));
		} 
		else 
		{
			$ret['errors'] = array($response['error']);
		}
		
		return $ret;
	}

	/**
	* New user registration for ORM auth
	*
	*/
	private static function new_user_orm($email, $invite = FALSE)
	{
		$ret = array();
		
		// Is the email registed in this site?
		$user = ORM::factory('User',array('email'=>$email));

		if ($user->loaded())
		{
			$ret['errors'] = array(__('The email address provided is already registered.'));
		}
		else
		{
			$auth_token = Model_Auth_Token::create_token('new_registration', array('email' => $email));
			if ($auth_token->loaded())
			{
				//Send an email with a secret token URL
				$mail_body = NULL;
				$mail_subject = NULL;
				if ($invite)
				{
					$mail_body = View::factory('emails/text/invite')
								 ->bind('secret_url', $secret_url);
					$mail_body->site_name = Model_Setting::get_setting('site_name');
					$mail_subject = __(':sitename Invite!', 
						array(':sitename' => Model_Setting::get_setting('site_name')));
				}
				else
				{
					$mail_body = View::factory('emails/text/createuser')
								 ->bind('secret_url', $secret_url);
					$mail_subject = __(':sitename: Please confirm your email address', 
						array(':sitename' => Model_Setting::get_setting('site_name')));
				}
				
				$secret_url = URL::site('login/create/'.urlencode($email).'/'.$auth_token->token, TRUE, TRUE);
				Swiftriver_Mail::send($email, $mail_subject, $mail_body); 


				$ret['messages'] = array(__('An email has been sent with instructions to complete the registration process.'));
			}
			else
			{
				$ret['errors'] = array($response['error']);
			}
		}
		
		return $ret;
	}
	
	
	public static function password_reset($email, $riverid_auth)
	{
		$messages = array();
		
		// Do the password reset depending on the auth driver we are using.
		if ($riverid_auth) 
		{
			$messages = self::password_reset_riverid($email);
		}
		else
		{
			$messages = self::password_reset_orm($email);
		}		  
		
		return $messages;
	}
	
	/**
	* Send a river id password reset request
	*
	*/	
	private static function password_reset_riverid($email)
	{
		$riverid_api = RiverID_API::instance();		            
		$mail_body = View::factory('emails/text/resetpassword')
					 ->bind('secret_url', $secret_url);		            
		$secret_url = URL::site('login/reset/'.urlencode($email).'/%token%', TRUE, TRUE);
		$site_email = Swiftriver_Mail::get_default_address();
		$mail_subject = __(':sitename: Password Reset', array(':sitename' => Model_Setting::get_setting('site_name')));
		$response = $riverid_api->request_password($email, $mail_body, $mail_subject, $site_email);
		
		$ret = array();
		if ($response['status']) 
		{
			$ret['messages'] = array(__('An email has been sent with instructions to complete the password reset process.'));
		} 
		else 
		{
			$ret['errors'] = array($response['error']);
		}
		
		return $ret;
	}

	/**
	* Password reset for ORM auth.
	*
	*/	
	private static function password_reset_orm($email)
	{
		$ret = array();
		$auth_token = Model_Auth_Token::create_token('password_reset', array('email' => $email));
		if ($auth_token->loaded())
		{
			//Send an email with a secret token URL
			$mail_body = View::factory('emails/text/resetpassword')
						 ->bind('secret_url', $secret_url);		            
			$secret_url = URL::site('login/reset/'.urlencode($email).'/'.$auth_token->token, TRUE, TRUE);
			$mail_subject = __(':sitename: Password Reset', array(':sitename' => Model_Setting::get_setting('site_name')));
			Swiftriver_Mail::send($email, $mail_subject, $mail_body);
			
			
			$ret['messages'] = array(
				__('An email has been sent with instructions to complete the password reset process.'));
		}
		else
		{
			$ret['errors'] = array(__('Error'));
		}
		
		return $ret;
	}

	/**
	 * Overrides the default delete behaviour
	 * Removes all the data associated with the user from
	 * the system. This data includes buckets, rivers, tags,
	 * collaborations, subscriptions and auth tokens
	 */
	public function delete()
	{
		// Does this user have an account space?
		$account = ORM::factory('Account')
		    ->where('user_id', '=', $this->id)
		    ->find();

		if ($account->loaded())
		{
			// Delete buckets - droplets, subscriptions and collaborations
			$buckets = ORM::factory('Bucket')
			    ->where('account_id', '=', $account->id)
			    ->find_all();
			foreach ($buckets as $bucket)
			{
				$bucket->delete();
			}

			// Delete rivers - droplets, subscriptions and collaborations
			$rivers = ORM::factory('River')
			    ->where('account_id', '=', $account->id)
			    ->find_all();
			foreach ($rivers as $river)
			{
				$river->delete();
			}

			// User created tags
			DB::delete('account_droplet_tags')
			    ->where('account_id', '=', $account->id)
			    ->execute();

			// User created places
			DB::delete('account_droplet_places')
			    ->where('account_id', '=', $account->id)
			    ->execute();

			// User created links
			DB::delete('account_droplet_links')
			    ->where('account_id', '=', $account->id)
			    ->execute();

			// User created media
			DB::delete('account_droplet_media')
			    ->where('account_id', '=', $account->id)
			    ->execute();
		}

		// Remove follows and list of followers
		DB::delete('user_followers')
		    ->where('user_id', '=', $this->id)
		    ->or_where('follower_id', '=', $this->id)
		    ->execute();

		// Accounts associated with the user
		DB::delete('accounts')
		    ->where('user_id', '=', $this->id)
		    ->execute();

		// User tokens
		DB::delete('user_tokens')
		    ->where('user_id', '=', $this->id)
		    ->execute();

		// Purge the logs - where the user has initiated an action
		// or an action has been performed on them
		DB::delete('user_actions')
		    ->where('user_id', '=', $this->id)
		    ->or_where('action_to_id', '=', $this->id)
		    ->execute();

		// Default
		parent::delete();
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function is_admin()
	{
		if ( ! ($admin_role = Cache::instance()->get('site_admin_role', FALSE)))
		{
			$admin_role = ORM::factory('Role',array('name'=>'admin'));
			Cache::instance()->set('site_admin_role', $admin_role, 86400 + rand(0,86400));
		}
		
		$cache_key = 'user_is_admin_'.$this->id;
		if ( ! ($is_admin = Cache::instance()->get($cache_key, FALSE)))
		{
			$is_admin = $this->has('roles', $admin_role);
			Cache::instance()->set($cache_key, $is_admin, 3600 + rand(0,3600));
		}
		
		return $is_admin;
	}
	
	
	/**
	 * Return the list of users who have the given user IDs
	 *
	 * @param    Array $ids List of user ids
	 * @return   Array Model_User array
	 */
	
	public static function get_users($ids)
	{
		$users = array();
		
		if ( ! empty($ids))
		{
			$query = ORM::factory('User')
						->where('id', 'IN', $ids);

			// Execute query and return results
			$users = $query->find_all()->as_array();
		}
		
		return $users;
	}
	
	/**
	 * Return user registered with the provided email address
	 *
	 * @param    string $email
	 * @return   Model_User
	 */
	public static function get_user_by_email($email)
	{
		return ORM::factory('User',array('email'=>$email));
	}
	
	/**
	 * Get the user's dashboard URL
	 *
	 * @return   string
	 */
	
	public function get_profile_url()
	{
		return URL::site($this->account->account_path);
	}
}
