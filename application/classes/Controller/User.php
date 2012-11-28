<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_User extends Controller_Swiftriver {
	
	/**
	 * sub content
	 */
	protected $sub_content;
	
	/**
	 * active
	 */
	protected $active;
	
	/**
	 * Is the visiting user the owner?
	 */
	protected $owner = FALSE;
	
	/**
	 * Buckets accessible by the current user
	 * @var araray
	 */
	protected $buckets = array();

	/**
	 * Rivers accessible by the current user
	 * @var array
	 */
	protected $rivers = array();
	
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
			$this->redirect('welcome', 302);
		}
		
		// Check if the visiting user is the owner of the account
		if ( $this->account->is_owner($this->visited_account->user->id))
		{
			$this->owner = TRUE;
		}
		
		// Is the account private?
		if ( ! $this->owner AND $this->visited_account->account_private)
		{
			$this->redirect($this->dashboard_url, 302);
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
		$this->template->content->nav = $this->get_nav($this->user);
			
		$following = $this->visited_account->user->following->find_all();
		$followers =  $this->visited_account->user->followers->find_all();
		$view_type = "dashboard";

		if ( ! $this->owner)
		{
			$follow_button = View::factory('template/follow');
			$follow_button->data = json_encode(array(
				'id' => $this->visited_account->user->id,
				'name' => $this->visited_account->user->name,
				'type' => 'user',
				'subscribed' => $this->user->has('following', $this->visited_account->user)
			));
			$follow_button->action_url = URL::site().$this->visited_account->account_path.'/user/user/manage';
			$this->template->content->follow_button = $follow_button;
		}
	}
	
	public function action_index()
	{
		if($this->owner)
		{
			$this->template->header->title = __('Dashboard');
			$this->template->header->js = View::factory('pages/user/js/main');
			
			$this->active = 'dashboard-navigation-link';
			
			$this->sub_content = View::factory('pages/user/main')
				->bind('owner', $this->owner)
				->bind('account', $this->visited_account);
			$gravatar_view = TRUE;
		}
		else
		{
			$this->template->header->title = __(':name\'s Profile', array(
				':name' =>  Text::limit_chars($this->visited_account->user->name)
				)
			);
			$this->template->header->js = View::factory('pages/user/js/profile');
			$this->template->header->js->visited_account = $this->visited_account;
			$this->template->header->js->bucket_list = json_encode($this->visited_account->user->get_buckets_array($this->user));
			$this->template->header->js->river_list = json_encode($this->visited_account->user->get_rivers_array($this->user));
			
			$this->sub_content = View::factory('pages/user/profile');
			$this->sub_content->account = $this->visited_account;
			$this->sub_content->anonymous = $this->anonymous;
			
			$gravatar_view = FALSE;
			$this->template->content->view_type = "user";
		}		
				
		// Activity stream
		$this->template->header->js .= HTML::script("themes/default/media/js/activities.js");
		$this->sub_content->activity_stream = View::factory('template/activities')
		    ->bind('activities', $activities)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner)
		    ->bind('gravatar_view', $gravatar_view);

		$fetch_url = URL::site().$this->visited_account->account_path.'/user/action/actions';

		$activity_list = Model_User_Action::get_activity_stream(
			$this->visited_account->user->id, $this->user->id, ! $this->owner);

		$this->sub_content->has_activity = count($activity_list) > 0;
		$activities = json_encode($activity_list);
	}


	/**
	 * Loads the rivers view page
	 */
	public function action_rivers()
	{
		$this->template->content->view_type = "";
		$this->sub_content = View::factory('pages/user/assets');
		$this->sub_content->owner = $this->owner;
		$this->sub_content->asset = 'river';
		$this->sub_content->visited_account = $this->visited_account;
		$this->sub_content->anonymous = $this->anonymous;
		$this->template->header->js = View::factory('pages/user/js/assets');
		$this->template->header->js->owner = $this->owner;
		$this->template->header->js->visited_account = $this->visited_account;
		$this->template->header->js->asset = 'river';
		
		if ( ! $this->owner)
		{
			$this->template->header->js->asset_list = json_encode($this->visited_account->user->get_rivers_array($this->user));
		}
	}

	/**
	 * Loads the bucket view page
	 */
	public function action_buckets()
	{
		$this->template->content->view_type = "";
		$this->sub_content = View::factory('pages/user/assets');
		$this->sub_content->owner = $this->owner;
		$this->sub_content->asset = 'bucket';
		$this->sub_content->visited_account = $this->visited_account;
		$this->sub_content->anonymous = $this->anonymous;
		$this->template->header->js = View::factory('pages/user/js/assets');
		$this->template->header->js->owner = $this->owner;
		$this->template->header->js->visited_account = $this->visited_account;
		$this->template->header->js->asset = 'bucket';
		
		if ( ! $this->owner)
		{
			$this->template->header->js->asset_list = json_encode($this->visited_account->user->get_buckets_array($this->user));
		}
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
				
				// Stalking!
				$user_orm = ORM::factory('User', $item_array['id']);
				if ( ! $user_orm->loaded())
				{
					throw new HTTP_Exception_404(
				        'The requested page :page was not found on this server.',
				        array(':page' => $page)
				        );
				}
				if ($user_orm->id == $this->user->id)
					throw new HTTP_Exception_400(); // Do not follow self
				
				// Are following
				if ($item_array['subscribed']  AND ! $this->user->has('following', $user_orm))
				{
					$this->user->add('following', $user_orm);
					Model_User_Action::create_action($this->user->id, 'user', 'follow', $user_orm->id);
					$this->notify_new_follower($user_orm);
				}
				
				// Are unfollowing
				if (! $item_array['subscribed'] AND $this->user->has('following', $user_orm))
				{
					$this->user->remove('following', $user_orm);
				}					
			break;
		}
	}
	
	/**
	 * @return	void
	 */
	private function notify_new_follower($user_orm)
	{
		// Send email notification after successful save
		$html = View::factory('emails/html/new_follower');
		$text = View::factory('emails/text/new_follower');
		$html->from_name = $text->from_name = $this->user->name;
		$html->avatar = Swiftriver_Users::gravatar($this->user->email, 80);
		$html->from_link = $text->from_link = URL::site($this->user->account->account_path, TRUE);
		$html->nickname = $text->nickname = $user_orm->account->account_path;
		$subject = __(':who if now following you of SwiftRiver!',
						array( ":who" => $this->user->name,
						));
		SwiftRiver_Mail::send($user_orm->email, 
							  $subject, $text->render(), $html->render());
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
			$this->redirect($this->dashboard_url, 302);
		}
		
		// Set the current page
		$this->active = 'settings-navigation-link';
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
				$mail_body = View::factory('emails/text/changeemail')
							 ->bind('secret_url', $secret_url);		            
        
				$secret_url = url::site('login/changeemail/'.urlencode($this->user->email).'/'.urlencode($new_email).'/%token%', TRUE, TRUE);
				$site_email = Swiftriver_Mail::get_default_address();
				$mail_subject = __(':sitename: Email Change', array(':sitename' => Model_Setting::get_setting('site_name')));
				$resp = RiverID_API::instance()
						    ->change_email($this->user->email, $new_email, $current_password,
						    	$mail_body, $mail_subject, $site_email);
        
				if ( ! $resp['status'])
				{
					$this->errors = $resp['error'];
					return;
				}    
			}
			else
			{
				// Make sure the new email address is not yet registered
				$user = ORM::factory('User',array('email'=>$new_email));
        
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
					$mail_body = View::factory('emails/text/changeemail')
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
			$account = ORM::factory('Account',array('account_path' => $nickname));
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
		$this->redirect(URL::site($this->user->account->account_path.'/settings'), 302);
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
			case "GET":
				$query_params = $this->request->query();
				$last_id = array_key_exists('last_id', $query_params)  ? intval($this->request->query('last_id')) : NULL;
				$since_id = array_key_exists('since_id', $query_params) ? intval($this->request->query('since_id')) : NULL;
				
				$activity_list = Model_User_Action::get_activity_stream(
											$this->visited_account->user->id, 
											$this->user->id, 
											! $this->owner,
											$last_id,
											$since_id);
											
				if (empty($activity_list))
					throw new HTTP_Exception_404();
				
				echo json_encode($activity_list);
				break;
			case "PUT":
				$action_array = json_decode($this->request->body(), TRUE);
				
				$action_id = intval($action_array['id']);
				$action_orm = ORM::factory('User_Action', $action_id);
				
				if ( ! $action_orm->loaded())
					throw new HTTP_Exception_404();
				
				if ( $this->user->id != $action_orm->action_to_id)
					throw new HTTP_Exception_403(); // User can only accept own invites
				
				
				// Get the collaboration being saved
				$collaborator_orm = NULL;
				switch ($action_orm->action_on)
				{
					case "account":
						$collaborator_orm = ORM::factory('Account_Collaborator')
						                       ->where('account_id', '=', $action_orm->action_on_id)
						                       ->where('user_id', '=', $action_orm->action_to_id)
						                       ->find();
					break;
					case "river":
						$collaborator_orm = ORM::factory('River_Collaborator')
						                       ->where('river_id', '=', $action_orm->action_on_id)
						                       ->where('user_id', '=', $action_orm->action_to_id)
						                       ->find();
					break;
					case "bucket":
						$collaborator_orm = ORM::factory('Bucket_Collaborator')
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
					
					// Notify owners
					$this->notify_new_collaborator($collaborator_orm);
				}
				else
				{
					$action_orm->delete();
					$collaborator_orm->delete();
				}
			break;
		}
			
	}
	
	private function notify_new_collaborator($collaborator_orm)
	{
		// Send email notification after successful save
		$html = View::factory('emails/html/collaboration_invite_accepted');
		$text = View::factory('emails/text/collaboration_invite_accepted');
		$html->from_name = $text->from_name = $collaborator_orm->user->name;
		$html->avatar = Swiftriver_Users::gravatar($collaborator_orm->user->email, 80);
		$html->from_link = URL::site($collaborator_orm->user->account->account_path, TRUE);
		$subject = __(':from accepted your collaboration invite!',
						array( ":from" => $collaborator_orm->user->name,
						));
		
		$owners = NULL;
		$to = NULL;
		if ($collaborator_orm instanceof Model_River_Collaborator)
		{
			$to = '"'.$collaborator_orm->river->account->user->name.'" <'.$collaborator_orm->river->account->user->email.'>';
			$owners = $collaborator_orm->river->get_collaborators(TRUE);
			$text->link = $html->asset_link = URL::site($collaborator_orm->river->get_base_url(), TRUE);
			$html->asset_name = $text->asset_name = $collaborator_orm->river->river_name;
			$html->asset = $text->asset = "river";
		}
		elseif ($collaborator_orm instanceof Model_Bucket_Collaborator)
		{
			$to = '"'.$collaborator_orm->bucket->account->user->name.'" <'.$collaborator_orm->bucket->account->user->email.'>';
			$owners = $collaborator_orm->bucket->get_collaborators(TRUE);
			$text->link = $html->asset_link = URL::site($collaborator_orm->bucket->get_base_url(), TRUE);
			$html->asset_name = $text->asset_name = $collaborator_orm->bucket->bucket_name;
			$html->asset = $text->asset = "bucket";
		}
		
		foreach ($owners as $owner)
		{
			if ($collaborator_orm->user->id == $owner['id'])
				continue; // Skip the just added collaborator
			
			if ($to)
			{
				$to .= ', ';
			}
			$to .= '"'.$owner['name'].'" '.'<'.$owner['email'].'>';
		}
		
		SwiftRiver_Mail::send($to, $subject, $text->render(), $html->render());
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
				$account = ORM::factory('Account',array('account_path'=>strtolower($nickname)));
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
					$this->redirect(URL::site().$this->user->account->account_path, 302);
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
			$follower['subscribed'] = in_array($follower['id'], $following);
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
			$follow['subscribed'] = TRUE;
			$follow['type'] = "user";
		}

		$follower_list = json_encode($following);
		$fetch_url = URL::site().$this->visited_account->account_path.'/user/followers/manage';
	}

	public function action_invite()
	{
		if ( ! Model_Setting::get_setting('general_invites_enabled') OR ! $this->owner)
			$this->redirect($this->dashboard_url, 302);

		// Set the current page
		$this->active = 'invite-navigation-link';
		$this->template->content->view_type = 'invites';

		$this->sub_content = View::factory('pages/user/invite')
			->bind('user', $this->user);

		if ($this->request->post())
		{
			$errors = array();
			$messages = array();
			$count = 0;
			$valid_emails = array();
			$emails = explode(', ', $this->request->post('emails'));
			foreach ($emails as $k => $email)
			{
				if ($count == $this->user->invites)
					break;
				
				$email = trim($email);
				if ( ! Valid::email($email, TRUE))
				{
					$errors[] = 'The email address "'.$email.'" is invalid.';
					continue;
				}

				$new = Model_User::new_user($email, $this->riverid_auth, TRUE);

				if (isset($new['errors']))
				{
					$errors[] = $email.' - '.implode(" ",$new['errors']);
					continue;
				}

				$valid_emails[] = $email;
				$count++;
			}

			$this->user->invites -= $count;
			$this->user->save();

			foreach ($valid_emails as $email)
			{
				$messages[] = 'Invite sent to "'.$email.'" successfully!';
			}
			if (count($errors) > 0)
			{
				$this->sub_content->bind('errors', $errors);
			}
			if (count($messages) > 0)
			{
				$this->sub_content->bind('messages', $messages);
			}
		}
	}	
	
	/**
	 * Dashboard Navigation Links
	 * 
	 * @param string $user - logged in user
	 * @return	array $nav
	 */
	protected static function get_nav($user)
	{
		$nav = array();

		// List
		$nav[] = array(
			'id' => 'dashboard-navigation-link',
			'url' => '',
			'label' => __('Dashboard')
		);
        
		// Drops
		$nav[] = array(
			'id' => 'settings-navigation-link',
			'url' => '/settings',
			'label' => __('Settings')
		);

		// Invite
		if (Model_Setting::get_setting('general_invites_enabled') AND
			$user->invites > 0)
		{
			$nav[] = array(
				'id' => 'invite-navigation-link',
				'url' => '/invite',
				'label' => __('Invites')
			);
		}
		
		
		// SwiftRiver Plugin Hook -- Add Nav Items
		Swiftriver_Event::run('swiftriver.dashboard.nav', $nav);

		return $nav;
	}
}
