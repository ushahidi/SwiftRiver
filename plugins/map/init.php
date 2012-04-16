<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Map Visualization Init
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
class Map_Init {

	public function __construct()
	{
		// Create Menu Item
		Swiftriver_Event::add('swiftriver.river.nav', array($this, 'map_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav', array($this, 'map_nav'));
		
		// For adding our js/css to the header
		//Swiftriver_Event::add('swiftriver.template.head', array($this, 'template_header'));
	}
	
	/**
	 * Display map link in river|bucket navigation bar
	 * 
	 * @return	void
	 */
	public function map_nav()
	{
		// Get the nav data
		$nav = Swiftriver_Event::$data;

		// Add new nav item
		$nav[] = array(
			'id' => '',
			'active' => (Request::$current->controller() == 'map') ? 'active' : '',
			'url' => '/trend/map',
			'label' => __('Map')
		);

		// Return the nav $data
		Swiftriver_Event::$data = $nav;
	}
	
}

// Initialize the plugin
new Map_Init;