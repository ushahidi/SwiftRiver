<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Login Controller
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
class Controller_Login extends Controller_Swiftriver {
	
	/**
	 * The before() method is called before main controller action.
	 * In our template controller we override this method so that we can
	 * set up default values. These variables are then available to our
	 * controllers if they need to be modified.
	 *
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		if (strtolower(Kohana::$config->load('auth.driver')) == 'riverid') 
		{
			$this->riverid_auth = TRUE;
		}

		$this->template->content = View::factory('pages/login/layout');
		$this->template->content->public_registration_enabled = Model_Setting::get_setting('public_registration_enabled');
	}
	
	/**
	 * Log User In
	 * 
	 * @return void
	 */	
	public function action_index()
	{
		$this->template->content->active = 'login';
		$this->template->content->sub_content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);

		if ($this->user)
		{
			$this->request->redirect($this->dashboard_url);
		}

		// Get the referriing URL
		$referrer = $this->request->query('redirect_to') 
		    ? $this->request->query('redirect_to') 
		    : NULL;

		//Check for system messages
		$session = Session::instance();
		$messages = $session->get_once('system_messages');
		if ($messages)
		{
			$this->messages = $messages;
		}
		
		$errors = $session->get_once('system_errors');
		if ($errors)
		{
			$this->errors = $errors;
		}
				
		// Password reset request
		if ($this->request->post('recover_email'))
		{
			$email = $this->request->post('recover_email');
			$csrf_token = $this->request->post('form_auth_id');
			
			if ( ! Valid::email($email) OR ! CSRF::valid($csrf_token))
			{
				$this->errors = array(__('The email address you have provided is invalid'));
			}
			else 
			{

				// Is the email registed in this site?
				$user = ORM::factory('user',array('email'=>$email));

				if ( ! $user->loaded())
				{
					$this->errors = array(__('The provided email address is not registered'));
				} 
				else
				{
					$messages = Model_User::password_reset($email, $this->riverid_auth);
					
					// Display the messages
					if (isset($messages['errors']))
					{
						$this->errors = $messages['errors'];
					}
					if (isset($messages['messages']))
					{
						$this->messages = $messages['messages'];
					}					
				}
			}

		}

