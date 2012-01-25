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
        if ($_REQUEST and
	 		isset($_REQUEST['new_email']) and $this->riverid_auth)
		{
		    if ( ! Valid::email($_REQUEST['new_email']))
		    {
		        $this->template->set('errors', array(__('Email address provided is invalid')));		        
		    }
		    else 
		    {
		        $email = $_REQUEST['new_email'];
		        
		        $riverid_api = RiverID_API::factory();
		        
		        if ( $riverid_api->is_registered($email)) 
		        {
		            $this->template->set('errors', array(__('Email already registered. Proceed to log in')));
		        }
		        else
		        {
    	            $mail_body = View::factory('emails/createuser')
    	                         ->bind('secret_url', $secret_url);		            
    	            $secret_url = url::site('login/create/'.urlencode($email).'/%token%', true, true);
        	        $response = $riverid_api->requestpassword($email, $mail_body);

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
		}				
        
		
		// Password reset request
        if ($_REQUEST and
	 		isset($_REQUEST['recover_email']) and $this->riverid_auth)
		{
		    if ( ! Valid::email($_REQUEST['recover_email']))
		    {
		        $this->template->set('errors', array(__('Recovery email address provided is invalid')));		        
		    }
		    else 
		    {
		        $email = $_REQUEST['recover_email'];
		        
		        // Is the email registed in this site?
		        $user = ORM::factory('user',array('email'=>$email));
		        
		        if ( ! $user->loaded())
		        {
		            $this->template->set('errors', array(__('Email not registered')));		        
		        } 
		        else
		        {
		            $riverid_api = RiverID_API::factory();		            
		            $mail_body = View::factory('emails/resetpassword')
		                         ->bind('secret_url', $secret_url);		            
		            $secret_url = url::site('login/reset/'.$user->id.'/%token%', true, true);
        	        $response = $riverid_api->requestpassword($email, $mail_body);
        	        
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
		}				
		// check, has the form been submitted, if so, setup validation
        else if ($_REQUEST AND
	 		isset($_REQUEST['username'], $_REQUEST['password']))
		{
			// Check Auth if the post data validates using the rules setup in the user model
			if ( Auth::instance()->login(
					$_REQUEST['username'],
					$_REQUEST['password']) )
			{
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('dashboard');
			}
			else
			{
				$this->template->set('username', $_REQUEST['username']);
				// Get errors for display in view
				$validation = Validation::factory($_REQUEST)
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
	    
	    if($user_id) 
	    {
	        $user = ORM::factory('user', $user_id);
	        if ($user->loaded())
	        {
	            $email = $user->email;
	        }	        
	    }	    
	    
	    if ($email and $_REQUEST and isset($_REQUEST['password_confirm'], $_REQUEST['password']) and $this->riverid_auth)
	    {
	        $post = Model_Auth_User::get_password_validation($_REQUEST);
	        if ( ! $post->check())
	        {
	            $errors = $post->errors('user');
	        }
	        else
	        {
	            $riverid_api = RiverID_API::factory();
	            $resp = $riverid_api->setpassword($email, $token, $_REQUEST['password']);
	            
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
	    	                      
	                   
	    if ($user->loaded() and $this->riverid_auth)
	    {
            $riverid_api = RiverID_API::factory();
            $resp = $riverid_api->confirmemail($new_email, $token);	        
            
            if ( ! $resp['status']) 
            {
                $errors = array($resp['error']);
            }
            else
            {
                $user->email = $new_email;
                $user->save();
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
