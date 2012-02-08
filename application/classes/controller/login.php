<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Login Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Login extends Controller_Template {
	
	/**
	 * @var	 bool auto render
	 */
	public $auto_render = TRUE;
	
	/**
	 * @var	 string	 page template
	 */
	public $template = 'pages/login';
	
	/**
	 * Are we using RiverID?
	 */
	 public $riverid_auth = FALSE;	
	
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
	}
	
	/**
	 * Log User In
	 * 
	 * @return void
	 */	
	public function action_index()
	{	
		// If user already signed-in
		if (Auth::instance()->logged_in() != 0)
		{
			$this->request->redirect('dashboard');
		}
		
		//Check for system messages
		$session = Session::instance();
		$messages = $session->get_once('system_messages');
		if($messages) {
			$this->template->set('messages', $messages);        
		}
		
		
		// New user registration
		if ($this->request->post('new_email'))
		{
			if ( ! Valid::email($this->request->post('new_email')))
			{
				$this->template->set('errors', array(__('Email address provided is invalid')));		        
			}
			else 
			{    	    
				if ($this->riverid_auth)
				{
					$this->__new_user_riverid($this->request->post('new_email'));
				}
				else
				{
					$this->__new_user_orm($this->request->post('new_email'));
				}
			}
		}
		
		
		// Password reset request
		if ($this->request->post('recover_email'))
		{
			$email = $this->request->post('recover_email');
			
			if ( ! Valid::email($email))
			{
				$this->template->set('errors', array(__('Recovery email address provided is invalid')));		        
			}
			else 
			{

				// Is the email registed in this site?
				$user = ORM::factory('user',array('email'=>$email));

				if ( ! $user->loaded())
				{
					$this->template->set('errors', array(__('Email not registered')));		        
				} 
				else
				{
					// Do the password reset depending on the auth driver we are using.
					if ($this->riverid_auth) 
					{
						$this->__password_reset_riverid($email, $user);
					}
					else
					{
						$this->__password_reset_orm($email, $user);
					}
				}
			}

		}

		// check, has the form been submitted, if so, setup validation
		if ($this->request->post('username') AND $this->request->post('password'))
		{
			$username = $this->request->post('username');
			$password = $this->request->post('password');

			// Check Auth if the post data validates using the rules setup in the user model
			if (Auth::instance()->login($username, $password))
			{
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('dashboard');
			}
			else
			{
				$this->template->set('username', $username);
				// Get errors for display in view
				$validation = Validation::factory($this->request->post())
					->rule('username', 'not_empty')
					->rule('password', 'not_empty');
				if ($validation->check())
				{
					$validation->error('password', 'invalid');
				}
				$this->template->set('errors', $validation->errors('login'));
			}
		}
	}
	
	/**
	* Send a river id registration request
	*
	*/
	private function __new_user_riverid($email) 
	{	        
		$riverid_api = RiverID_API::instance();
		
		if ( $riverid_api->is_registered($email)) 
		{
			$this->template->set('errors', array(__('Email already registered. Proceed to log in')));
		}
		else
		{
			$mail_body = View::factory('emails/createuser')
						 ->bind('secret_url', $secret_url);
			$secret_url = url::site('login/create/'.urlencode($email).'/%token%', TRUE, TRUE);
			$response = $riverid_api->request_password($email, $mail_body);
			
			if ($response['status']) 
			{
				$this->template->set('messages', array(__('e-mail sent')));
			} 
			else 
			{
				$this->template->set('error', array($response['error']));
			}

		}        
	}

	/**
	* New user registration for ORM auth
	*
	*/
	private function __new_user_orm($email)
	{
		// Is the email registed in this site?
		$user = ORM::factory('user',array('email'=>$email));

		if ($user->loaded())
		{
			$this->template->set('errors', array(__('Email already registered')));
			return;
		}

		$auth_token = Model_Auth_Token::create_token($email, 'new_registration');        
		if ($auth_token->loaded())
		{
			//Send an email with a secret token URL
			$mail_body = View::factory('emails/createuser')
						 ->bind('secret_url', $secret_url);		            
			$secret_url = url::site('login/create/'.urlencode($email).'/'.$auth_token->token, TRUE, TRUE);
			Swiftriver_Mail::send($email, __('Please confirm your email address'), $mail_body);
			
			
			$this->template->set('messages', array(__('e-mail sent')));
		}
		else
		{
			$this->template->set('messages', array(__('error')));
		}
	}	

	/**
	* Send a river id password reset request
	*
	*/	
	private function __password_reset_riverid($email, $user)
	{
		$riverid_api = RiverID_API::instance();		            
		$mail_body = View::factory('emails/resetpassword')
					 ->bind('secret_url', $secret_url);		            
		$secret_url = url::site('login/reset/'.$user->id.'/%token%', TRUE, TRUE);
		$response = $riverid_api->request_password($email, $mail_body);
		
		if ($response['status']) 
		{
			$this->template->set('messages', array(__('e-mail sent')));
		} 
		else 
		{
			$this->template->set('error', array($response['error']));
		}
	}

	/**
	* Password reset for ORM auth.
	*
	*/	
	private function __password_reset_orm($email, $user)
	{
		$auth_token = Model_Auth_Token::create_token($email, 'password_reset');        
		if ($auth_token->loaded())
		{
			//Send an email with a secret token URL
			$mail_body = View::factory('emails/resetpassword')
						 ->bind('secret_url', $secret_url);		            
			$secret_url = url::site('login/reset/'.$user->id.'/'.$auth_token->token, TRUE, TRUE);
			Swiftriver_Mail::send($email, __('Password Reset'), $mail_body);
			
			
			$this->template->set('messages', array(__('e-mail sent')));
		}
		else
		{
			$this->template->set('messages', array(__('error')));
		}
	}

	/**
	 * Reset password
	 * 
	 * @return void
	 */	
	public function action_reset()
	{
		$this->auto_render = FALSE;	    
		$template = View::factory('pages/reset')
						  ->bind('errors', $errors);

		$user_id = intval($this->request->param('id', 0));
		$email = $this->request->param('email');
		$token = $this->request->param('token');

		$user = ORM::factory('user', $user_id);
		if ($user->loaded())
		{
			// If we have userid only, get email from the user object
			$email = $user->email;
		}	        
		
		
		// If the form has been filled in and submitted
		if ($email AND $this->request->post('password_confirm') AND $this->request->post('password'))
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
					else
					{
						$session = Session::instance();
						$session->set('system_messages', array(__('Password reset was successful. Proceed to Log in')));
						$this->request->redirect('login');
						return;
					}
				}
				else
				{
					// Do an ORM password reset
					if (Model_Auth_Token::is_valid_token($email, $token, 'password_reset') OR
						Model_Auth_Token::is_valid_token($email, $token, 'new_registration'))
					{
						if ( ! $user->loaded() ) {
							// New user registration
							$user->name = $user->username = $user->email = $email;
							$user->save();
							
							// Allow the user be able to login immediately
							$login_role = ORM::factory('role',array('name'=>'login'));
							$user->add('roles', $login_role);
						}                   
						$user->password = $this->request->post('password');
						$user->save();
						Session::instance()->set('system_messages', array(__('Password reset was successful. Proceed to Log in')));
						$this->request->redirect('login');
						return;	                    
					}
					else
					{
						$errors = array(__('Error'));
					}
				}
			}
		}

		echo $template;
	}	
	
	
	/**
	 * Change email address
	 * 
	 * @return void
	 */		
	public function action_changeemail()
	{
		$this->auto_render = FALSE;	    
		$template = View::factory('pages/changeemail')
						  ->bind('errors', $errors);

		$user_id = intval($this->request->param('id', 0));
		$user = ORM::factory('user', $user_id);
		$new_email = $this->request->param('email');
		$token = $this->request->param('token');

		if ($user->loaded())
		{
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
				if ( ! Model_Auth_Token::is_valid_token($new_email, $token, 'change_email'))
				{
					$errors = array(__('Error'));
				}
			}

			if(empty($errors))
			{
				// Email change was validated, make the change to the user object
				$user->email = $user->username = $new_email;
				$user->save();

				// Force a logout
				$session = Session::instance();
				$session->set('system_messages', array(__('Email changed successfully. Proceed to Log in')));
				Auth::instance()->logout();
				$this->request->redirect('login');

				return;
			}
		}           

		echo $template;   
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
