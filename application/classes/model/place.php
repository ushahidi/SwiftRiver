<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Places
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
		$this->id = self::get_ids(1);
		$this->place_name = trim($this->place_name);
		$this->place_name_canonical = strtolower($this->place_name);
		$this->hash = md5($this->place_name.$this->longitude.$this->latitude);
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
			// FIXME: Geocode the place name
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
	public static function get_places(& $drops)
	{
		if (empty($drops))
			return;
			
		// Generate the place hashes and create a index hash array of the given places
		// linking a drop to a place
		$places_idx = array();
		foreach ($drops as $drop_key => & $drop)
		{
			if (isset($drop['places']))
			{
				foreach ($drop['places'] as $place_key => $place)
				{
					if ( ! isset($place['id']) )
					{
						$place['place_name'] = trim($place['place_name']);
						$hash = md5($place['place_name'].$place['longitude'].$place['latitude']);
						if (empty($places_idx[$hash]))
						{
							$places_idx[$hash]['place_name'] = $place['place_name'];
							$places_idx[$hash]['longitude'] = $place['longitude'];
							$places_idx[$hash]['latitude'] = $place['latitude'];
							$places_idx[$hash]['keys'] = array();
						}
						$places_idx[$hash]['keys'][] = array($drop_key, $place_key);
					}
				}
			}
		}
		
		if (empty($places_idx))
			return;
		
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Find those that exist
		$found = DB::select('hash', 'id')
					->from('places')
					->where('hash', 'IN', array_keys($places_idx))
					->execute()
					->as_array();
					
		// Update the found entries
		$new_place_count = count($places_idx);
		foreach ($found as $hash)
		{
			foreach ($places_idx[$hash['hash']]['keys'] as $keys)
			{
				list($drop_key, $place_key) = $keys;
				$drops[$drop_key]['places'][$place_key]['id'] = $hash['id'];
			}
			$new_place_count--;
			unset($places_idx[$hash['hash']]);
		}
		
		if ( ! empty($places_idx))
		{
			// Get a range of IDs to be used in inserting the new places
			$base_id = self::get_ids($new_place_count);
			
			$query = DB::insert('places', array('id', 'hash', 'place_name', 'place_name_canonical', 'longitude', 'latitude'));
			foreach ($places_idx as $hash => $value)
			{
				foreach ($value['keys'] as $key)
				{
					list($drop_key, $place_key) = $key;
					$drops[$drop_key]['places'][$place_key]['id'] = $base_id;
				}
				$query->values(array(
					'id' => $base_id++,
					'hash' => $hash,
					'place_name' => $value['place_name'],
					'place_name_canonical' => strtolower($value['place_name']),
					'longitude' => $value['longitude'],
					'latitude' => $value['latitude']
				));
			}
			$query->execute();
		}
		Swiftriver_Mutex::release(get_class());
	}
	
	/**
	 * Get a range of IDs to be used for inserting drops
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lowe limit of the range requested
	 */
	public static function get_ids($num)
	{
	    // Build River Query
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('places',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
}
