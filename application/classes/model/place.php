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
	 * Returns the droplet's places as an array
	 */
	public static function get_droplet_places($orm_droplet)
	{
		$query = DB::select('place_name',
							array(DB::expr('X(place_point)'), 'longitude'),
							array(DB::expr('Y(place_point)'), 'latitude'),
							array('place_source', 'source'))
					->from('droplets')
					->join('droplets_places', 'INNER')
					->on('droplets_places.droplet_id', '=', 'droplets.id')
					->join('places', 'INNER')
					->on('droplets_places.place_id', '=', 'places.id')
					->where('droplets.id', '=', $orm_droplet->id);
		
		return $query->execute()->as_array();
	}
	
	/**
	 * Retrives a place using its latitude and longitude values
	 *
	 * @param array $coords Array with the place data; latitude and longitude
	 * @param bool $save Optionally saves the place record if no match is found
	 * @return mixed Model_Place if a record is found, FALSE otherwise
	 */
	public static function get_place_by_lat_lon($data, $save = FALSE)
	{
		if (! is_array($data) OR empty($data))
			return FALSE;
		
		// Latitude and longitude must be specified
		if (empty($data['latitude']) OR empty($data['longitude']))
			return FALSE;
			
		// Retrieve record using lon, lat
		$orm_place = ORM::factory('place')
				->where(DB::expr('X(place_point)'), '=', $data['longitude'])
				->where(DB::expr('Y(place_point)'), '=', $data['latitude'])
				->find();
		
		if ($orm_place->loaded())
		{
			return $orm_place;
		}
		
		// Check if $place_data has the place_name and place_source array keys
		if (array_key_exists('place_name', $data) AND array_key_exists('source', $data))
		{
			if ( ! $orm_place->loaded() AND $save)
			{
				// Create the place record
				$orm_place->place_name = $data['place_name'];
				$orm_place->place_point = DB::expr("GeomFromText('POINT(".$data['longitude']." ".$data['latitude'].")')");
				$orm_place->place_source = $data['source'];
			
				return $orm_place->save();
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Retrives a place using its name
	 *
	 * @param string $place_name Name of the place
	 * @param bool $save Optionally saves the place record if no match is found
	 * @return mixed Model_Place if a record is found, FALSE otherwise
	 */
	public static function get_place_by_name($place_name, $save = FALSE)
	{
		
		// Retrieve record using lon, lat
		$orm_place = ORM::factory('place')
				->where('place_name', '=', $place_name)
				->find();
		
		if ($orm_place->loaded())
		{
			return $orm_place;
		}
		
		if ( ! $orm_place->loaded() AND $save)
		{
			// Create the place record
			$orm_place->place_name = $place_name;		
			return $orm_place->save();
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if a given place already exists. 
	 * The parameter $places is an array of hashes containing the 
	 * place_name, latitude and longitude
	 * E.g: $place = array('place_name' => 'Nairobi', 'latitude' => '-1.2857', 'longitude' => '36.820174', 'source' => 'placemaker')
	 *
	 * @param array $place Array of hashes described above
	 * @return mixed array of place ids if the place exists, FALSE otherwise
	 */
	public static function get_places($places)
	{
		// First try to add any links missing from the db
		// The below generates the below query to find missing links and insert them all at once:
		/*
		 *   	insert into places (place_name, place_point)
		 *   	SELECT DISTINCT * 
		 *   	FROM (
		 *   		SELECT 'Mombasa' AS `place_name`, GeomFromText('POINT(39.666653 -4.050063)') AS `place_point` UNION ALL 
		 *   		SELECT 'Nairobi' AS `place_name`, GeomFromText('POINT(36.820174 -1.2857)') AS `place_point`
		 *   	) AS `a` 
		 *   	WHERE (place_name, place_point) NOT IN (
		 *   		SELECT `place_name`, `place_point` 
		 *   		FROM `places` 
		 *   		WHERE (place_name, X(place_point), Y(place_point)) IN (
		 *   			('Nairobi', '-1.2857', '36.820174'), 
		 *   			('Mombasa', '-4.050063', '39.666653')
		 *   		)
		 *   	);
         *   	
         *   	
		 */
		$query = DB::select()->distinct(TRUE);
		$place_subquery = NULL;
		foreach ($places as $place)
		{
			$union_query = DB::select(
							array(DB::expr("'".addslashes($place['place_name'])."'"), 'place_name'), 		
							array(DB::expr("GeomFromText('POINT(".$place['longitude']." ".$place['latitude'].")')"), 'place_point'));
			if ( ! $place_subquery)
			{
				$place_subquery = $union_query;
			}
			else
			{
				$place_subquery = $union_query->union($place_subquery, TRUE);
			}
		}
		if ($place_subquery)
		{
			$query->from(array($place_subquery,'a'));
			$sub = DB::select('place_name', 'place_point')
			           ->from('places')
			           ->where(DB::expr('(place_name, Y(place_point), X(place_point))'), 'IN', $places);
			$query->where(DB::expr('(place_name, place_point)'), 'NOT IN', $sub);
			DB::insert('places', array('place_name', 'place_point'))->select($query)->execute();
		}
		
		// Get the tag IDs
		if ($places)
		{
			$query = DB::select('id')
			           ->from('places')
			           ->where(DB::expr('(place_name, Y(place_point), X(place_point))'), 'IN', $places);

			return $query->execute()->as_array();
		}
		
		return FALSE;
	}
}
