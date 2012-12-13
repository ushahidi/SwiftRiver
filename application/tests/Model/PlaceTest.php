<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Place tests
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @group      swiftriver
 * @group      swiftriver.core
 * @group      swiftriver.core.model
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_PlaceTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'places');
	
	/**
	* Provides test data for test_get_ids()
	*/
	public function provider_get_ids()
	{
		 return array(
			 // Get one ID
			 array(1, 99, 100),
			 // Get a range of 10 IDs
			 array(10, 99, 109),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_ids
 	*/
	public function test_get_ids($num, $expected_start, $expected_next)
	{
		$this->assertEquals($expected_start, Model_Place::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'places'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$place = ORM::factory("Place");
		$place->place_name = 'Test Place';
		$place->latitude = -1;
		$place->longitude = 36;
		$place->save();
		
		$this->assertEquals('test place', $place->place_name_canonical);
		$this->assertEquals(md5('Test Place36-1'), $place->hash);
	}
	
	/**
	* Provides test data for test_get_place_by_name()
	*/
	public function provider_get_place_by_name()
	{
		 return array(
			 // Existing place
			 array('Gaza', FALSE, 5),
			 // Non existing place without save
			 array('Neverland', FALSE, NULL),
			 // Non existing place with save
			 array('Mount Crux', TRUE, 99),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_place_by_name
 	*/
	public function test_get_place_by_name($place_name, $save, $expected_id)
	{
		$place = Model_Place::get_place_by_name($place_name, $save);
		
		if (isset($expected_id))
		{
			$expected = ORM::factory('Place', $expected_id);
			$this->assertEquals($expected->as_array(), $place->as_array());
		}
		else
		{
			$this->assertFalse($place->loaded());
		}
	}
	
	/**
	* Provides test data for test_get_places()
	*/
	public function provider_get_places()
	{
		 return array(
			 // No places array in drop
			 array(array(), array()),
			 // Places array in drop for existing place
			 array(
				 array(
					 array(
						 'places' => array(
							 array(
								 'place_name' => 'China',
								 'longitude' => '105',
								 'latitude' => '35',
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'places' => array(
							 array(
								 'id' => 3,
								 'place_name' => 'China',
								 'longitude' => '105',
								 'latitude' => '35',
							 )
						 )
					 )
				 )
			 ),
			 // Places array for a new place
			 array(
				 array(
					 array(
						 'places' => array(
							 array(
								 'place_name' => 'Neverland',
								 'longitude' => '0',
								 'latitude' => '0',
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'places' => array(
							 array(
								 'id' => 99,
								 'place_name' => 'Neverland',
								 'longitude' => '0',
								 'latitude' => '0',
							 )
						 )
					 )
				 )
			 ),
			 //// Places array with existing and new place
			 array(
				 array(
					 array(
						 'places' => array(
							 array(
								 'place_name' => 'China',
								 'longitude' => '105',
								 'latitude' => '35',
							 ),
							 array(
								 'place_name' => 'Neverland',
								 'longitude' => '0',
								 'latitude' => '0',
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'places' => array(
							 array(
								 'id' => 3,
								 'place_name' => 'China',
								 'longitude' => '105',
								 'latitude' => '35',
							 ),
							 array(
								 'id' => 99,
								 'place_name' => 'Neverland',
								 'longitude' => '0',
								 'latitude' => '0',
							 )
						 )
					 )
				 )
			 ),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_places
 	*/
	public function test_get_places($drop, $expected)
	{
		Model_Place::get_places($drop);
		$this->assertEquals($expected, $drop);
	}
 }