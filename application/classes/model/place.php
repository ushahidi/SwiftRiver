<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Places
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Place extends ORM
{
	/**
	 * A place has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'droplets_places'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_places'
			),				
		);

	/**
	 * Overload saving to perform additional functions on the place
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time places only
		if ($this->loaded() === FALSE)
		{
			// Save the date the place was first added
			$this->place_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
	
	/**
	 * Retrives a place using its latitude and longitude values
	 *
	 * @param array $coords Array with the place data; latitude and longitude
	 * @param bool $save Optionally saves the place record if no match is found
	 * @return mixed Model_Place if a record is found, FALSE otherwise
	 */
	public static function get_place_by_lat_lon($coords, $save = FALSE)
	{
		if (! is_array($coords))
			return FALSE;
			
		// Retrieve record using lon, lat
		$orm_place = ORM::factory('place')
				->where(DB::expr('X(place_point)'), '=', $coords['longitude'])
				->where(DB::expr('Y(place_point)'), '=', $coords['latitude'])
				->find();
		
		if ($orm_place->loaded())
		{
			return $orm_place;
		}
		
		// Check if $place_data has the place_name and place_source array keys
		if (array_key_exists('name', $coords) AND array_key_exists('source', $coords))
		{
			if ( ! $orm_place->loaded() AND $save)
			{
				// Create the place record
				$orm_place->place_name = $coords['name'];
				$orm_place->place_point = DB::expr("GeoFromText('POINT('".$coords['longitude']." ".$coords['latitude']."')')");
				$orm_place->place_source = $coords['source'];
			
				return $orm_place->save();
			}
		}
		else
		{
			return FALSE;
		}
	}
}
