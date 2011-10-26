<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Yahoo Placemaker Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class placemaker_init {
	
	public function __construct()
	{	
		// Hook into the 'extract_metadata' event
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'filter'));
	}

	public function filter()
	{
		// Include Yahoo Placemaker PHP Class
		include_once Kohana::find_file('vendor', 'placemakerphp/placemaker');

		// Get Our Service Key
		$settings = ORM::factory('placemaker_setting')
			->where('key', '=', 'appid')
			->find();

		if ( $settings->loaded() AND $settings->value )
		{
			try
			{
				// Get the droplet content
				$droplet = Swiftriver_Event::$data;
				$content = $droplet['droplet_content'];

				// Initialize Yahoo Placemaker
				$placemaker = new Placemaker();
				$placemaker->appid = $settings->value; // Set APPID
				$places = $placemaker->get_all($content);
				foreach($places as $place)
				{
					// $droplet is a memory reference so changes to it ought
					// to be visible from the script initiating this plugin
					$droplet['places'][] = array(
						'name' => $place->name,
						'latitude' => $place->latitude,
						'longitude' => $place->longitude,
						'source' => 'placemaker'
					);
				}
			}
			catch (Exception $e)
			{
				// Some kind of Yahoo Placemaker Error
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
			}
		}
	}
}

new placemaker_init;