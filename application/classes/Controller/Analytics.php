<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Analytics controller for the user dashboard
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
class Controller_Analytics extends Controller_User {

	public function before()
	{
		parent::before();

		if ( ! $this->owner)
		{
			$this->request->redirect($this->dashboard_url);
		}
		
		$this->template->header->set('css', HTML::style("themes/default/media/css/analytics.css"))
			->bind("js", $charts_js);
		
		$charts_js = HTML::script("themes/default/media/js/d3.v2.min.js")
		    . HTML::script("themes/default/media/js/charts.js");
	}

	public function action_index()
	{
		// Set the analytics link as active
		$this->active = 'analytics-dashboard-link';

		// Set the view type
		$this->template->content->view_type = 'settings';

		$this->sub_content = View::factory('pages/user/analytics')
			->bind('river_growth_trend', $river_growth_trend);

		// Get the river growth data for the user's rivers
		$river_growth_trend = json_encode(Swiftriver_Trends::get_river_growth_trend($this->user));
	}
}
