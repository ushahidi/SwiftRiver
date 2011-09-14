<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Feed Builder Controller
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
class Controller_Project_Builder extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'builder';
	}
	
	/**
	 * List all the Feeds
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index($page = NULL)
	{
		$this->template->content = View::factory('pages/project/builder/overview')
			->bind('feeds', $result)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);
		
		// Feeds
		$feeds = ORM::factory('feed');
		// Get the total count for the pagination
		$total = $feeds->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'feed_name'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $feeds->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
	
	/**
	 * Create a New Feed
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_edit($page = NULL)
	{
		$this->template->content = View::factory('pages/project/builder/edit')
			->bind('project', $this->project);
		
		$plugins = Kohana::$config->load('plugin');
		foreach ($plugins as $key => $plugin)
		{
			//print_r($plugin);
		}
	}
}