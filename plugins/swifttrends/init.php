<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Swifttrends Visualization Init
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
class Swifttrends_Init {

	protected $trend;

	public function __construct()
	{
		$this->trend = array(
				'tags' => __('Tags'),
				'links' => __('Links'),
				'places' => __('Places'),
				'media' => __('Media')
			);

		// Create Menu Items
		Swiftriver_Event::add('swiftriver.river.nav', array($this, 'trends_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav', array($this, 'trends_nav'));
	}

    /**
	 * Display Trend link in river navigation bar
	 * 
	 * @return	void
	 */
	public function trends_nav()
	{

		// Get the nav data
		$nav = Swiftriver_Event::$data;

		// Add new nav items
		foreach ($this->trend as $key => $value)
		{
			$nav[] = array(
				'id' => '',
				'active' => ( Request::$current->controller() == 'default' AND 
					Request::$current->action() == $key ) ? 'active' : '',
				'url' => '/trend/default/'.$key,
				'label' => $value
			);
		}

		// Return the nav $data
		Swiftriver_Event::$data = $nav;
	}	
}

// Initialize the plugin
new Swifttrends_Init;