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
	 * Stores the referring URL
	 * @var string
	 */
	private $referrer;
	
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

		$this->template->content = View::factory('pages/login/main')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $this->referrer);
	}
	
	/**
	 * Log User In
	 * 
	 * @return void
	 */	
	public function action_index()
	{
		$this->template->content->active = 'login';
		$this->template->content->public_registration_enabled = Swiftriver::get_setting('public_registration_enabled');
		
		if ($this->user)
		{
			$this->redirect($this->dashboard_url, 302);
		}

		// Get the referriing URL
		$this->referrer = $this->request->query('redirect_to');

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
					if (empty($redirect_to))
					{
						$redirect_to = $this->request->referrer();
					}

					Kohana::$log->add(Log::DEBUG, __("Redirecting to :redirect_to", 
						array(":redirect_to" => $redirect_to)));
					
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
					
					foreach ($validation->errors('login') as $error)
					{
						Swiftriver_Messages::add_message('failure',  __('Failure'), $error, FALSE);
					}
					$this->redirect(URL::site('login', TRUE), 302);
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
				foreach ($validation->errors('login') as $error)
				{
					Swiftriver_Messages::add_message('failure', __('Failure'), $error, FALSE);
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
					
					Swiftriver_Messages::add_message(
						'success', 
						__('Success'), 
						__('An email has been sent with instructions to complete the registration process.'),
						FALSE
					);
					$this->redirect(URL::site('login'), 302);
				}
				catch (SwiftRiver_API_Exception_BadRequest  $e)
				{
					foreach ($e->get_errors() as $error)
					{
						$message = "Error";
						
						if ($error['field'] == 'account_path' AND $error['code'] == 'duplicate')
						{
							$message = __('The username you seected is not available.');
						}
						
						if ($error['field'] == 'email' AND $error['code'] == 'duplicate')
						{
							$message = __('The email address is already registered.');
						}
						
						Swiftriver_Messages::add_message('failure',  __('Failure'),  $message, FALSE);	
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
	public function action_forgot_password()
	{
		$this->template->content = View::factory('pages/login/forgot_password')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		
		if ($this->request->method() == 'POST' AND CSRF::valid($this->request->post('form_auth_id')))
		{
			$validation = Validation::factory($this->request->post())
				->rule('email', 'not_empty')
				->rule('email', 'email');
			
			if ( ! $validation->check())
			{
				foreach ($validation->errors('login') as $error) 
				{
					Swiftriver_Messages::add_message('failure', __('Failure'), $error, FALSE);
				}
				$this->session->set("email", $this->request->post('email'));
				$this->redirect(URL::site('login/forgot_password'), 302);
			}
			else
			{
				try
				{
					$account = $this->account_service->forgot_password($this->request->post('email'));
					
					Swiftriver_Messages::add_message('success', __('Success'), 
						__('An email has been sent with instructions to reset your password.'),
						FALSE
					);
					$this->redirect(URL::site('login/forgot_password'), 302);
				}
				catch (SwiftRiver_API_Exception $e)
				{		
					Swiftriver_Messages::add_message('failure', __('Failure'), 
						__('An unkown error has occurred'),
						FALSE
					);	
					
					$this->session->set("fullname", $this->request->post('fullname'));
					$this->session->set("email", $this->request->post('email'));
					$this->session->set("username", $this->request->post('username'));
					$this->redirect(URL::site('login/forgot_password'), 302);
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
	public function action_reset_password()
	{
		// Check if the email and token params are present
		if ( ! isset($_GET['email']) OR ! isset($_GET['token']))
		{
			$this->redirect('/login');
		}

		$this->template->content = View::factory('pages/login/reset_password')
		    ->bind('messages', $this->messages)
		    ->bind('errors', $this->errors)
		    ->bind('referrer', $referrer);
		
		if ($this->request->method() == 'POST' AND CSRF::valid($this->request->post('form_auth_id')))
		{
			try
			{
				// Marshall the submitted data
				$reset_data = array(
					'email' => $this->request->query('email'),
					'token' => $this->request->query('token'),
					'password' => $this->request->post('password'),
					'password_confirm' => $this->request->post('password_confirm')
				);
			
				// Reset the password
				if ($this->account_service->reset_password($reset_data))
				{
					Swiftriver_Messages::add_message('success', __('Success'),
						__('Password reset successfully.'),
						FALSE
					);
					$this->redirect(URL::site('login'), 302);
				}
				else
				{
					$this->redirect(URL::site($this->request->uri()), 302);
				}

			}
			catch (SwiftRiver_API_Exception_BadRequest  $e)
			{
				foreach ($e->get_errors() as $error)
				{
					$message = "Error";
					
					if ($error['field'] == 'token' AND $error['code'] == 'invalid')
					{
						$message = __('Account not found.');
					}
					
					Swiftriver_Messages::add_message('failure', __('Failure'), $message, FALSE);
				}
				
				$this->redirect(URL::site($this->request->uri()), 302);
			}
			catch (SwiftRiver_API_Exception_NotFound  $e)
			{		
				Swiftriver_Messages::add_message('failure', __('Failure'), 
					__('There is no account registered with that email address.'),
					FALSE
				);	
				
				$this->session->set("fullname", $this->request->post('fullname'));
				$this->session->set("email", $this->request->post('email'));
				$this->session->set("username", $this->request->post('username'));
				$this->redirect(URL::site($this->request->uri()), 302);
			}
		}
	}
	
	/**
	* Activates a newly created account
	* 
	* @return void
	*/	
	public function action_activate()
	{
		$email = $this->request->query('email');
		$token = $this->request->query('token');

		if ( ! isset($email) OR ! isset($token))
		{
			$this->redirect('login', 302);
		}
		
		try 
		{
			$this->account_service->activate_account($email, $token);
		}
		catch (SwiftRiver_API_Exception_BadRequest  $e)
		{
			foreach ($e->get_errors() as $error)
			{
				$message = "Error";
						
				if ($error['field'] == 'token' AND $error['code'] == 'invalid')
				{
					$message = __('Account not found.');
				}
				
				Swiftriver_Messages::add_message('failure', __('Failure'), $message, FALSE);
			}
			$this->redirect(URL::site('login'), 302);
		}
		catch (SwiftRiver_API_Exception_NotFound  $e)
		{
			Swiftriver_Messages::add_message('failure', __('Failure'), __('Account not found.'), FALSE);
			$this->redirect(URL::site('login'), 302);
		}
		
		Swiftriver_Messages::add_message('success', __('Success'),
			__('Account activated. Proceed to log in.'), FALSE
		);
		$this->redirect(URL::site('login'), 302);
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
