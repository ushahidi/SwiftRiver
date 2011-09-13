<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Profile Controller
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
class Controller_Profile extends Controller_Sweeper {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get Currently Logged in User
		$this->user = Auth::instance()->get_user();
		
		$this->template->header->page_title = __('My Profile').
			" : ".$this->user->name.
			" (".$this->user->email.")";
		$this->template->header->tab_menu = View::factory('pages/profile/menu');
	}
	
	/**
	 * View Profile Statistics
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index($page = NULL)
	{
		$this->template->content = View::factory('pages/profile/overview')
			->bind('user', $this->user);
	}
	
	/**
	 * Edit My Profile
	 *
	 * @return	void
	 */
	public function action_edit()
	{
		$this->template->content = View::factory('pages/projects/edit');
		$this->template->content->sub_title = __('Add/Edit A Project');
		$this->template->content->form = View::factory('pages/projects/form')
			->bind('post', $post)
			->bind('errors', $errors);
		
		// save the data
		if ($_POST)
		{
			$project = ORM::factory('project', $id);
			$post = $project->validate($_POST);
			if ($post->check())
			{
				$project->project_title = $post['project_title'];
				$project->project_description = $post['project_description'];
				$project->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('projects');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('projects');
			}
		}
		else
		{
			if (is_numeric($id))
			{
				$project = ORM::factory('project', $id);
				$post = $project->as_array();
			}
		}	
	}
}