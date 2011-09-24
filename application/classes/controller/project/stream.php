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
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/project/stream/overview')
			->bind('items', $result)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);
		$this->template->header->js = View::factory('pages/project/stream/js/overview')
			->bind('project', $this->project);
		
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

	/**
	 * Ajax Loaded Edit Window
	 *
	 * @return	void
	 */
	public function action_ajax_edit()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$item_id = ( isset($_POST['item_id']) AND ! empty($_POST['item_id']) ) ?
			$_POST['item_id'] : 0;

		if ($item_id)
		{
			$item = ORM::factory('item', $item_id);

			if ($item->loaded())
			{
				$content = View::factory('pages/project/stream/window/edit')
					->bind('item', $item)
					->bind('next', $next)
					->bind('previous', $previous)
					->bind('tags', $tags)
					->bind('links', $links)
					->bind('discussions', $discussions)
					->bind('source', $source);

				$tags = $item->tags->find_all();
				$links = $item->links->find_all();
				$discussions = $item->discussions->find_all();
				$source = $item->source;

				// Get Next Item
				$next = ORM::factory('item')
					->where('id', '>', $item_id)
					->find();
				
				// Get Previous Item
				$previous = ORM::factory('item')
					->where('id', '<', $item_id)
					->order_by('id','desc')
					->find();

				// Return the Content
				echo $content;
			}	
		}
	}
}