		// Check, has the form been submitted, if so, setup validation
		if ($this->request->post('username') AND $this->request->post('password'))
		{
			// Validate the form token
			if (CSRF::valid($this->request->post('form_auth_id')))
			{
				$username = $this->request->post('username');
				$password = $this->request->post('password');
				
				// Check Auth if the post data validates using the rules setup in the user model
				if (Auth::instance()->login($username, $password, 
					$this->request->post('remember') == 1))
				{
					// Always redirect after a successful POST to prevent refresh warnings
					// First check if a referrer was provided in the post parameters
					// and if not provided, use the referrer from the request otherwise
					// just redirect to the user profile if the above are not found or do
					// not point to a url in this site
					$redirect_to = $this->request->post('referrer');
					$redirect_to = $redirect_to ? $redirect_to : $this->request->referrer();
					
					if
					( 
						! $redirect_to 
						OR strpos($redirect_to, URL::base($this->request)) === FALSE
						OR strpos($redirect_to, URL::base($this->request)) != 0
					)
					{
						$user = Auth::instance()->get_user();
						$redirect_to = URL::site().$user->account->account_path;
					}
					$this->request->redirect($redirect_to);
				}
				else
				{
					$this->template->content->set('username', $username);

					// Get errors for display in view
					$validation = Validation::factory($this->request->post())
						->rule('username', 'not_empty')
						->rule('password', 'not_empty');
					if ($validation->check())
					{
						$validation->error('password', 'invalid');
					}
					$this->errors = $validation->errors('login');
				}
			}
			else
			{
				// Show invalid request message
				Kohana::$log->add(Log::ERROR, "Invalid CSRF token :token", 
					array(':token' => $this->request->post('form_auth_id')));
			}
		}
	}
	
	public function action_register()
	{
		$this->template->content->active = 'create';
		$this->template->content->sub_content = View::factory('pages/login/register')
		                          ->bind('messages', $this->messages)
		                          ->bind('errors', $this->errors);
		
		// New user registration
		if ($this->request->post('new_email'))
		{
			$messages = Model_User::new_user($this->request->post('new_email'), $this->riverid_auth);
			
			// Display the messages
			if (isset($messages['errors']))
			{
				$this->errors = $messages['errors'];
			}
			if (isset($messages['messages']))
			{
				$this->messages = $messages['messages'];
			}					
		}
	}
	
	
	public function action_register_ajax()
	{
		$this->auto_render = FALSE;

		if ($this->request->post('new_email'))
		{
			$messages = Model_User::new_user($this->request->post('new_email'), $this->riverid_auth, (bool) $this->request->post('invite'));
			$ret = array();
        
			if (isset($messages['errors']))
			{
				$ret['status'] = 'ERROR';
				$ret['errors'] = $messages['errors'];
			}
			if (isset($messages['messages']))
			{
				$ret['status'] = 'OK';
				$ret['messages'] = $messages['messages'];
			}
        
			echo json_encode($ret);
		}
	}
		
	
	/**
	* Reset password
	* 
	* @return void
	*/	
	public function action_reset()
	{
		$this->template->content = View::factory('pages/login/reset')
		                                 ->bind('errors', $errors);
        
		$email = $this->request->param('email');
		$token = $this->request->param('token');
		
		$user = ORM::factory('user', array('email' => $email));
		
		// If the form has been filled in and submitted
		if ($user->loaded() AND $this->request->post('password_confirm') AND $this->request->post('password'))
		{
			// Validate the passwords
			$post = Model_Auth_User::get_password_validation($this->request->post());
			if ( ! $post->check())
			{
				$errors = $post->errors('user');
			}
			else
			{
				// Do a RiverID password reset
				if ($this->riverid_auth)
				{
					$riverid_api = RiverID_API::instance();
					$resp = $riverid_api->set_password($email, $token, $this->request->post('password'));
        
					if ( ! $resp['status']) 
					{
						$errors = array($resp['error']);
					}
				}
				else
				{
					// Do an ORM password reset
					$token = Model_Auth_Token::get_token($token, 'password_reset');
					if ($token)
					{
						$data = json_decode($token->data);
						$token->delete();
						if ($email != $data->email)
						{
							// The email in the request does not match
							// the email in the token
							$errors = array(__('Invalid email'));
						}
						else
						{
							$user->password = $this->request->post('password');
							$user->save();
						}                    
					}
					else
					{
						$errors = array(__('Error'));
					}
				}
			}
			
			if ( ! $errors)
			{
				// Auto login
				Auth::instance()->login($user->username, $this->request->post('password'), FALSE);
				
				// Show a message and redirect to swift
				$this->template->content = View::factory('pages/login/landing');
				$this->template->content->messages = array(__('Password reset was successful.'));
				$this->template->header->meta = '<meta HTTP-EQUIV="REFRESH" content="5; url='.URL::site().'">';
			}
		}
	}
	
	/**
	* Create an account
	* 
	* @return void
	*/	
	public function action_create()
	{
		$this->template->content = View::factory('pages/login/create')
		    ->bind('form_name', $form_name)
		    ->bind('form_nickname', $form_nickname)
		    ->bind('errors', $errors);
		
		$email = $this->request->param('email');
		$token = $this->request->param('token');
		
		$user = ORM::factory('user', array('email' => $email));
		
		if ($user->loaded())
		{
			$this->template->content = View::factory('pages/login/landing');
			$this->template->content->errors = array(__('Email is already registered'));
			$this->template->header->meta = '<meta HTTP-EQUIV="REFRESH" content="5; url='.URL::site().'">';
			return;
		}
		else
		{
			// To retun user entered values in case of errors
			$form_name = $this->request->post('name');
			$form_nickname = $this->request->post('nickname');
		}
		
		if ($this->request->post() AND ! $user->loaded())
		{
			$post = Model_Auth_User::get_password_validation($this->request->post())
									->rule('name', 'not_empty')
									->rule('nickname', 'not_empty')
									->rule('nickname', 'alpha_dash');
									
			if ( ! $post->check())
			{
				$errors = $post->errors('user');
			}
			else
			{
				// RiverID validation
				if ($this->riverid_auth)
				{
					$riverid_api = RiverID_API::instance();
					$resp = $riverid_api->set_password($email, $token, $this->request->post('password'));
        
					if ( ! $resp['status']) 
					{
						$errors = array($resp['error']);
					}
					
				}
				else
				{
					// ORM auth validation
					$token = Model_Auth_Token::get_token($token, 'new_registration');
					if (! $token)
					{
						$errors = array(__('Error'));
					}
					else
					{
						$data = json_decode($token->data);
						$token->delete();
						
						if ($email != $data->email) {
							// The email in the request does not match
							// the email in the token
							$errors = array(__('Invalid email'));
						}
					}
				}
				
				// Is the nickname taken?
				$nickname = strtolower($this->request->post('nickname'));
				$account = ORM::factory('account',array('account_path' => $nickname));
				if ($account->loaded())
				{
					$errors = array(__('Nickname is already taken'));
				}
			}
			
			if ( ! $errors )
			{
				// User entry
				$user = ORM::factory('user');
				$user->username = $user->email = $email;
				$user->name = $this->request->post('name');
				
				
				if ( ! $this->riverid_auth) {
					// Password only needed locally for ORM auth
					$user->password = $this->request->post('password');
				}
				
				$user->save();
				
				// Account entry
				$nickname = strtolower($this->request->post('nickname'));
				$user->account->account_path = $nickname;
				$user->account->user_id = $user->id;
				$user->account->save();
				
				
				// Allow the user be able to login immediately
				$login_role = ORM::factory('role',array('name'=>'login'));
				$user->add('roles', $login_role);
				$user->save();
				
				// Auto login
				Auth::instance()->login($user->username, $this->request->post('password'), FALSE);
				
				// Show a message and redirect to swift
				$this->template->content = View::factory('pages/login/landing');
				$this->template->content->messages = array(__('Account was created successfuly.'));
				$this->template->header->meta = '<meta HTTP-EQUIV="REFRESH" content="5; url='.URL::site().'">';
			}
		}
	}

	
	/**
	 * Change email address
	 * 
	 * @return void
	 */		
	public function action_changeemail()
	{
		$this->template->content = View::factory('pages/login/landing');
		$this->template->header->meta = '<meta HTTP-EQUIV="REFRESH" content="5; url='.URL::site().'">';
		
		// Force logout
		Auth::instance()->logout();
		
		$session = Session::instance();
		
		$old_email = $this->request->param('old_email');
		$new_email = $this->request->param('new_email');
		$token = $this->request->param('token');
		
		$user = ORM::factory('user', array('email' => $old_email));

		if ($this->riverid_auth)
		{
			$riverid_api = RiverID_API::instance();
			$resp = $riverid_api->confirm_email($new_email, $token);	        
        
			if ( ! $resp['status']) 
			{
				$errors = array($resp['error']);
			}            
		}
		else
		{
			$token = Model_Auth_Token::get_token($token, 'change_email');
			if ( $token)
			{
				$data = json_decode($token->data);
				$token->delete();
				
				if ($new_email != $data->new_email OR $old_email != $data->old_email) {
					// The emails in the request does not match
					// the emails in the token
					$errors = array(__('Invalid email'));
				}
			}
			else
			{
				$errors = array(__('Error'));
			}
		}
        
		if (empty($errors))
		{
			// Email change was validated, make the change to the user object
			$user->email = $user->username = $new_email;
			$user->save();
			
			// Auto login
			Auth::instance()->force_login($user);
        
			$this->template->content->messages = array(__('Email changed successfully.'));
		}
		else
		{
			$this->template->content->errors = $errors;
		}		
	}

	/**
	 * Log User Out
	 * 
	 * @return void
	 */	
	public function action_done()
	{
		// Sign out the user
		Auth::instance()->logout();
		
		Request::current()->redirect('login');
	}
}
