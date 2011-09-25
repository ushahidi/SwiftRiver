<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Dashboard Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Dashboard extends Controller_Sweeper {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * Renders the Dashboard
	 *
	 * @param   string   request method
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->header->page_title = __('Dashboard');
		$this->template->header->js = View::factory('pages/dashboard/js/overview');
		$this->template->header->js->first_date = $this->_get_first_date();
		$this->template->content = View::factory('pages/dashboard/overview');
		$this->template->content->stats = View::factory('pages/dashboard/stats')
			->bind('projects', $projects)
			->bind('stories', $stories)
			->bind('items', $items)
			->bind('tags', $tags)
			->bind('links', $links);

		$this->template->content->charts = View::factory('pages/dashboard/charts');

		$projects = ORM::factory('project')->count_all();
		$stories = ORM::factory('story')->count_all();
		$items = ORM::factory('item')->count_all();
		$tags = ORM::factory('tag')->count_all();
		$links = ORM::factory('link')->count_all();
	}

	private function _get_first_date()
	{
		$item = ORM::factory('item')
			->order_by('item_date_add', 'ASC')
			->find();
		if ( $item->loaded() )
		{
			return date('M j, Y', strtotime ( '-1 month' , strtotime($item->item_date_add) ));	
		}
		else
		{
			return date('M j, Y', strtotime ( '-1 month' , date('M j, Y') ));
		}
	}
}