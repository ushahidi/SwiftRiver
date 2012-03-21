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
				'bubble' => __('Bubble Chart'),
				'sunburst' => __('Sunburst Chart'),
				'cluster' => __('Cluster Chart')
			);

		// Create Menu Items
		Swiftriver_Event::add('swiftriver.river.nav.more', array($this, 'river_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav.more', array($this, 'bucket_nav'));
	}

    /**
	 * Display ChartsFX link in river navigation bar
	 * 
	 * @return	void
	 */
	public function river_nav()
	{
		$river = Swiftriver_Event::$data;
		foreach ($this->charts as $key => $value)
		{
			$url = URL::site().$river->account->account_path.'/river/'.$river->river_name_url.'/trend/chartsfx/'.$key;
			echo '<li class="button-view"><a href="'.$url.'">'.$value.'</a></li>';
		}
	}
	
	/**
	 * Display ChartsFX link in the bucket navigation bar
	 * 
	 * @return	void
	 */
	public function bucket_nav()
	{
		$bucket = Swiftriver_Event::$data;
		foreach ($this->charts as $key => $value)
		{
			$url = URL::site().$bucket->account->account_path.'/bucket/'.$bucket->bucket_name_url.'/trend/chartsfx/'.$key;
			echo '<li class="button-view"><a href="'.$url.'">'.$value.'</a></li>';
		}
	}
	
}

// Initialize the plugin
new Chartsfx_Init;