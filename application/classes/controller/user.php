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
		if ( ! $this->owner AND $this->visited_account->account_private) {
			$this->request->redirect($this->dashboard_url);
		}

		$this->template->content = View::factory('pages/user/layout')
			->bind('account', $this->visited_account)
			->bind('owner', $this->owner)
			->bind('active', $this->active)
			->bind('sub_content', $this->sub_content)
			->bind('anonymous', $this->anonymous)
			->bind('followers', $followers)
			->bind('following', $following);
			
		$following = $this->visited_account->user->following->find_all();
		$followers =  $this->visited_account->user->followers->find_all();

		// Some info about the owner of the user profile being visited
		// Will be used later for following unfollowing
		// $this->template->content->fetch_url = URL::site().$this->visited_account->account_path.'/user/user/manage';

		$this->template->content->user_item = json_encode(array(
				"id" => $this->visited_account->user->id,
				"type" => "user",					
				"item_name" => $this->visited_account->user->name,
				"item_url" => URL::site().$this->visited_account->account_path,
				"subscribed" => $this->user->has('following', $this->visited_account->user),
				"is_owner" => $this->user->id == $this->visited_account->user->id				
			));
			
			
		$this->template->header->js = View::factory('pages/user/js/settings')
			->bind('account', $this->visited_account);
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
				->bind('profile_js', $profile_js);

			$profile_js = $this->_get_profile_js(TRUE);
		}
		else
		{
			$this->_prepare_user_profile_view($activity_stream);
		}
		
		// Activity stream
		$activity_stream = View::factory('template/activities')
		    ->bind('activities', $activities)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner);

		$fetch_url = URL::site().$this->visited_account->account_path.'/user/action/actions';

		$activities = json_encode(Model_User_Action::get_activity_stream(
			$this->visited_account->user->id, ! $this->owner));

	}

	/**
	 * Displays the current user's profile
	 */
	public function action_profile()
	{
		$this->active = '';

		$this->_prepare_user_profile_view($activity_stream);

		// Activity stream
		$activity_stream = View::factory('template/activities')
		    ->bind('activities', $activities)
		    ->bind('fetch_url', $fetch_url)
		    ->bind('owner', $this->owner);

		$fetch_url = URL::site().$this->visited_account->account_path.'/user/action/actions';

		$activities = json_encode(Model_User_Action::get_activity_stream(
			$this->visited_account->user->id, ! $this->owner));
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
		    ->bind('profile_js', $profile_js);

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
		$visited_user_id = $this->visited_account->user->id;

		$river_fetch_url = $base_path.'river/manage';
		$bucket_fetch_url = $base_path.'bucket/manage';

		if ($this->owner)
		{
			// TODO - Check the cache
			$rivers_list = json_encode($this->user->get_rivers());
			$buckets_list = json_encode($this->user->get_buckets());
		}
		else
		{
			$rivers_list = json_encode($this->user->get_other_user_visible_rivers($visited_user_id));
			$buckets_list = json_encode($this->user->get_other_user_visible_buckets($visited_user_id));
		}

		return $profile_js;
	}

	
	/**
	 * @return	void
	 */
	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		if ( $this->anonymous )
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
					if ($item_array['subscribed'] == 1 AND 
						! $this->user->has('following', $user_orm))
					{
						$this->user->add('following', $user_orm);
					}
					
					// Are unfollowing
					if ($item_array['subscribed'] == 0 AND 
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
	 * Account settings controls
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		// Set the current page
		$this->active = 'settings';

		$this->template->sub_content = View::factory('pages/user/settings')
		    ->bind('user', $this->user);
		
	}
	
	/**
	 * Ajax Settings Editing Inline
	 *
	 * Edit User Settings
	 * 
	 * @return	string - json
	 */
	public function action_ajax_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// Save settings
		if ( ! empty($_POST))
		{
			try 
			{
				if (empty($_POST['password']) || empty($_POST['password_confirm']))
				{
					// force unsetting the password! Otherwise Kohana3 will automatically hash the empty string - preventing logins
					unset($_POST['password'], $_POST['password_confirm']);
				}
				
				$current_password = $_POST['current_password'];
				
				// Authenticate changes for non river id auth by checking if old password matches
				if ( ! $this->riverid_auth AND
					Auth::instance()->hash($current_password) != $this->user->password)
				{
					echo json_encode(array("status"=>"error", "errors" => array('Current password is incorrect')));
					return;
				}
				
				
				// Password is changing and we are using RiverID authentication
				if ( ! empty($_POST['password']) and ! empty($_POST['password_confirm']))
				{
					$post = Model_Auth_User::get_password_validation($_POST);
					if ( ! $post->check())
					{
						throw new ORM_Validation_Exception('', $post);
					}
					
					if ($this->riverid_auth)
					{
						$resp = RiverID_API::instance()
								   ->change_password($this->user->email, $_POST['current_password'], $_POST['password']);

						if ( ! $resp['status'])
						{
							echo json_encode(array("status"=>"error", "errors" => array($resp['error'])));
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
						echo json_encode(array("status"=>"error", "errors" => array(__('Email provided is invalid'))));
						return;
					}
					
					if ($this->riverid_auth)
					{
						// RiverID email change process
						$mail_body = View::factory('emails/changeemail')
									 ->bind('secret_url', $secret_url);		            
						
						$secret_url = url::site('login/changeemail/'.$this->user->id.'/'.urlencode($new_email).'/%token%', TRUE, TRUE);
						
						$resp = RiverID_API::instance()
							->change_email($this->user->email, $new_email, $current_password, $mail_body);
						
						if ( ! $resp['status'])
						{
							echo json_encode(array("status"=>"error", "errors" => array($resp['error'])));
							return;
						}    
					}
					else
					{
						// Make sure the new email address is not yet registered
						$user = ORM::factory('user',array('email'=>$new_email));
						if ($user->loaded())
						{
							echo json_encode(array("status"=>"error", "errors" => array(__('New email is already registered'))));
							return;
						}
						
						$auth_token = Model_Auth_Token::create_token($new_email, 'change_email');
						if ($auth_token->loaded())
						{
							// Send an email with a secret token URL
							$mail_body = View::factory('emails/changeemail')
											   ->bind('secret_url', $secret_url);		            
							
							$secret_url = url::site('login/changeemail/'
													.$this->user->id
													.'/'
													.urlencode($new_email)
													.'/'
													.$auth_token->token, TRUE, TRUE);
							
							// Send email to the user using the new address
							Swiftriver_Mail::send($new_email, __('Email Change'), $mail_body);
						}
						else
						{
							echo json_encode(array("status"=>"error", "errors" => array(__('Error'))));
							return;
						}
					}
					
					// Don't change email address immediately.
					// Only do so after the tokens sent above are validated
					unset($_POST['email']);

				} // end if - email address change
				
				// Nickname is changing
				if ($_POST['nickname'] != $this->user->account->account_path)
				{
					$nickname = $_POST['nickname'];
					// Make sure the account path is not already taken
					$account = ORM::factory('account',array('account_path'=>$nickname));
					if ($account->loaded())
					{
						echo json_encode(array("status"=>"error", "errors" => array(__('Nickname is already taken'))));
						return;
					}
					
					// Update
					$this->user->account->account_path = $nickname;
					$this->user->account->save();
				}

				$this->user->update_user($_POST, array('name', 'password', 'email'));

				echo json_encode(array("status"=>"success"));
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get the validation errors
				$errors = $e->errors('user');
				echo json_encode(array("status"=>"error", "errors" => $errors));
			}
		}
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
			case "PUT":
				$action_id = intval($this->request->param('id', 0));
				$action_orm = ORM::factory('user_action', $action_id);
				$action_array = json_decode($this->request->body(), TRUE);
				
				// Are we confirming?
				if ($action_array['confirmed'])
				{
					$action_orm->confirmed = $action_array['confirmed'];
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
					if ($collaborator_orm and $collaborator_orm->loaded())
					{
						$collaborator_orm->collaborator_active = $action_array['confirmed'];
						$collaborator_orm->save();
						$action_orm->save();
					}						
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
				}
				else
				{
					// The data check out, create the account and proceed to Swift!
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

}