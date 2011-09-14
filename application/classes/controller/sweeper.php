<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Sweeper Controller (Default)
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Sweeper extends Controller_Template {
	
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
	 * Active Project
	 * If set, we should redirect to this project by default, otherwise remain on dashboard
	 * @var int ID of the current project
	 */
	public $active_project = NULL;
	
	/**
	 * Called from before() when the user is not logged in but they should.
	 *
	 * Override this in your own Controller / Controller_App.
	 */
	public function login_required()
	{
		Request::current()->redirect('login');
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
		}
		
		if 
		(
			// auth is required AND user role given in auth_required is NOT logged in
			( Auth::instance()->logged_in($this->auth_required) === FALSE )
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
		
		// Load Header & Footer & variables
		$this->template->header = View::factory('template/header');
		$this->template->header->page_title = "";
		$this->template->header->active_project = "None Selected"; // Current Project
		$this->template->header->js = ""; // Dynamic Javascript
		$this->template->header->menu = View::factory('template/menu');
		$this->template->header->menu->active = Request::$current->controller();;	// Active Controller
		$this->template->header->menu->projects = ORM::factory('project')->find_all();
		$this->template->header->menu->active_project_id = "";
		$this->template->header->tab_menu = "";
		$this->template->content = "";
		$this->template->footer = View::factory('template/footer');
	}
	
	public function action_index()
	{
		// redirect
		Request::current()->redirect('dashboard');
		return;
	}
}