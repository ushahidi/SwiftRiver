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
			->bind('followers', $followers);
		$this->active = 'main';
		$this->template_type = 'dashboard';

		// Get Following (as_array)
		// ++ We can cache this array in future
		$following = ORM::factory('user_follower')
			->select('users.*')
			->where('user_follower.follower_id', '=', $this->user->id)
			->join('users', 'INNER')
				->on('users.id', '=', 'user_follower.follower_id')
			->order_by('user_follower.follower_date_add', 'DESC')
			->find_all()
			->as_array();

		// Get Followers (as_array)
		// ++ We can cache this array in future
		$followers = $this->user->user_followers
			->select('users.*')
			->join('users', 'INNER')
				->on('users.id', '=', 'user_follower.user_id')		
			->order_by('user_follower.follower_date_add', 'DESC')
			->find_all()
			->as_array();

		$actions = 0;
	}

	/**
	 * Dashboard Rivers
	 *
	 * @return	void
	 */
	public function action_rivers()
	{
		$this->sub_content = View::factory('pages/dashboard/rivers')
			->bind('rivers', $rivers);
		$this->active = 'rivers';
		$this->template_type = 'list';
		
		// Get Rivers (as_array)
		// ++ We can cache this array in future
		$rivers = ORM::factory('river')
			->join('accounts', 'INNER')
				->on('river.account_id', '=', 'accounts.id')
			->where('accounts.user_id', '=', $this->user->id)
			->find_all()
			->as_array();
	}

	/**
	 * Dashboard Buckets
	 *
	 * @return	void
	 */
	public function action_buckets()
	{
		$this->sub_content = View::factory('pages/dashboard/buckets')
			->bind('buckets', $buckets);
		$this->active = 'buckets';
		$this->template_type = 'list';
		
		// Get Rivers (as_array)
		// ++ We can cache this array in future
		$buckets = ORM::factory('bucket')
			->join('accounts', 'INNER')
				->on('bucket.account_id', '=', 'accounts.id')
			->where('accounts.user_id', '=', $this->user->id)
			->find_all()
			->as_array();
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
		$settings = View::factory('pages/dashboard/settings');
		$settings->user = $this->user;
		echo $settings;
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