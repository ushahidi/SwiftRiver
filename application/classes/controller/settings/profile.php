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
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Profile extends Controller_Settings_Main {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
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
			->bind('user', $this->user)
			->bind('stories', $stories)
			->bind('projects', $projects);
			
		$stories = ORM::factory('story')
			->where('user_id', '=', $this->user->id)
			->count_all();

		$projects = ORM::factory('project')
			->where('user_id', '=', $this->user->id)
			->count_all();
	}
	
	/**
	 * Edit My Profile
	 *
	 * @return	void
	 */
	public function action_edit()
	{
			
	}
}
