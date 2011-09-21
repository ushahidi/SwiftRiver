<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Stream Controller
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
class Controller_Project_Stream extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'stream';
	}
	
	/**
	 * The Item Stream
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index($page = NULL)
	{
		$this->template->content = View::factory('pages/project/stream/overview')
			->bind('items', $result)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);
		$this->template->header->js = View::factory('pages/project/stream/js/overview');
		
		// Items
		$items = ORM::factory('item');
		// Get the total count for the pagination
		$total = $items
			->where('project_id', '=', $this->project->id)
			->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),  // route
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'item_date_pub'; // set default sorting
		$dir = isset($_GET['dir']) ? 'ASC' : 'DESC'; // set order_by
		$result = $items->limit($pagination->items_per_page)
			->where('project_id', '=', $this->project->id)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
}