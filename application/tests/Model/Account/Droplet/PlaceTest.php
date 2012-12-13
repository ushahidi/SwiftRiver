<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account_Droplet_PlaceTest tests
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
class Model_Account_Droplet_PlaceTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'places', 'account_droplet_places');
	
	/**
	* Provides test data for test_get_place()
	*/
	public function provider_get_place()
	{
		return array(
			// Existing account drop place
			array("Heathrow", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'place_id' => 9
					)
				)
			),
			// New account drop place
			array("Another Custom Place", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'place_id' => 99
					)
				)
			)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_place
 	*/
	public function test_get_place($place, $droplet_id, $account_id, $expected)
	{
		Model_Account_Droplet_Place::get_place($place, $droplet_id, $account_id);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `account_id`, `droplet_id`, `place_id` ".
			"FROM `account_droplet_places` `adp`, `places` `p` ".
			"WHERE adp.place_id = p.id ".
			"AND `p`.`place_name` = '$place' ".
			"AND `droplet_id` = '$droplet_id' ".
			"AND `account_id` = '$account_id'; "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected, $results);
	}
 }

