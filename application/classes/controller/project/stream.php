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
			->bind('current', $current)
			->bind('items', $result)
			->bind('filter_tags', $tags)
			->bind('filter_service', $service)
			->bind('filter_author', $author)
			->bind('querystring', $querystring)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);

		$this->template->header->js = View::factory('pages/project/stream/js/overview')
			->bind('project', $this->project);

		// Current Page
		$current = $this->_current_page();
		$querystring = array_unique($this->request->query());

		// Items
		$items = ORM::factory('item');
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'item_date_pub'; // set default sorting
		$dir = isset($_GET['dir']) ? 'ASC' : 'DESC'; // set order_by

		// Get Query parameters
		$tags = isset($_GET['t']) ? $_GET['t'] : array();
		$service = isset($_GET['s']) ? $_GET['s'] : '';
		$author = isset($_GET['a']) ? $_GET['a'] : '';
		
		// Build Query
		$query = DB::select(array(DB::expr('DISTINCT items.id'), 'id'))->from('items');
		// Do we have tags to filter by?
		if ( is_array($tags) AND count($tags) )
		{
			$query->join('items_tags', 'INNER')
				->on('items_tags.item_id', '=', 'items.id');
			$query ->join('tags', 'INNER')
				->on('items_tags.tag_id', '=', 'tags.id');
			
			$query->where('tags.tag', 'IN', $tags);
		}
		// Do we have a service to filter by?
		if ( $service )
		{
			$query->where('items.service', '=', $service);
		}
		// Do we have an author to filter by?
		if ( $author )
		{
			$query->where('items.item_author', '=', $author);
		}
		// Do we have locations to filter by?
		// ++ coming back to this later
		$query->where('project_id', '=', $this->project->id);
		$query->order_by($sort, $dir);

		// Create a paginator
		$total = clone $query;
		$total = $total->execute()->count();
		$pagination = new Pagination(array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),  // route
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));

		// Finally execute main query with limits
		$result = $query->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->as_object()
			->execute();
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

	/**
	 * Generate current url + query strings
	 *
	 * @return	string $current page
	 */
	private function _current_page()
	{
		$current = URL::site().Request::current()->uri();
		$query = array_unique(Request::current()->query());
		$current .= '?'.http_build_query($query, NULL, '&');

		return $current;
	}
}