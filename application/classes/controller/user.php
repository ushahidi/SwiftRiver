<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_User extends Controller_Swiftriver {
	
	/**
	 * sub content
	 */
	private $sub_content;
	
	/**
	 * active
	 */
	private $active;
	
	/**
	 * Is the visiting user the owner?
	 */
	protected $owner = FALSE;
	
	/**
	 * Buckets accessible by the current user
	 * @var araray
	 */
	private $buckets = array();

	/**
	 * Rivers accessible by the current user
	 * @var array
	 */
	private $rivers = array();
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Anonymous user doesn't have a profile
		if ( $this->visited_account->user->username == 'public')
		{
			Request::current()->redirect('welcome');
		}
		
		// Check if the visiting user is the owner of the account
		if ( $this->account->is_owner($this->visited_account->user->id))
		{
			$this->owner = TRUE;
		}
		
		// Is the account private?
		if ( ! $this->owner AND $this->visited_account->account_private)
		{
			$this->request->redirect($this->dashboard_url);
		}

		$this->template->content = View::factory('pages/user/layout')
			->bind('account', $this->visited_account)
			->bind('owner', $this->owner)
			->bind('active', $this->active)
			->bind('sub_content', $this->sub_content)
			->bind('anonymous', $this->anonymous)
			->bind('followers', $followers)
			->bind('following', $following)
			->bind('view_type', $view_type);
			
		$following = $this->visited_account->user->following->find_all();
		$followers =  $this->visited_account->user->followers->find_all();
		$view_type = "dashboard";

		// Some info about the owner of the user profile being visited
		// Will be used later for following unfollowing
		$this->template->content->fetch_url = URL::site().$this->visited_account->account_path.'/user/user/manage';

		$this->template->content->user_item = json_encode(array(
				"id" => $this->visited_account->user->id,
				"type" => "user",					
				"item_name" => $this->visited_account->user->name,
				"item_url" => URL::site().$this->visited_account->account_path,
				"following" => $this->user->has('following', $this->visited_account->user),
				"is_owner" => $this->user->id == $this->visited_account->user->id				
			));
	}
	
	public function action_index()
	{
		if($this->owner)
		{
			$this->template->header->title = __('Dashboard');
		}
		else
		{
			$this->template->header->title = __(':name\'s Profile', array(
				':name' =>  Text::limit_chars($this->visited_account->user->name)
				)
			);
		}
		
		if ($this->owner)
		{
			$this->active = 'main';

			$this->sub_content = View::factory('pages/user/main')
				->bind('activity_stream', $activity_stream)
				->bind('owner', $this->owner)
				->bind('account', $this->visited_account)
				->bind('has_activity', $has_activity)
				->bind('profile_js', $profile_js);

			$profile_js = $this->_get_profile_js(TRUE);
			$gravatar_view = TRUE;
		}
		else
		{
			$this->_prepare_user_profile_view($activity_stream);
			$gravatar_view = FALSE;
			$this->template->content->view_type = "user";
		}
		
		// Activity stream
		$activity_stream = View::factory('template/activities')
		    ->bind('activities', $activities)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner)
		    ->bind('gravatar_view', $gravatar_view);

		$fetch_url = URL::site().$this->visited_account->account_path.'/user/action/actions';

		$activity_list = Model_User_Action::get_activity_stream(
			$this->visited_account->user->id, ! $this->owner);

		$has_activity = count($activity_list) > 0;
		$activities = json_encode($activity_list);

	}


	/**
	 * Loads the rivers view page
	 */
	public function action_rivers()
	{
		$this->template->content->view_type = "";

		$this->sub_content = View::factory('pages/user/rivers_buckets')
		    ->bind('list_items', $list_items)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner)
		    ->bind('item_type', $item_type)
		    ->bind('item_owner', $item_owner);
		
		$item_type = "rivers";
		$item_owner = $this->visited_account->user->name;

		// Page title
		if ($this->owner)
		{
			$this->template->header->title = __("My Rivers");

			$this->sub_content->subscriber_header = __("Rivers you follow");
		}
		else
		{
			$this->template->header->title = __(':name\'s Rivers', array(
				':name' => Text::limit_chars($this->visited_account->user->name)
		    ));

		}

		$this->sub_content->owner_header = $this->template->header->title;
		$list_items = json_encode($this->_get_rivers_list());
		$fetch_url = URL::site().$this->visited_account->account_path.'/user/river/manage';
	}

	/**
	 * Loads the bucket view page
	 */
	public function action_buckets()
	{
		$this->template->content->view_type = "";

		$this->sub_content = View::factory('pages/user/rivers_buckets')
		    ->bind('list_items', $list_items)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner)
		    ->bind('item_type', $item_type)
		    ->bind('item_owner', $item_owner);

		$item_type = "buckets";
		$item_owner = $this->visited_account->user->name;
		
		// Page title
		if ($this->owner)
		{
			$this->template->header->title = __("My Buckets");
			$this->sub_content->subscriber_header = __("Buckets you follow");
		}
		else
		{
			$this->template->header->title = __(':name\'s Buckets', array(
				':name' => Text::limit_chars($this->visited_account->user->name)
		    ));
		}

		$this->sub_content->owner_header = $this->template->header->title;
		$list_items = json_encode($this->_get_buckets_list());
		$fetch_url = URL::site().$this->visited_account->account_path.'/user/bucket/manage';
	}


	/**
	 * Prepares profile page of the current user with all the 
	 * necessary data
	 */
	private function _prepare_user_profile_view(& $activity_stream)
	{
		$this->sub_content = View::factory('pages/user/profile')
		    ->bind('activity_stream', $activity_stream)
		    ->bind('owner', $this->owner)
		    ->bind('profile_js', $profile_js)
		    ->bind('account', $this->visited_account)
		    ->bind('buckets', $this->buckets)
		    ->bind('rivers', $this->rivers);

		// Get the javascript
		$profile_js = $this->_get_profile_js();
	}

	/**
	 * Loads and returns the JS snippet for populating the river and bucket 
	 * views in the main and profile view dashboard pages
	 * @return string
	 */
	private function _get_profile_js($dashboard_view = FALSE)
	{
		$profile_js = View::factory('pages/user/js/profile')
		    ->bind('rivers_list', $rivers_list)
		    ->bind('buckets_list', $buckets_list)
		    ->bind('river_fetch_url', $river_fetch_url)
		    ->bind('bucket_fetch_url', $bucket_fetch_url)
		    ->bind('dashboard_view', $dashboard_view)
		    ->bind('owner', $this->owner);
		
		$base_path = URL::site().$this->visited_account->account_path.'/user/';
		

		$river_fetch_url = $base_path.'river/manage';
		$bucket_fetch_url = $base_path.'bucket/manage';

		$this->rivers =  $this->_get_rivers_list(FALSE);
		$this->buckets = $this->_get_buckets_list(FALSE);

		$buckets_list = json_encode($this->buckets);
		$rivers_list = json_encode($this->rivers);

		return $profile_js;
	}

	/**
	 * Generates the list of rivers available to the current user
	 *
	 * @param bool $standardize When TRUE, uses the "item_" prefix for the keys
	 * @return array
	 */
	private function _get_rivers_list($standardize = TRUE)
	{
		$visited_user_id = $this->visited_account->user->id;

		$rivers = ($this->owner)
		    ? $this->user->get_rivers()
		    : $this->user->get_other_user_visible_rivers($visited_user_id);

		$items = array();
		foreach ($rivers as & $river)
		{
			$river_url = URL::site().$river['river_url'];

			$is_owner = is_string($river['is_owner']) 
			    ? (($river['is_owner'] == 'FALSE') ? FALSE : TRUE)
			    : $river['is_owner'];

			$subscribed = is_string($river['subscribed']) 
			    ? (($river['subscribed'] == 'FALSE') ? FALSE: TRUE)
			    : $river['subscribed'];

			if ( ! $standardize)
			{
				$river['river_url'] = $river_url;
				$river['is_owner'] = $is_owner;
				$river['subscribed'] = $subscribed;
			}
			else
			{
				$items[] = array(
					'id' => $river['id'],
					'type' => $river['type'],
					'item_name' => $river['river_name'],
					'item_url' => $river_url,
					'subscribed' => $subscribed,
					'is_owner' => $is_owner,
					'subscriber_count' => $river['subscriber_count']
				);
			}
		}

		return  ($standardize) ? $items : $rivers;
	}

	/**
	 * Generates the list of buckets available/accessible to the current user
	 *
	 * @param bool $standardize When TRUE, uses the "item_" prefix for the keys
	 * @return array
	 */
	private function _get_buckets_list($standardize = TRUE)
	{
		$visited_user_id = $this->visited_account->user->id;

		$buckets = ($this->owner)
		    ? $this->user->get_buckets()
		    : $this->user->get_other_user_visible_buckets($visited_user_id);

		$items = array();
		foreach ($buckets as & $bucket)
		{
			
			$bucket_url = URL::site().$bucket['bucket_url'];
			$is_owner = is_string($bucket['is_owner']) 
			    ? (($bucket['is_owner'] == 'FALSE') ? FALSE : TRUE)
			    : $bucket['is_owner'];

			$subscribed = is_string($bucket['subscribed']) 
			    ? (($bucket['subscribed'] == 'FALSE') ? FALSE: TRUE)
			    : $bucket['subscribed'];
			    
			if ( ! $standardize)
			{
				$bucket['bucket_url'] = $bucket_url;
				$bucket['subscribed'] = $subscribed;
				$bucket['is_owner'] = $is_owner;
			}
			else
			{
				$items[] = array(
					'id' => $bucket['id'],
					'type' => $bucket['type'],
					'item_name' => $bucket['bucket_name'],
					'item_url' => $bucket_url,
					'subscribed' => $subscribed,
					'is_owner' => $is_owner,
					'subscriber_count' => $bucket['subscriber_count']
				);
			}
		}

		return ($standardize) ? $items : $buckets;
	}

	
	/**
	 * @return	void
	 */
	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		if ($this->anonymous)
		{
			// No anonymous allowed here
			throw new HTTP_Exception_403();
		}
		
		switch ($this->request->method())
		{
			case "PUT":
				$item_array = json_decode($this->request->body(), TRUE);
				
				if ($item_array['type'] == 'river') 
				{
					
					$river_orm = ORM::factory('river', $item_array['id']);
					if ( ! $river_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are we adding a subscription?
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->add('river_subscriptions', $river_orm);
					}
					
					// Are we removing a subscription?
					if ($item_array['subscribed'] == 0 AND 
						$this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->remove('river_subscriptions', $river_orm);
					}					
				}
				
				if ($item_array['type'] == 'bucket') 
				{
					
					$bucket_orm = ORM::factory('bucket', $item_array['id']);
					if ( ! $bucket_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are we adding a subscription?
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('bucket_subscriptions', $bucket_orm))
					{
						$this->user->add('bucket_subscriptions', $bucket_orm);
					}
					
					// Are we removing a subscription?
					if ($item_array['subscribed'] == 0 AND 
						$this->user->has('bucket_subscriptions', $bucket_orm))
					{
						$this->user->remove('bucket_subscriptions', $bucket_orm);
					}					
				}
				
				// Stalking!
				if ($item_array['type'] == 'user') 
				{
					
					$user_orm = ORM::factory('user', $item_array['id']);
					if ( ! $user_orm->loaded())
					{
						throw new HTTP_Exception_404(
					        'The requested page :page was not found on this server.',
					        array(':page' => $page)
					        );
					}
					
					// Are following
					if ($item_array['following'] == 1 AND 
						! $this->user->has('following', $user_orm))
					{
						$this->user->add('following', $user_orm);
					}
					
					// Are unfollowing
					if ($item_array['following'] == 0 AND 
						$this->user->has('following', $user_orm))
					{
						$this->user->remove('following', $user_orm);
					}					
				}
				
			break;
			case "DELETE":
				$item_type = $this->request->param('name', 0);
				$id = $this->request->param('id', 0);
				
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				if ($item_type == 'bucket') {
					$bucket_orm = ORM::factory('bucket', $id);
					$bucket_orm->delete();
				}
				if ($item_type == 'river') {
					$river_orm = ORM::factory('river', $id);
					$river_orm->delete();
				}
				
			break;
		}
	}
	
	/**
	 * Account settings
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		if ( ! $this->owner)
		{
			$this->request->redirect($this->dashboard_url);
		}
		
		// Set the current page
		$this->active = 'settings';
		$this->template->content->view_type = 'settings';
		$this->template->header->js = View::factory('pages/user/js/settings');
		$this->template->header->js->user = $this->user;

		$this->sub_content = View::factory('pages/user/settings')
		    ->bind('user', $this->user)
		    ->bind('errors', $this->errors);
		
		if ( ! empty($_POST))
		{
			$this->_update_settings();
		}
		
		$session = Session::instance();
		$this->sub_content->messages = $session->get('messages');
		$session->delete('messages');
	}
	
	private function _update_settings()
	{	
		// Validate current password
		$validated = FALSE;
		$current_password = $_POST['current_password'];
		if ($this->riverid_auth)
		{
			$response = RiverID_API::instance()->signin($this->user->email, $_POST['current_password']);
			$validated = ($response AND $response['status']);
		}
		else
		{
			$validated =  (Auth::instance()->hash($current_password) == $this->user->password);
		}
        
		if ( ! $validated)
		{
			$this->errors = __('Current password is incorrect');
			return;
		}
		
		$messages = array();
        
		// Password is changing and we are using RiverID authentication
		if ( ! empty($_POST['password']) OR ! empty($_POST['password_confirm']))
		{
			$post = Model_Auth_User::get_password_validation($_POST);
			if ( ! $post->check())
			{
				$this->errors = $post->errors('user');
				return;
			}
        
			// Are we using RiverID?
			if ($this->riverid_auth)
			{
				$resp = RiverID_API::instance()
						   ->change_password($this->user->email, $_POST['current_password'], $_POST['password']);
        
				if ( ! $resp['status'])
				{
					$this->errors = $resp['error'];
					return;
				}
        
				// For API calls below, use this new password
				$current_password = $_POST['password'];
				unset($_POST['password'], $_POST['password_confirm']);
			}			        
		}
        
		// Email address is changing
		if ($_POST['email'] != $this->user->email)
		{
			$new_email = $_POST['email'];
        
			if ( ! Valid::email($new_email))
			{
				$this->errors = __('Invalid email address');
				return;
			}
        
			if ($this->riverid_auth)
			{
				// RiverID email change process
				$mail_body = View::factory('emails/changeemail')
							 ->bind('secret_url', $secret_url);		            
        
				$secret_url = url::site('login/changeemail/'.urlencode($this->user->email).'/'.urlencode($new_email).'/%token%', TRUE, TRUE);
				$site_email = Kohana::$config->load('useradmin.email_address');
				$mail_subject = __(':sitename: Email Change', array(':sitename' => Model_Setting::get_setting('site_name')));
				$resp = RiverID_API::instance()
					->change_email($this->user->email, $new_email, $current_password, $mail_body, $mail_subject, $site_email);
        
				if ( ! $resp['status'])
				{
					$this->errors = $resp['error'];
					return;
				}    
			}
			else
			{
				// Make sure the new email address is not yet registered
				$user = ORM::factory('user',array('email'=>$new_email));
        
				if ($user->loaded())
				{
					$this->errors = __('The new email address has already been registered');
					return;
				}
        
				$auth_token = Model_Auth_Token::create_token('change_email', array(
					'new_email' => $new_email,
					'old_email' => $this->user->email
					));
				if ($auth_token->loaded())
				{
					// Send an email with a secret token URL
					$mail_body = View::factory('emails/changeemail')
									   ->bind('secret_url', $secret_url);		            
        
					$secret_url = URL::site('login/changeemail/'
											.urlencode($this->user->email)
											.'/'
											.urlencode($new_email)
											.'/'
											.$auth_token->token, TRUE, TRUE);
        
					// Send email to the user using the new address
					$mail_subject = __(':sitename: Email Change', array(':sitename' => Model_Setting::get_setting('site_name')));
					Swiftriver_Mail::send($new_email, $mail_subject, $mail_body);
				}
				else
				{
					$this->errors = __('Error');
					return;
				}
				
				$messages[] = __("A confirmation email has been sent to :email", array(':email' => $new_email));
			}
        
			// Don't change email address immediately.
			// Only do so after the tokens sent above are validated
			unset($_POST['email']);
        
		} // END if - email address change
        
		// Nickname is changing
		if ($_POST['nickname'] != $this->user->account->account_path)
		{
			$nickname = $_POST['nickname'];
			// Make sure the account path is not already taken
			$account = ORM::factory('account',array('account_path' => $nickname));
			if ($account->loaded())
			{
				$this->errors = __('Nickname is already taken');
				return;
			}
        
			// Update
			$this->user->account->account_path = $nickname;
			$this->user->account->save();
		}
        
        
		$this->user->update_user($_POST, array('name', 'password', 'email'));
        
		$messages[] = __("Account settings were saved successfully.");
		Session::instance()->set("messages", $messages);
		$this->request->redirect(URL::site($this->user->account->account_path.'/settings'));
	}
	
	
	/**
	 * Actions restful api
	 *
	 * @return	void
	 */
	public function action_actions()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{			
			case "POST":
				$action_array = json_decode($this->request->body(), TRUE);
				$action_id = intval($action_array['action_id']);
				$action_orm = ORM::factory('user_action', $action_id);
				
				if ( ! $action_orm->loaded())
					return;
				
				
				// Get the collaboration being saved
				$collaborator_orm = NULL;
				switch ($action_orm->action_on)
				{
					case "account":
						$collaborator_orm = ORM::factory('account_collaborator')
						                       ->where('account_id', '=', $action_orm->action_on_id)
						                       ->where('user_id', '=', $action_orm->action_to_id)
						                       ->find();
					break;
					case "river":
						$collaborator_orm = ORM::factory('river_collaborator')
						                       ->where('river_id', '=', $action_orm->action_on_id)
						                       ->where('user_id', '=', $action_orm->action_to_id)
						                       ->find();
					break;
					case "bucket":
						$collaborator_orm = ORM::factory('bucket_collaborator')
						                       ->where('bucket_id', '=', $action_orm->action_on_id)
						                       ->where('user_id', '=', $action_orm->action_to_id)
						                       ->find();
					break;
				}
				
				// Are we confirming?
				if ($action_array['confirmed'])
				{
					$action_orm->confirmed = $action_array['confirmed'];
					
					if ($collaborator_orm and $collaborator_orm->loaded())
					{
						$collaborator_orm->collaborator_active = $action_array['confirmed'];
						$collaborator_orm->save();
						$action_orm->save();
					}
				}
				else
				{
					$action_orm->delete();
					$collaborator_orm->delete();
				}
			break;
		}
			
	}
	
	/**
	 * Initial registration page
	 *
	 * @return	void
	 */
	public function action_register()
	{
		$this->template->content = View::factory('pages/user/register')
		                               ->bind('user', $this->user);
		
		if ($this->request->method() == "POST")
		{
			$post = Validation::factory($this->request->post())
				              ->rule('name', 'not_empty')
				              ->rule('nickname', 'not_empty')
				              ->rule('nickname', 'alpha_dash');
				
			if ( $post->check())
			{
				$nickname = $this->request->post('nickname');
				
				// Check if the nickname is already taken
				// Make sure the account path is not already taken
				$account = ORM::factory('account',array('account_path'=>strtolower($nickname)));
				if ($account->loaded())
				{
					$this->template->content->errors = array(__('Nickname is already taken'));
					
					// Set below for the template to show the previously entered values
					$this->user->account->account_path = $nickname;
					$this->user->name = $this->request->post('name');
				}
				else
				{
					// The data checks out, create the account and proceed to Swift!
					$this->user->account->account_path = $nickname;
					$this->user->account->save();
					$this->user->name = $this->request->post('name');
					$this->user->save();
					Request::current()->redirect(URL::site().$this->user->account->account_path);
					return;	
				}
			}
			else
			{
				// Display the errors
				$this->template->content->errors = $post->errors("validation");
			}
		}
	}

	/**
	 * Loads the dialog for creating a new item (bucket|river|drop)
	 */
	public function action_create()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$modal_create =  View::factory('dialogs/modal_create')
		    ->bind('account', $this->account);

		echo $modal_create;
	}

	/**
	 * REST endpoint for sharing droplets via email
	 */
	public function action_share()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		if ($_POST)
		{
			// Extract the input data
			$post = Arr::extract($_POST, array('form_auth_id', 'recipient', 
				'subject', 'body'));

			// Setup validation
			$validation = Validation::factory($post)
			    ->rule('recipient', 'not_empty')
			    ->rule('recipient', 'email')
			    ->rule('subject', 'not_empty')
			    ->rule('body', 'not_empty');

			// Validate
			if ( ! $validation->check())
			{
				$this->response->status(400);
			}
			else
			{
				// Modify the mail body to include the email address of the
				// use sharing content
				$mail_body = __(':sender has shared an item with you."\r\n":body',
					array(':sender' => $this->user->username, ':body' => $post['body']));

				// Send the email
				Swiftriver_Mail::send($post['recipient'], $post['subject'], $mail_body);
			}
		}
		else
		{
			throw new HTTP_Exception_405("Only HTTP POST requests are allowed");
		}
	}

	/**
	 * Loads the list of followers
	 */
	public function action_followers()
	{
		$this->template->header->title = __("Followers");

		$this->sub_content = View::factory('pages/user/followers')
		    ->bind('owner', $this->owner)
		    ->bind('follower_list', $follower_list)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('account_owner', $account_owner)
		    ->bind('user', $this->user);

		$this->sub_content->header_title = __("Followers");
		$this->sub_content->following_mode = FALSE;
		$account_owner = $this->visited_account->user->name;

		// Get the list of users the curernt user is following
		$following = array();
		foreach ($this->user->get_following() as $follow)
		{
			$following[] = $follow['id'];
		}

		$followers = ($this->owner) 
		    ? $this->user->get_followers() 
		    : $this->visited_account->user->get_followers();

		foreach ($followers as & $follower)
		{
			$follower['user_avatar'] = Swiftriver_Users::gravatar($follower['username'], 35);
			$follower['user_url'] = URL::site().$follower['account_path'];
			$follower['following'] = in_array($follower['id'], $following);
			$follower['type'] = "user";
		}

		$follower_list = json_encode($followers);
		$fetch_url = URL::site().$this->visited_account->account_path.'/user/followers/manage';
	}

	/**
	 * Displays the users being followed
	 */
	public function action_following()
	{
		$this->template->header->title = __("Following");
		$this->sub_content = View::factory('pages/user/followers')
		    ->bind('owner', $this->owner)
		    ->bind('follower_list', $follower_list)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('account_owner', $account_owner)
		    ->bind('user', $this->user);

		$this->sub_content->header_title = __("Following");
		$this->sub_content->following_mode = TRUE;
		$account_owner = $this->visited_account->user->name;

		$following = ($this->owner) 
		    ? $this->user->get_following() 
		    : $this->visited_account->user->get_following();

		foreach ($following as & $follow)
		{
			$follow['user_avatar'] = Swiftriver_Users::gravatar($follow['username'], 35);
			$follow['user_url'] = URL::site().$follow['account_path'];
			$follow['following'] = TRUE;
			$follow['type'] = "user";
		}

		$follower_list = json_encode($following);
		$fetch_url = URL::site().$this->visited_account->account_path.'/user/followers/manage';	}

}