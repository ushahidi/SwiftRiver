<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Places
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
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
	 * @param string $latitude
	 * @param string $longitude
	 * @return Model_Place
	 */
	public static function get_place_by_lat_lon($latitude, $longitude)
	{
		return ORM::factory('place')
				->where(DB::expr('X(place_point)'), '=', $longitude)
				->where(DB::expr('Y(place_point)'), '=', $latitude)
				->find();
	}
}
