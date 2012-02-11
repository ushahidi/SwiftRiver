<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Swiftriver Base controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Swiftriver extends Controller_Template {
	
	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 */
	public $auto_render = TRUE;
	
	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'template/layout';
	
	/**
	 * Controls access for the controller and sub controllers, if not set to FALSE we will only allow user roles specified
	 *
	 * Can be set to a string or an array, for example array('editor', 'admin') or 'login'
	 */
	public $auth_required = FALSE;
	
	/**
	 * Active River
	 * If set, we should redirect to this river by default, otherwise remain on dashboard
	 * @var int ID of the current river
	 */
	public $active_river = NULL;

	/**
	 * Logged In User
	 */
	public $user = NULL;

	/**
	 * Current Users Account
	 */
	public $account = NULL;

	/**
	 * This Session
	 */
	protected $session;
	
	/**
	 * Are we using RiverID?
	 */
	public $riverid_auth = FALSE;
	
	/**
	 * Base URL for constructing XHR endpoints
	 * @var string
	 */
	protected $base_url;
	
	/**
	 * Called from before() when the user is not logged in but they should.
	 *
	 * Override this in your own Controller / Controller_App.
	 */
	public function login_required()
	{
		Request::current()->redirect('welcome');
	}

	/**
	 * Called from before() when the user does not have the correct rights to access a controller/action.
	 * This is the users personal dashboard
	 *
	 */
	public function access_required()
	{
		Request::current()->redirect('dashboard');
	}	
	
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
		try
		{
			$this->session = Session::instance();
		}
		catch (ErrorException $e)
		{
			session_destroy();
		}
		
		// Execute parent::before first
		parent::before();
		
		// Open session
		$this->session = Session::instance();
		
		//if we're not logged in, gives us chance to auto login
		$supports_auto_login = new ReflectionClass(get_class(Auth::instance()));
		$supports_auto_login = $supports_auto_login->hasMethod('auto_login');
		if( ! Auth::instance()->logged_in() AND $supports_auto_login)
		{
			Auth::instance()->auto_login();
			if ( ! Auth::instance()->get_user() )
			{
				$this->login_required();
			}
		}

		if 
		(
			// auth is required AND user role given in auth_required is NOT logged in
			$this->auth_required !== FALSE AND 
				Auth::instance()->logged_in($this->auth_required) === FALSE
		)
		{
			if (Auth::instance()->logged_in())
			{
				// user is logged in but not on the secure_actions list
				$this->access_required();
			}
			else
			{
				$this->login_required();
			}
		}

		// Logged In User
		$this->user = Auth::instance()->get_user();
		
		// Is this user an admin?
		if ($this->user->has('roles',ORM::factory('role',array('name'=>'admin'))))
		{
			$this->admin = TRUE;
		} 
		else
		{
		    $this->admin = FALSE;
		}
		
		if (strtolower(Kohana::$config->load('auth.driver')) == 'riverid' and
                      ! in_array($this->user->username, Kohana::$config->load('auth.exempt'))) 
		{
			$this->riverid_auth = TRUE;
		}

		// Does this user have an account space?
		$this->account = ORM::factory('account')
			->where('user_id', '=', $this->user->id)
			->find();
			
		if ( ! $this->account->loaded() && $this->request->uri() != 'dashboard/register')
		{
			// Make the user create an account
			Request::current()->redirect('dashboard/register');
		}
		
		
		// Build the base URL
		$visited_account = $this->request->param('account');
		if ($visited_account and $visited_account != $this->account->account_path) 
		{
			$this->base_url = URL::site().$visited_account.'/'.$this->request->controller();
		}
		else
		{
			$this->base_url = URL::site().$this->account->account_path.'/'.$this->request->controller();
		}		
		
		// Load Header & Footer & variables
		if ($this->auto_render) 
		{
			$this->template->header = View::factory('template/header');
			$this->template->header->js = ''; // Dynamic Javascript
			
			// Header Nav
			$this->template->header->nav_header = View::factory('template/nav/header');
			$this->template->header->nav_header->rivers = $this->user->get_rivers();
			$this->template->header->nav_header->buckets = $this->user->get_buckets();
			$this->template->header->nav_header->user = $this->user;
			$this->template->header->nav_header->admin = $this->admin;
			$this->template->header->nav_header->account = $this->account;
			$this->template->header->nav_header->num_notifications = Model_User_Action::count_notifications($this->user->id);
			
			$this->template->content = '';
			$this->template->footer = View::factory('template/footer');
		}
	}
	

	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->request->redirect('/dashboard');
	}	
}
