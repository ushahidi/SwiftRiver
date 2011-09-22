<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Stories Controller
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
class Controller_Project_Stories extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'stories';
	}

	/**
	 * List all the Stories
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/project/stories/overview')
			->bind('stories', $result)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);
		
		// Feeds
		$stories = ORM::factory('story');
		// Get the total count for the pagination
		$total = $stories
			->where('project_id', '=', $this->project->id)
			->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'story_title'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $stories->limit($pagination->items_per_page)
			->where('project_id', '=', $this->project->id)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}

	/**
	 * Add/Edit a Story
	 *
	 * @return	void
	 */
	public function action_edit()
	{
		$id = $this->request->param('id');
		$this->template->content = View::factory('pages/project/stories/edit')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('project', $this->project);
		
		// save the data
		if ($_POST)
		{
			$story = ORM::factory('story', $id);
			$post = $story->validate($_POST);
			if ($post->check())
			{
				$story->project_id = $this->project->id;
				$story->story_title = $post['story_title'];
				$story->story_summary = $post['story_summary'];
				$story->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				Request::current()->redirect('project/'.$this->project->id.'/stories');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('stories');
			}
		}
		else
		{
			if (is_numeric($id))
			{
				$story = ORM::factory('story', $id);
				$post = $story->as_array();
			}
		}	
	}	
	
}