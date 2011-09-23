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
class Controller_Project_Builder_Main extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'builder';
		$this->menu = View::factory('pages/project/builder/menu');
		$this->menu->active = '';
		$this->menu->project = $this->project;
		$this->services = Plugins::services();
	}
	
	/**
	 * List all the Feeds
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/project/builder/overview')
			->bind('services', $this->services)
			->bind('feeds', $result)
			->bind('paging', $pagination)
			->bind('menu', $this->menu)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project)
			->bind('msgs', $msgs);

		$this->template->header->js = View::factory('common/js/utility');

		$this->menu->active = 'main';


		$msgs = array();
		// Delete Action
		if ($_POST 
			AND isset($_POST['id']) 
			AND ! empty($_POST['id']) )
		{
			$feed = ORM::factory('feed', $_POST['id']);
			if ( $feed->loaded() )
			{
				// Delete existing keys first
				$existing = ORM::factory('feed_option')
					->where('feed_id', '=', $feed->id)
					->find_all();
				foreach ($existing as $key)
				{
					$key->delete();
				}
			}
			$feed->delete();
		}

		
		// Feeds
		$feeds = ORM::factory('feed');
		// Get the total count for the pagination
		$total = $feeds
			->where('project_id', '=', $this->project->id)
			->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'service'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $feeds->limit($pagination->items_per_page)
			->where('project_id', '=', $this->project->id)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
	
	/**
	 * Create a New Feed - Select Feed Service
	 *
	 * @return	void
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/project/builder/new')
			->bind('project', $this->project)
			->bind('menu', $this->menu)
			->bind('services', $this->services);

		$this->menu->active = 'new';
	}
}