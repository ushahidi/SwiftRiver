<?php defined('SYSPATH') OR die('No direct script access');

class Map_Init {

	public function __construct()
	{
		// Create Menu Item
		Swiftriver_Event::add('swiftriver.river.nav', array($this, 'river_nav'));
		Swiftriver_Event::add('swiftriver.bucket.nav', array($this, 'bucket_nav'));
	}

	public function river_nav()
	{
		$river = Swiftriver_Event::$data;

		// If menu is active
		$active_menu = Controller_Trend_Main::$active;
		echo ($active_menu == 'map') ? '<li class="active">' : '<li>';
		echo '<a href="'.URL::site().'river/trend/map/'.$river->id.'">'.__('Map').'</a></li>';
	}
	
	public function bucket_nav()
	{
		$bucket = Swiftriver_Event::$data;

		// If menu is active
		$active_menu = Controller_Trend_Main::$active;
		echo ($active_menu == 'map') ? '<li class="active">' : '<li>';
		echo '<a href="'.URL::site().'bucket/trend/map/'.$bucket->id.'">'.__('Map').'</a></li>';
	}
	
}

// Initialize the plugin
new Map_Init;