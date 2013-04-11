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

	}
	
	/**
	 * Log User In
	 * 
	 * @return void
	 */	
	public function action_index()
	{
		$this->template->content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		$this->template->content->active = 'login';
		$this->template->content->public_registration_enabled = Swiftriver::get_setting('public_registration_enabled');
		
		if ($this->user)
		{
			$this->redirect($this->dashboard_url, 302);
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
				$user = ORM::factory('User',array('email'=>$email));

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
					
					$redirect_to_request = Request::factory(parse_url($redirect_to, PHP_URL_PATH));
					if (strtolower($redirect_to_request->uri()) == 'welcome')
					{
						// Just logged in from the welcome page, go to the dashboard.
						$user = Auth::instance()->get_user();
						$redirect_to = URL::site($user->account->account_path);
					}
					
					$this->redirect($redirect_to, 302);
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
					
					foreach ($validation->errors('login') as $error) {
						Swiftriver_Messages::add_message(
							'failure', 
							__('Failure'), 
							$error,
							false
						);
					}
					$this->redirect(URL::site('login'), 302);
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
		$this->template->content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		$this->template->content->active = 'register';		
		
		// New user registration
		if ($this->request->method() == 'POST')
		{
			$validation = Validation::factory($this->request->post())
				->rule('fullname', 'not_empty')
				->rule('email', 'not_empty')
				->rule('email', 'email')
				->rule('password', 'not_empty')
				->rule('password', 'min_length', array(':value', '6'))
				->rule('password_confirm',  'matches', array(':validation', ':field', 'password'))
				->rule('username', 'not_empty')
				->rule('username', 'alpha_dash');
			
			if ( ! $validation->check())
			{
				foreach ($validation->errors('login') as $error) {
					Swiftriver_Messages::add_message(
						'failure', 
						__('Failure'), 
						$error,
						false
					);
				}
				$this->session->set("fullname", $this->request->post('fullname'));
				$this->session->set("email", $this->request->post('email'));
				$this->session->set("username", $this->request->post('username'));
				$this->redirect(URL::site('login/register'), 302);
				
			}
			else
			{
				try
				{
					$account = $this->account_service->create_account(
						$this->request->post('fullname'),
						$this->request->post('email'),
						$this->request->post('username'),
						$this->request->post('password')
					);
					
					
					// Send an email verification email.
					$email = $this->request->post('email');
					$token = $account['token'];
					$mail_body = View::factory('emails/text/createuser')
								 ->bind('secret_url', $secret_url);
					$mail_subject = __(':sitename: Please confirm your email address', 
						array(':sitename' => Swiftriver::get_setting('site_name')));
						
					$secret_url = URL::site('login/create/'.urlencode($email).'/'.$token, TRUE, TRUE);
					Swiftriver_Mail::send($email, $mail_subject, $mail_body); 
					
					Swiftriver_Messages::add_message(
						'success', 
						__('Success'), 
						__('An email has been sent with instructions to complete the registration process.'),
						false
					);
					$this->redirect(URL::site('login'), 302);
				}
				catch (SwiftRiver_API_Exception_BadRequest  $e)
				{
					foreach ($e->get_errors() as $error)
					{
						$message = "Error";
						
						if ($error['field'] == 'account_path' && $error['code'] == 'duplicate')
						{
							$message = __('The username you seected is not available.');
						}
						
						if ($error['field'] == 'email' && $error['code'] == 'duplicate')
						{
							$message = __('The email address is already registered.');
						}
						
						Swiftriver_Messages::add_message(
							'failure', 
							__('Failure'), 
							$message,
							false
						);	
					}
					
					$this->session->set("fullname", $this->request->post('fullname'));
					$this->session->set("email", $this->request->post('email'));
					$this->session->set("username", $this->request->post('username'));
					$this->redirect(URL::site('login/register'), 302);
				}
			}
							
		}
		
		$this->template->content->fullname = $this->session->get_once("fullname");
		$this->template->content->email = $this->session->get_once("email");
		$this->template->content->username = $this->session->get_once("username");
	}
	
	
	/**
	* Request a reset password url on mail
	* 
	* @return void
	*/	
	public function action_request_reset()
	{
		$this->template->content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		$this->template->content->active = 'request_reset';	
		
		if ($this->request->method() == 'POST')
		{
			$validation = Validation::factory($this->request->post())
				->rule('email', 'not_empty')
				->rule('email', 'email');
			
			if ( ! $validation->check())
			{
				foreach ($validation->errors('login') as $error) 
				{
					Swiftriver_Messages::add_message(
						'failure', 
						__('Failure'), 
						$error,
						false
					);
				}
				$this->session->set("email", $this->request->post('email'));
				$this->redirect(URL::site('login/request_reset'), 302);
				
			}
			else
			{
				try
				{
					$account = $this->account_service->get_token(
						$this->request->post('email')
					);
					
					
					// Send an email verification email.
					$email = $this->request->post('email');
					$token = $account['token'];

					//Send an email with a secret token URL
					$mail_body = View::factory('emails/text/resetpassword')
								 ->bind('secret_url', $secret_url);		            
					$secret_url = URL::site('login/reset/'.urlencode($email).'/'.$token, TRUE, TRUE);
					$mail_subject = __(':sitename: Password Reset', array(':sitename' => Swiftriver::get_setting('site_name')));
					Swiftriver_Mail::send($email, $mail_subject, $mail_body);
					
					
					Swiftriver_Messages::add_message(
						'success', 
						__('Success'), 
						__('An email has been sent with instructions to reset your password.'),
						false
					);
					$this->redirect(URL::site('login/request_reset'), 302);
				}
				catch (SwiftRiver_API_Exception_NotFound  $e)
				{		
					Swiftriver_Messages::add_message(
						'failure', 
						__('Failure'), 
						__('There is no account registered with that email address.'),
						false
					);	
					
					$this->session->set("fullname", $this->request->post('fullname'));
					$this->session->set("email", $this->request->post('email'));
					$this->session->set("username", $this->request->post('username'));
					$this->redirect(URL::site('login/request_reset'), 302);
				}
			}
							
		}
		
		$this->template->content->email = $this->session->get_once("email");
	}
	
	/**
	* Reset account password
	* 
	* @return void
	*/
	public function action_reset()
	{		
		$this->template->content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		$this->template->content->active = 'reset';
		
		if ($this->request->method() == 'POST')
		{
			$validation = Validation::factory($this->request->post())
				->rule('password', 'not_empty')
				->rule('password', 'min_length', array(':value', '6'))
				->rule('password_confirm',  'matches', array(':validation', ':field', 'password'));
				
			
			if ( ! $validation->check())
			{
				foreach ($validation->errors('login') as $error) 
				{
					Swiftriver_Messages::add_message(
						'failure', 
						__('Failure'), 
						$error,
						false
					);
				}
				$this->redirect(URL::site($this->request->uri()), 302);
				
			}
			else
			{
				try
				{
					$email = $this->request->param('email');
					$token = $this->request->param('token');
					$password = $this->request->post('password');
					$this->account_service->reset_password(
						$email,
						$token,
						$password
					);
										
					Swiftriver_Messages::add_message(
						'success', 
						__('Success'), 
						__('Password reset successfully.'),
						false
					);
					$this->redirect(URL::site('login'), 302);
				}
				catch (SwiftRiver_API_Exception_BadRequest  $e)
				{
					foreach ($e->get_errors() as $error)
					{
						$message = "Error";
						
						if ($error['field'] == 'token' && $error['code'] == 'invalid')
						{
							$message = __('Account not found.');
						}
						
						Swiftriver_Messages::add_message(
							'failure', 
							__('Failure'), 
							$message,
							false
						);	
					}
					
					$this->redirect(URL::site($this->request->uri()), 302);
				}
				catch (SwiftRiver_API_Exception_NotFound  $e)
				{		
					Swiftriver_Messages::add_message(
						'failure', 
						__('Failure'), 
						__('There is no account registered with that email address.'),
						false
					);	
					
					$this->session->set("fullname", $this->request->post('fullname'));
					$this->session->set("email", $this->request->post('email'));
					$this->session->set("username", $this->request->post('username'));
					$this->redirect(URL::site($this->request->uri()), 302);
				}
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
		$email = $this->request->param('email');
		$token = $this->request->param('token');
		
		try 
		{
			$this->account_service->activate_account($email, $token);
		}
		catch (SwiftRiver_API_Exception_BadRequest  $e)
		{
			foreach ($e->get_errors() as $error)
			{
				$message = "Error";
						
				if ($error['field'] == 'token' && $error['code'] == 'invalid')
				{
					$message = __('Account not found.');
				}
						
				Swiftriver_Messages::add_message(
					'failure', 
					__('Failure'), 
					$message,
					false
				);	
			}
			$this->redirect(URL::site('login'), 302);
		}
		catch (SwiftRiver_API_Exception_NotFound  $e)
		{
			Swiftriver_Messages::add_message(
				'failure', 
				__('Failure'), 
				__('Account not found.'),
				false
			);	
			$this->redirect(URL::site('login'), 302);
		}
		
		Swiftriver_Messages::add_message(
			'success', 
			__('Success'), 
			__('Account activated. Proceed to log in.'),
			false
		);	
		$this->redirect(URL::site('login'), 302);
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
		
		$user = ORM::factory('User', array('email' => $old_email));

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
		
		$this->redirect('login', 302);
	}
}
