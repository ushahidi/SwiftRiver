<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Discussion Controller
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
class Controller_Project_Discussions_Main extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'discussion';
		$this->menu = View::factory('pages/project/discussions/menu');
		$this->menu->active = '';
		$this->menu->project = $this->project;
	}
	

	/**
	 * List all the Feeds
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/project/discussions/overview')
			->bind('discussions', $result)
			->bind('menu', $this->menu)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);

		$this->menu->active = 'main';	

		// Discussions
		$discussions = ORM::factory('discussion');
		// Get the total count for the pagination
		$total = $discussions
			->where('project_id', '=', $this->project->id)
			->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'discussion_date_add'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $discussions->limit($pagination->items_per_page)
			->where('project_id', '=', $this->project->id)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
}