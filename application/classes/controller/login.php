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
	 * Log User In
	 * 
	 * @return void
	 */	
	public function action_index()
	{	
		// If user already signed-in
		if (Auth::instance()->logged_in() != 0)
		{
			$this->_redirect();
		}
		
		// check, has the form been submitted, if so, setup validation
        if ($_REQUEST AND
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
