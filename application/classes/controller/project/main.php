<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Controller - Handles Individual Projects
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
class Controller_Project_Main extends Controller_Sweeper {
	
	/**
	 * This Project
	 */
	protected $project;
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// First we need to make sure this project
		// actually exists
		$id = $this->request->param('id');
		
		if (is_numeric($id))
		{
			$this->project = ORM::factory('project', $id);
			if ( ! $this->project->loaded())
			{
				// It doesn't -- redirect back to all projects
				$this->request->redirect('projects');
			}
		}
		else
		{
			// Non-Numeric ID -- redirect back to all projects
			$this->request->redirect('projects');
		}
		
		$this->template->header->active_project = $this->project->project_title;
		$this->template->header->menu->active_project_id = $this->project->id;
		$this->template->header->page_title = $this->project->project_title;
		$this->template->header->tab_menu = View::factory('pages/project/menu');
		$this->template->header->tab_menu->project_id = $this->project->id;
		$this->template->header->tab_menu->active = '';
	}
	
	/**
	 * List all the Projects
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/project/overview')
			->bind('project', $this->project)
			->bind('feeds', $feeds)
			->bind('stories', $stories)
			->bind('items', $items)
			->bind('tags', $tags)
			->bind('links', $links)
			->bind('places', $places);

		$this->template->header->js = View::factory('pages/project/js/overview')
			->bind('first_date', $first_date)
			->bind('project', $this->project);
		
		$first_date = $this->_get_first_date();

		$feeds = $this->project->feeds->count_all();
		$stories = $this->project->stories->count_all();
		$items = $this->project->items->count_all();

		// Tags
		$tags = DB::select(array(DB::expr('DISTINCT tags.id'), 'id'))
			->from('tags')
			->join('items_tags', 'INNER')
				->on('items_tags.tag_id', '=', 'tags.id')
			->join('items', 'INNER')
				->on('items_tags.item_id', '=', 'items.id')
			->where('items.project_id', '=', $this->project->id)
			->execute()->count();

		// Links
		$links = DB::select(array(DB::expr('DISTINCT links.id'), 'id'))
			->from('links')
			->join('items_links', 'INNER')
				->on('items_links.link_id', '=', 'links.id')
			->join('items', 'INNER')
				->on('items_links.item_id', '=', 'items.id')
			->where('items.project_id', '=', $this->project->id)
			->execute()->count();

		// places
		$places = DB::select(array(DB::expr('DISTINCT places.id'), 'id'))
			->from('places')
			->join('items_places', 'INNER')
				->on('items_places.place_id', '=', 'places.id')
			->join('items', 'INNER')
				->on('items_places.item_id', '=', 'items.id')
			->where('items.project_id', '=', $this->project->id)
			->execute()->count();		
	}

	/**
	 * Get the Chart Start Date
	 *
	 * @return	string date
	 */
	private function _get_first_date()
	{
		$item = ORM::factory('item')
			->where('project_id', '=', $this->project->id)
			->order_by('item_date_add', 'ASC')
			->find();
		if ( $item->loaded() )
		{
			return date('M j, Y', strtotime ( '-1 day' , strtotime($item->item_date_add) ));	
		}
		else
		{
			return date('M j, Y', strtotime ( '-1 day' , strtotime(date('M j, Y')) ));
		}
	}	
}