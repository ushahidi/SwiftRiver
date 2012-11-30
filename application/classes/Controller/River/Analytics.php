<?php defined('SYSPATH') or die('No direct script access');

/**
 * River analytics controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_River_Analytics extends Controller_River {

	public function before()
	{
		parent::before();

		// Redirect to the river's landing page if current user is not
		// an owner of the river
		if ( ! $this->owner)
		{
			$this->request->redirect($this->river_base_url);
		}

		$this->template->header->set('css', HTML::style("themes/default/media/css/analytics.css"))
			->bind("js", $charts_js);
		
		$charts_js = HTML::script("themes/default/media/js/d3.v2.min.js")
		    . HTML::script("themes/default/media/js/charts.js");

		$this->template->content = View::factory('pages/river/analytics/layout')
			->bind('active', $this->active)
			->bind('analytics_content', $this->analytics_content)
			->bind('river', $this->river)
			->bind('river_base_url', $this->river_base_url);
	}

	/**
	 * Landing page for the river analytics
	 */
	public function action_index()
	{
		$this->active = "summary";
		$this->analytics_content = View::factory('pages/river/analytics/summary')
			->set('total_drop_count', $this->river->drop_count)
			->bind('days_active', $days_active)
			->bind('breakdown', $breakdown)
			->bind('river_velocity', $river_velocity)
			->bind('used_quota', $used_quota);
		
		$breakdown = json_encode(Swiftriver_Trends::get_river_channels_breakdown($this->river->id));
		$days_active = $this->river->get_number_of_days_active();
		
		// Calculate the river velocity - no. of drops per day
		$river_velocity = round($this->river->drop_count/$days_active);
		$used_quota = ($this->river->drop_count >= $this->river->drop_quota)
			? 100
			: round($this->river->drop_count/$this->river->drop_quota, 2) * 100;		
	}

	public function action_channels()
	{
		$this->active = "channels";
		// Analytics view page
		$this->analytics_content = View::factory('pages/river/analytics/channels')
			->set('duration', __("year"))
			->bind('channels_trend', $channels_trend);

		$channels_trend = json_encode(Swiftriver_Trends::get_channels_trend($this->river->id, NULL));
	}
    
	/**
	 * Source activity visualization
	 */
	public function action_sources()
	{
		$this->active = "sources";
		$this->analytics_content = View::factory('pages/river/analytics/sources')
			->bind('sources_trend', $sources_trend);

		$sources_trend = json_encode(Swiftriver_Trends::get_sources_trend($this->river->id));
	}
	
	/**
	 * Summary analytics of the river i.e.
	 *	1. Content volume (pie chart)
	 *	2. Velocity of the river - rate at which drops are coming in
	 *	3. Total no. of drops in the river
	 *	4. No. of drops fetched today
	 *  5. Level of curation - % of drops that have been put in buckets
	 *  6. Composition of the tags
	 */
	public function action_summary()
	{
		$this->action_index();		
	}

}
