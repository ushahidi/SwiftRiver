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
	 * Account that owns the object being visited.
	 * Can be different from the current user's account above
	 */
	public $visited_account = NULL;

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
	 * URL of the looged in user's profile (dashboard)
	 * @var string
	 */
	protected $dashboard_url;

	/**
	 * Cache instance
	 * @var Cache
	 */
	protected $cache = NULL;
		
	/**
	 * Boolean indicating whether the logged in user is the default user
	 * @var boolean
	 */
	protected $anonymous = FALSE;
	
	
	/**
	 * Called from before() when the user is not logged in but they should.
	 *
	 * Override this in your own Controller / Controller_App.
	 */
	public function login_required()
	{
		// If anonymous access is enabled, log in the public user otherwise
		// present the login form
		if
		( 
			(bool) Model_Setting::get_setting('anonymous_access_enabled') AND 
			$this->request->controller() != 'login'
		)
		{
			if ( ! ($public_user = Cache::instance()->get('site_public_user', FALSE)))
			{
				$public_user = ORM::factory('user', array('username' => 'public'));
				Cache::instance()->set('site_public_user', $public_user, 86400 + rand(0,86400));
			}

			if ($public_user->loaded()) 
			{
				Auth::instance()->force_login($public_user);
				return;
			}
		}
		
		$uri = $this->request->url(TRUE);
		$query = URL::query(array('redirect_to' => $uri.URL::query()), FALSE);
		Request::current()->redirect('login'.$query);
	}

	/**
	 * Called from before() when the user does not have the correct rights to access a controller/action.
	 * This is the users personal dashboard
	 *
	 */
	private function access_required()
	{
		Request::current()->redirect($this->dashboard_url);
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
		// Execute parent::before first
		parent::before();
		
		try
		{
			$this->session = Session::instance();
		}
		catch (ErrorException $e)
		{
			session_destroy();
		}
		
		// Load the default Cache engine
		$this->cache = Cache::instance();
		
		// Open session
		$this->session = Session::instance();

		// If an api key has been provided, login that user
		$api_key = $this->request->query('api_key');
		if ($api_key)
		{
			$user_orm = ORM::factory('user', array('api_key' => $api_key));
			if ($user_orm->loaded() AND $user_orm->username != 'public') 
			{
				Auth::instance()->force_login($user_orm);
			}
			else
			{
				// api_keys used by apps. Instead of giving the login page
				// tell them something went wrong.
				throw new HTTP_Exception_403();
			}
		}

		// In case anonymous setting changed and user had a session,
		// log out 
		if
		(
			Auth::instance()->logged_in() AND 
			Auth::instance()->get_user()->username == 'public' AND 
			! (bool) Model_Setting::get_setting('anonymous_access_enabled')
		)
		{
			Auth::instance()->logout();
		}
		
		// Anonymous logged in and login controller requested, logout
		if (
				Auth::instance()->logged_in() AND 
				Auth::instance()->get_user()->username == 'public' AND 
				$this->request->controller() == 'login'
			)
		{
			Auth::instance()->logout();
		}

		// If we're not logged in, gives us chance to auto login
		$supports_auto_login = new ReflectionClass(get_class(Auth::instance()));
		$supports_auto_login = $supports_auto_login->hasMethod('auto_login');
		
		if( ! Auth::instance()->logged_in() AND $supports_auto_login)
		{
			// Controller exempt from auth check
			$exempt_controllers = Kohana::$config->load('auth.ignore_controllers');

			Auth::instance()->auto_login();
			if 
			( 
				! Auth::instance()->get_user() AND 
				! in_array($this->request->controller(), $exempt_controllers)
			)
			{
				$this->login_required();
			}
		}

		if 
		(
			// Auth is required AND user role given in auth_required is NOT logged in
			$this->auth_required !== FALSE AND 
				Auth::instance()->logged_in($this->auth_required) === FALSE
		)
		{
			if (Auth::instance()->logged_in())
			{
				// User is logged in but not on the secure_actions list
				$this->access_required();
			}
			else
			{
				$this->login_required();
			}
		}


		// Get the logged In User
		$this->user = Auth::instance()->get_user();
		
		if ($this->user)
		{

			// Is anonymous logged in?
			if ($this->user->username == 'public')
			{
				$this->anonymous = TRUE;
			}
			
			// Is this user an admin?
			$this->admin = $this->user->is_admin();
			
			if (strtolower(Kohana::$config->load('auth.driver')) == 'riverid' AND
	                      ! in_array($this->user->username, Kohana::$config->load('auth.exempt'))) 
			{
				$this->riverid_auth = TRUE;
			}

			// Does this user have an account space?
			if ( ! ($this->account = $this->cache->get('user_account_'.$this->user->id, FALSE)))
			{
				$this->account = ORM::factory('account')
					->where('user_id', '=', $this->user->id)
					->find();
				$this->cache->set('user_account_'.$this->user->id, $this->account, 3600 + rand(0,3600));
			}
			
				
			if ( ! $this->account->loaded() AND $this->request->uri() != 'register')
			{
				// Make the user create an account
				Request::current()->redirect('register');
			}
			
			// Logged in user's dashboard url
			if ($this->anonymous)
			{
				$this->dashboard_url = URL::site('welcome');
			}
			else
			{
				$this->dashboard_url = URL::site().$this->account->account_path;
			}
			
			// Build the base URL
			$visited_account_path = $this->request->param('account');
			if ($visited_account_path AND $visited_account_path != $this->account->account_path) 
			{
				$this->base_url = URL::site().$visited_account_path.'/'.$this->request->controller();
				$this->visited_account = ORM::factory('account', 
					array('account_path' => $visited_account_path));
				
				// Visited account doesn't exist?
				if ( ! $this->visited_account->loaded())
				{
					$this->request->redirect($this->dashboard_url);
				}
			}
			else
			{
				$this->base_url = URL::site().$this->account->account_path.'/'.$this->request->controller();
				$this->visited_account = $this->account;
			}
		}


		// Load Header & Footer & variables
		if ($this->auto_render) 
		{
			$this->template->header = View::factory('template/header')
			    ->bind('user', $this->user)
			    ->bind('site_name', $site_name)
				->bind('dashboard_url', $this->dashboard_url);

			$this->template->header->js = ''; // Dynamic Javascript
			$this->template->header->css = ''; // Dynamic CSS
			$this->template->header->meta = '';
			$this->template->header->show_nav = TRUE;
			$site_name = Model_Setting::get_setting('site_name');
			
			// Header Nav
			$this->template->header->nav_header = View::factory('template/nav/header')
			    ->bind('user', $this->user)
			    ->bind('admin', $this->admin)
			    ->bind('account', $this->account)
			    ->bind('anonymous', $this->anonymous);
			$this->template->header->nav_header->controller = $this->request->controller();
			
			if ($this->user)
			{
				$this->template->header->nav_header->num_notifications = Model_User_Action::count_notifications($this->user->id);
				if ( ! ($buckets = Cache::instance()->get('user_buckets_'.$this->user->id, FALSE)))
				{
					$buckets = json_encode($this->user->get_buckets_array($this->user));
					Cache::instance()->set('user_buckets_'.$this->user->id, $buckets, 3600 + rand(0,3600));
				}
				$this->template->header->bucket_list = $buckets;
				if ( ! ($rivers = Cache::instance()->get('user_rivers_'.$this->user->id, FALSE)))
				{
					$rivers = json_encode($this->user->get_rivers_array($this->user));
					Cache::instance()->set('user_rivers_'.$this->user->id, $rivers, 3600 + rand(0,3600));
				}
				$this->template->header->river_list = $rivers;
			}

			$this->template->content = '';
			$this->template->footer = View::factory('template/footer');

			if ( ! in_array($this->request->controller(), array('river', 'bucket', 'search')))
			{
				// Reset cookies
				Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, 'all');
			}
		}

	}
	
	
	/**
	 * @return	void
	 */
	public function after()
	{
		// Set a GC probability of 10%
		$gc = 10;
		
		// If the GC probability is a hit
		if (rand(0,99) <= $gc and $this->cache instanceof Cache_GarbageCollect)
		{
		     // Garbage Collect
		     $this->cache->garbage_collect();
		}
		
		// Execute parent::before first
		parent::after();
	}
	

	/**
	 * @return	void
	 */
	public function action_index()
	{
		if ($this->user)
		{
			$this->request->redirect($this->dashboard_url);
		}
		else
		{
			$this->login_required();
		}
	}	
}
