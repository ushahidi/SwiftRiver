<?php defined('SYSPATH') OR die('No direct script access');

/**
 * ChartsFX Visualization Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Chartsfx_Init {

	protected $charts;

	public function __construct()
	{
		$this->charts = array(
				'bubble' => __('Bubbles'),
				'sunburst' => __('Sunburst'),
				'cluster' => __('Cluster')
			);

		// Create Menu Items
		Swiftriver_Event::add('swiftriver.river.nav', array($this, 'chartsfx_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav', array($this, 'chartsfx_nav'));
	}

    /**
	 * Display ChartsFX link in river navigation bar
	 * 
	 * @return	void
	 */
	public function chartsfx_nav()
	{

		// Get the nav data
		$nav = Swiftriver_Event::$data;

		// Add new nav items
		foreach ($this->charts as $key => $value)
		{
			$nav[] = array(
				'id' => '',
				'active' => ( Request::$current->controller() == 'chartsfx' AND 
					Request::$current->action() == $key ) ? 'active' : '',
				'url' => '/trend/chartsfx/'.$key,
				'label' => $value
			);
		}

		// Return the nav $data
		Swiftriver_Event::$data = $nav;
	}	
}

// Initialize the plugin
new Chartsfx_Init;