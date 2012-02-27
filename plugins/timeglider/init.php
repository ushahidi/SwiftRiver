<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Time Glider Visualization Init
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
class Timeglider_Init {

	public function __construct()
	{
		// Create Menu Item
		Swiftriver_Event::add('swiftriver.river.nav.more', array($this, 'river_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav.more', array($this, 'bucket_nav'));
		
		// For adding our js/css to the header
		//Swiftriver_Event::add('swiftriver.template.head', array($this, 'template_header'));
	}
	
	

    /**
	 * Display Time Glider link in river navigation bar
	 * 
	 * @return	void
	 */
	public function river_nav()
	{
		$river = Swiftriver_Event::$data;
		$url = URL::site().$river->account->account_path.'/river/'.$river->river_name_url.'/trend/timeglider';
		echo '<li class="button-view"><a href="'.$url.'">'.__('Time Glider').'</a></li>';
	}
	
	/**
	 * Display Time Glider link in the bucket navigation bar
	 * 
	 * @return	void
	 */
	public function bucket_nav()
	{
		$bucket = Swiftriver_Event::$data;
		$url = URL::site().$bucket->account->account_path.'/river/'.$bucket->bucket_name_url.'/trend/timeglider';
		echo '<li class="button-view"><a href="'.$url.'">'.__('Time Glider').'</a></li>';
	}
	
}

// Initialize the plugin
new Timeglider_Init;