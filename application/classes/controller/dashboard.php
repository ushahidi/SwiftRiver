<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Dashboard Controller
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
class Controller_Dashboard extends Controller_Swiftriver {

	private $active;

	private $sub_content;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		$this->template->content = View::factory('pages/dashboard/layout')
			->bind('user', $this->user)
			->bind('active', $this->active)
			->bind('template_type', $this->template_type)
			->bind('sub_content', $this->sub_content);
		
		$this->template->header->js = View::factory('pages/dashboard/js/dashboard');
	}

	/**
	 * Main Dashboard
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->sub_content = View::factory('pages/dashboard/main')
			->bind('actions', $actions)
			->bind('following', $following)
			->bind('followers', $followers)
			->bind('activity_stream', $activity_stream);
		$this->active = 'main';
		$this->template_type = 'dashboard';
		
		$following = $this->user->following->find_all();
		$followers =  $this->user->followers->find_all();;
		
			
		// Activity stream
		$activity_stream = View::factory('template/activities')
		                       ->bind('activities', $activities)
		                       ->bind('fetch_url', $fetch_url);
		$fetch_url = url::site('dashboard/actions');
		$activities = json_encode(Model_User_Action::get_activity_stream($this->user->id));

		$actions = 0;
	}
	
	/**
	 * Initial registration page
	 *
	 * @return	void
	 */
	public function action_register()
	{
		$this->template->content = View::factory('pages/dashboard/register')
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
					Request::current()->redirect('dashboard');
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
	 * Dashboard Rivers
	 *
	 * @return	void
	 */
	public function action_rivers()
	{
		$this->sub_content = View::factory('pages/dashboard/rivers_buckets')
			->bind('has_items', $has_items)
			->bind('new_item_url', $new_item_url)
			->bind('action_object', $action_object)
			->bind('rivers_buckets_js', $rivers_buckets_js);

		$new_item_url = URL::site('river/new');
		$action_object = 'river';

		$this->active = 'rivers';
		$this->template_type = 'list';

		// Load up the javascript
		$rivers_buckets_js = View::factory('pages/dashboard/js/rivers_buckets')
		    ->bind('list_items', $list_items)
		    ->bind('list_item_url_root', $list_item_url_root);
		
		// Enpoint for processing changes to the river
		$list_item_url_root = URL::site('river/save_settings');

		// Get the rivers 
		$rivers = $this->user->get_rivers_array();
		foreach ($rivers as $k => $river)
		{
			$river['item_name'] = $river['river_name'];
			$river["is_owner"] = ($river['user_id'] == $this->user->id);
			$river["item_owner_url"] = URL::site('river').'/user/'.$river['account_path'];
			$river["item_url"] = URL::site('river').'/index/'.$river['id'];

			$rivers[$k] = $river;
		}
		
		$has_items = (bool) count($rivers);
		$list_items = json_encode($rivers);
	}

	/**
	 * Dashboard Buckets
	 *
	 * @return	void
	 */
	public function action_buckets()
	{
		$this->sub_content = View::factory('pages/dashboard/rivers_buckets')
			->bind('has_items', $has_items)
			->bind('new_item_url', $new_item_url)
			->bind('action_object', $action_object)
			->bind('rivers_buckets_js', $rivers_buckets_js);

		$new_item_url = URL::site('bucket/new');
		$action_object = 'bucket';

		$this->active = 'buckets';
		$this->template_type = 'list';

		// Load up the javascript
		$rivers_buckets_js = View::factory('pages/dashboard/js/rivers_buckets')
		    ->bind('list_items', $list_items)
		    ->bind('list_item_url_root', $list_item_url_root);
		
		// Endpoint for processing changes to the bucket
		$list_item_url_root = URL::site('bucket/api');

		// Get the buckets 
		$buckets = $this->user->get_buckets_array();
		foreach ($buckets as $k => $bucket)
		{
			$bucket['item_name'] = $bucket['bucket_name'];
			$bucket["is_owner"] = ($bucket['user_id'] == $this->user->id);
			$bucket["item_owner_url"] = URL::site('bucket').'/user/'.$bucket['account_path'];
			$bucket["item_url"] = URL::site('bucket').'/index/'.$bucket['id'];

			$buckets[$k] = $bucket;
		}
		
		$has_items = (bool) count($buckets);
		$list_items = json_encode($buckets);
	}

	/**
	 * Dashboard Teams
	 *
	 * @return	void
	 */
	public function action_teams()
	{
		$this->sub_content = View::factory('pages/dashboard/teams');
		$this->active = 'teams';
		$this->template_type = 'list';
	}

	/**
	 * Account settings controls
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		$settings = View::factory('pages/dashboard/settings')
		                ->bind('collaborators_control', $collaborators_control);
		$settings->user = $this->user;
		
		// Disable account collaboration for now
		/*$collaborators_control = View::factory('template/collaborators')
		                             ->bind('collaborator_list', $collaborator_list)
		                             ->bind('fetch_url', $fetch_url)
		                             ->bind('logged_in_user_id', $logged_in_user_id);
		$collaborator_list = json_encode($this->account->get_collaborators());
		$fetch_url = url::site('dashboard/collaborators');
		$logged_in_user_id = $this->user->id;*/
		$collaborators_control = NULL;
		
		echo $settings;
	}
	
	/**
	 * Account collaborators restful api
	 * 
	 * @return	void
	 */
	public function action_collaborators()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$query = $this->request->query('q') ? $this->request->query('q') : NULL;
		
		if ($query) {
			echo json_encode(Model_User::get_like($query, array($this->account->user->id, $this->user->id)));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('user', $user_id);
				
				if ( ! $user_orm->loaded()) 
					return;
				
				$collaborator_orm = $this->account->account_collaborators->where('user_id', '=', $user_orm->id)->find();
				if ($collaborator_orm->loaded())
				{
					$collaborator_orm->delete();
					Model_User_Action::delete_invite($this->user->id, 'account', $this->account->id, $user_orm->id);
				}
			break;
			
			case "PUT":
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('user', $user_id);
				
				$collaborator_orm = ORM::factory("account_collaborator")
									->where('account_id', '=', $this->account->id)
									->where('user_id', '=', $user_orm->id)
									->find();
				
				if ( ! $collaborator_orm->loaded())
				{
					$collaborator_orm->account = $this->account;
					$collaborator_orm->user = $user_orm;
					$collaborator_orm->save();
					Model_User_Action::create_action($this->user->id, 'account', $this->account->id, $user_orm->id);
				}				
			break;
		}
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
	 * Ajax Title Editing Inline
	 *
	 * Edit User Name
	 * 
	 * @return	void
	 */
	public function action_ajax_title()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_REQUEST AND
			isset($_REQUEST['edit_id'], $_REQUEST['edit_value']) AND
			! empty($_REQUEST['edit_id']) AND 
			! empty($_REQUEST['edit_value']) )
		{
			// Action can only be performed on one's own account
			if ($_REQUEST['edit_id'] === $this->user->id)
			{
				$this->user->name = $_REQUEST['edit_value'];
				$this->user->save();
			}
		}
	}

	/**
	 * Ajax rendered list of all tabs, for small screens
	 * 
	 * @return	void
	 */
	public function action_view_tabs()
	{
		$this->template->content = View::factory('pages/dashboard/views')
			->bind('user', $this->user);
	}

	/**
	 * Ajax rendered filter for list of Rivers
	 * 
	 * @return	void
	 */
	public function action_filter_rivers()
	{
		$this->sub_content = View::factory('pages/dashboard/filter_rivers');
		$this->active = 'rivers';
	}

	/**
	 * Ajax rendered form for editing multiple rivers in a list
	 * 
	 * @return	void
	 */
	public function action_edit_multiple_rivers()
	{
		$this->template = View::factory('pages/dashboard/edit_multiple_rivers');
	}		
}