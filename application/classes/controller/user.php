<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Controller
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
class Controller_User extends Controller_Swiftriver {
	
	/**
	 * sub content
	 */
	private $sub_content;
	
	/**
	 * active
	 */
	private $active;
	
	/**
	 * template type
	 */
	private $template_type;
	
	/**
	 * visited account
	 */
	private $visited_account;

	/**
	 * visited account path
	 */
	private $visited_account_path;

	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the account we are visiting
		$this->visited_account_path = $this->request->param('account');
		
		$this->visited_account = ORM::factory('account', array('account_path' => $this->visited_account_path));
		
		if ( ! $this->visited_account->loaded())
		{
			$this->request->redirect('dashboard');
		}

		$this->template->content = View::factory('pages/user/layout')
			 ->bind('sub_content', $this->sub_content)
			 ->bind('template_type', $this->template_type)
			 ->bind('active', $this->active)
			 ->bind('user', $this->visited_account->user)
			 ->bind('account_path', $this->visited_account_path);
					
		
	}
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->sub_content = View::factory('pages/user/rivers')
		                         ->bind('rivers', $rivers);
		$this->active = 'rivers';
		$this->template_type = 'list';
		
		// Get rivers visible to a user
		$rivers = $this->user->get_other_user_visible_rivers($this->visited_account->user->id);
	}
	
	/**
	 * @return	void
	 */
	public function action_buckets()
	{
		$this->sub_content = View::factory('pages/user/buckets')
		                         ->bind('buckets', $buckets);
		$this->active = 'buckets';
		$this->template_type = 'list';
		
		// Get rivers visible to a user
		$buckets = $this->user->get_other_user_visible_buckets($this->visited_account->user->id);
	}	
}