<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Charts Controller
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
class Controller_Charts extends Controller_Swiftriver {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * Ajax Loaded Chart Data For Item Streams
	 * 
	 * @return	void
	 */
	public function action_index($project_id = NULL)
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$project_id = $this->request->param('project_id');
		
		echo $this->_get_items($project_id);
	}

	//
	private function _get_items($project_id = NULL)
	{
		$js_array = array();

		if ( ! $project_id)
		{
			$projects = ORM::factory('project')
				->find_all();
		}
		else
		{
			$projects = ORM::factory('project')
				->where('id', '=', $project_id)
				->find_all();
		}

		foreach ($projects as $project)
		{
			$js = array();
			$query = DB::select(
					array(DB::expr('DATE_FORMAT(item_date_add, "%Y-%m-%d")'), 'date'),
					array('COUNT("id")', 'counts')
				)
				->from('items')
				->where('project_id', '=', $project->id)
				->group_by('date');

			$total = clone $query;
			
			if ($total->execute()->count())
			{
				$items = $query->execute();
				$i = 0;
				foreach ($items as $item)
				{
					$label = ($i == 0) ? $project->project_title : '';
					$js[] = array( $item['date'].' 0:00AM', (int) $item['counts'], $label );
					$i++;
				}
				$js_array[] = $js;
			}
			else
			{
				//$js = array();
			}
		}

		return json_encode($js_array);
	}
}
