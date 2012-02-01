<?php defined('SYSPATH') or die('No direct script access');
/**
 * Model_Place Unit test
 *
 * @see         Model_Place
 * @package     Swiftriver
 * @category    Tests
 * @author      Ushahidi Team
 * @author      Emmanuel Kala <emmanuel(at)ushahidi.com>
 * @copyright   (c) 2008-2012 Ushahidi Inc
 * @license     For license information, see LICENSE file
 */
class Swiftriver_Model_PlaceTest extends Unittest_TestCase {
	
	/**
	 * @covers Model_Place::get_place_by_lat_lon
	 */
	public function test_get_place_by_lat_lon()
	{
		// Valid place data; non-existent place
		$place_data = array(
			'place_name' => 'Place1',
			'latitude' => -89.283329963684082,
			'longitude' => 179.81666946411133,
			'source' => 'unittest'
		);
		
		// Find the place and save if not found
		$result = Model_Place::get_place_by_lat_lon($place_data, TRUE);
		$this->assertInstanceOf('Model_Place', $result);
		$result->delete();
		
		// Invalid place data
		$result = Model_Place::get_place_by_lat_lon(array('place_name' => 'PlaceY'));
		$this->assertFalse($result);
		
		// Garbage collection
		unset ($result, $place_data);
	}
}
?>