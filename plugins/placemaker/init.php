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
		// Hook into routing
		Event::add('sweeper.item.post_save_new', array($this, 'filter'));
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
				// Get Item Content
				$item = Event::$data;
				$content = $item->item_content;

				// Initialize Yahoo Placemaker
				$placemaker = new Placemaker();
				$placemaker->appid = $settings->value; // Set APPID
				$places = $placemaker->get_all($content);
				foreach($places as $place)
				{
					$place = ORM::factory('place')
						->where(DB::expr('X(place_point)'), '=', $place->longitude)
						->where(DB::expr('Y(place_point)'), '=', $place->latitude)
						->find();

					if ( ! $place->loaded() )
					{
						$place->place_name = $place->name;
						$place->place_point = DB::expr("GeomFromText('POINT($place->longitude $place->latitude)')");
						$place->place_source = 'placemaker';
						$place->save();
					}

					if ( ! $item->has('places', $place))
					{
						$item->add('places', $place);
					}
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