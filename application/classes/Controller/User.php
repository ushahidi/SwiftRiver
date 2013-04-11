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
		
		$this->owner = $this->visited_account['id'] == $this->user['id'];
		
		$this->template->content = View::factory('pages/user/layout')
			->bind('account', $this->visited_account)
			->bind('sub_content', $this->sub_content)
			->bind('active', $this->active);

		$this->template->content->set('show_navigation', TRUE)
				->set('nav', $this->get_nav($this->user));
			
		$following = array();
		$followers =  array();
		$view_type = "dashboard";

		if ( ! $this->owner)
		{
			$is_following = $this->account_service->is_account_follower($this->visited_account['id'], $this->user['id']);
		
			$follow_button = View::factory('template/follow');
			$follow_button->data = json_encode(array(
				'id' => $this->user['id'],
				'name' => $this->visited_account['owner']['name'],
				'type' => 'user',
				'following' => $is_following
			));
			$follow_button->action_url = URL::site($this->visited_account['account_path'].'/user/followers/manage');
			$this->template->content->follow_button = $follow_button;
		}
	}
	
	public function action_index()
	{
		$this->template->header->title = __('Dashboard');
		$this->active = 'activities-navigation-link';
			
		$this->sub_content = View::factory('pages/user/activity')
			->bind('owner', $this->owner)
			->bind('account', $this->visited_account);
	}
	
	public function action_content()
	{
		$this->template->header->title = __('Content');
		$this->template->header->js = View::factory('pages/user/js/content')
			->bind('rivers', $rivers)
			->bind('forms', $forms)				
			->bind('buckets', $buckets)
			->bind('owner', $this->owner);
		
		$rivers = json_encode($this->account_service->get_rivers($this->visited_account, $this->user));
		$buckets = json_encode($this->account_service->get_buckets($this->visited_account, $this->user));
		$forms = json_encode($this->account_service->get_forms($this->visited_account, $this->user));

		$this->sub_content = View::factory('pages/user/content')
			->bind('owner', $this->owner);
		$this->sub_content->asset_templates = View::factory('template/assets');

		$this->active = 'content-navigation-link';
	}


	/**
	 * Loads the rivers view page
	 */
	public function action_rivers()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$river_array = json_decode($this->request->body(), TRUE);
				
				$post = Validation::factory($river_array)
				            ->rule('name', 'not_empty');
				
				if ( ! $post->check())
				{
					throw new HTTP_Exception_400();
				}
				
				try
				{
					$response = $this->river_service->create_river_from_array($river_array);
					echo json_encode($response);
				}
				catch (Swiftriver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}
			break;
			
			case "DELETE":
				$river_id = $this->request->param('id', 0);
				$this->river_service->delete_river($river_id);
			break;
		}
		
	}

	/**
	 * XHR endpoint for managing a user's buckets
	 */
	public function action_buckets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST";
				$payload = json_decode($this->request->body(), TRUE);
				if (Valid::not_empty($payload['name']))
				{
					$bucket_name = $payload['name'];
					$bucket = $this->bucket_service->create_bucket($bucket_name, $this->user);
					
					$this->response->headers("Content-Type", "application/json;charset=UTF-8");
					echo json_encode($bucket);
				}				
			break;
			
			case "DELETE";
				$bucket_id = $this->request->param('id', 0);
				$this->bucket_service->delete_bucket($bucket_id);
			break;
		}
	}
	
	/**
	 * Endpoint for managing forms
	 */
	public function action_forms()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$form_array = json_decode($this->request->body(), TRUE);
			
				$post = Validation::factory($form_array)
							->rule('name', 'not_empty')
							->rule('fields', 'not_empty');
			
				if ( ! $post->check())
				{
					throw new HTTP_Exception_400();
				}
			
				try
				{
					$response = $this->form_service->create_form($form_array);
					echo json_encode($response);
				}
				catch (Swiftriver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}
				
			break;
			
			case "PUT":
				$form_array = json_decode($this->request->body(), TRUE);
				
				$post = Validation::factory($form_array)
							->rule('name', 'not_empty');
			
				if ( ! $post->check())
				{
					throw new HTTP_Exception_400();
				}
				
				try
				{
					$form_id = intval($this->request->param('id', 0));
					$response = $this->form_service->update_form($form_id, $form_array['name']);
					echo json_encode($response);
				}
				catch (Swiftriver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}
			break;
			
			case "DELETE":
				$form_id = $this->request->param('id', 0);
				$this->form_service->delete_form($form_id);
			break;
		}
		
	}
	
	/**
	 * XHR endopint for adding/removing followers
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
				
				// Follow/Unfollow
				if ($item_array['following'])
				{
					// Follow
					$this->account_service->add_follower($this->visited_account['id'], $item_array['id']);
				}
				elseif ( ! $item_array['following'])
				{
					// Unfollow
					$this->account_service->remove_follower($this->visited_account['id'], $item_array['id']);
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
		Swiftriver_Mail::send($user_orm->email, 
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
		
		$this->template->content->show_navigation = FALSE;

		// Set the current page
		$this->active = 'settings-navigation-link';
		$this->template->content->view_type = 'settings';
		$this->template->header->js = View::factory('pages/user/js/settings');
		$this->template->header->js->user = $this->user;

		$this->sub_content = View::factory('pages/user/settings')
		    ->bind('user', $this->user)
		    ->bind('errors', $this->errors);
		
		if ($this->request->method() === 'POST' AND CSRF::valid($this->request->post('form_auth_id')))
		{
			if ( ! isset($_POST['current_password']))
			{
				if (($account = $this->account_service->update_account($this->user['id'], $_POST)) != FALSE)
				{
					$this->user = $account;
					$this->visited_account  = $account;
				}
			}
			elseif (isset($_POST['current_password']))
			{
				// The change password form has been submitted
				$this->account_service->change_password($this->user['id'], $_POST);
			}
				
		}
		
		$session = Session::instance();
		$this->sub_content->messages = $session->get('messages');
		$session->delete('messages');
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
				
				$activity_list = array();
											
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
		
		Swiftriver_Mail::send($to, $subject, $text->render(), $html->render());
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
		if ( ! Swiftriver::get_setting('general_invites_enabled') OR ! $this->owner)
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

		// Activity Stream
		$nav[] = array(
			'id' => 'activities-navigation-link',
			'url' => '',
			'label' => __('Activity')
		);
        
		// Content
		$nav[] = array(
			'id' => 'content-navigation-link',
			'url' => '/content',
			'label' => __('Content')
		);

		// Invite
		if (Swiftriver::get_setting('general_invites_enabled') AND
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
