<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Controller
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
class Controller_River extends Controller_Swiftriver {
	/**
	 * This River
	 */
	protected $river;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Does this account have a river?
		$this->river = ORM::factory('river')
			->where('account_id', '=', $this->account->id)
			->order_by('river_current', 'DESC')
			->find();
		
		if ( ! $this->river->loaded())
		{
			$this->river->river_name = __('River 1');
			$this->river->account_id = $this->account->id;
			$this->river->save();
		}
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/river/main')
			->bind('droplets', $filter_total)
			->bind('meter', $meter)
			->bind('filters', $filters)
			->bind('channels', $channels);
		
		// This River's total droplets
		$total_droplets = $this->river->droplets->count_all();

		// Build River Query
		$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'))
			->from('droplets');
		$query->join('rivers_droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets.id');
		$query->where('rivers_droplets.river_id', '=', $this->river->id);
		$query->order_by('droplet_date_pub', 'DESC');

		// Clone query before any filters have been applied
		$pre_filter = clone $query;
		$total = (int) $pre_filter->execute()->count();

		// SwiftRiver Plugin Hook -- Hook into River Droplet Query
		Swiftriver_Event::run('swiftriver.river.filter', $query);

		// Get our droplets
		$droplets = $query->execute()->as_array();
		$filter_total = (int) count($droplets);

		// Droplets Meter
		$meter = 0;
		if ($total)
		{
			$meter = ($filter_total / $total) * 100;
		}

		$filters = url::site().$this->account->account_path.'/river/filters/';
		$channels = url::site().$this->account->account_path.'/river/channels/';
	}

	/**
	 * Create a New River
	 *
	 * @return	void
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/river/new');
		$this->template->header->js = View::factory('pages/river/js/new');
		$this->template->header->js->channels = url::site().$this->account->account_path.'/river/channels/';
	}

	/**
	 * Ajax rendered filter control box
	 * 
	 * @return	void
	 */
	public function action_filters()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/river/filters_control');
	}

	/**
	 * Ajax rendered channel control box
	 * 
	 * @return	void
	 */
	public function action_channels()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/river/channels_control');
	}

	/**
	 * Ajax Title Editing Inline
	 *
	 * Edit River Name
	 * 
	 * @return	void
	 */
	public function action_ajax_title()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_REQUEST AND
			isset($_REQUEST['edit_id'], $_REQUEST['edit_value']) AND
			! empty($_REQUEST['edit_id']) AND 
			! empty($_REQUEST['edit_value']) )
		{
			if ($_REQUEST['edit_id'] === $this->river->id)
			{
				$this->river->river_name = $_REQUEST['edit_value'];
				$this->river->save();
			}
		}
	}	
}