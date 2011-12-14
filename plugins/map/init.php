<?php defined('SYSPATH') OR die('No direct script access');

class Map_Init {

	public function __construct()
	{
		// Create Menu Item
		Swiftriver_Event::add('swiftriver.river.nav', array($this, 'river_nav'));
	}

	public function river_nav()
	{
		$river = Swiftriver_Event::$data;

		echo '<li><a href="'.URL::site().'river/trend/map/'.$river->id.'">'.__('Map').'</a></li>';
	}
}

// Initialize the plugin
new Map_Init;