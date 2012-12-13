<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_River_Tag_TrendTest tests.
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
class Model_River_Tag_TrendTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array(
		'settings', 'seq', 'river_tag_trends', 
	);
	
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
		$this->assertEquals($expected_start, Model_River_Tag_Trend::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'river_tag_trends'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* Provides test data for test_get_trend()
	*/
	public function provider_get_trend()
	{
		return array(
			// No trends
			array(1, '2022-11-14 00:00:00', 'organization', array()),
			// Valid trends
			array(1, '2012-11-14 00:00:00', 'organization', 
				array(
					array(
						'tag' => 'Google',
						'count' => '6',
					),
					array(
						'tag' => 'New York Times',
						'count' => '3',
					),
					array(
						'tag' => 'Samsung',
						'count' => '3',
					),
				),
			),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_get_trend
	*/
	public function test_get_trend($river_id, $start_time, $tag_type, $expected)
	{
		$trends = Model_River_Tag_Trend::get_trend($river_id, $start_time, $tag_type);
		
		$this->assertEquals($expected, $trends);
	}
	
	/**
	* Provides test data for test_create_from_array()
	*/
	public function provider_create_from_array()
	{
		return array(
			// New trend
			array(
				array(
					array(
						'river_id' => 1,
						'date_pub' => '2022-11-14 04:00:00',
						'tag' => 'Google',
						'tag_type' => 'organization',
						'count' => 33,
					)
				), 
				array(
					'id' => 99,
					'hash' => 'b67ec7f115fdd8ef9044c7ce0c84237d',
					'river_id' => 1,
					'date_pub' => '2022-11-14 04:00:00',
					'tag' => 'Google',
					'tag_type' => 'organization',
					'count' => 33
				)
			),
			// Update existing trend
			array(
				array(
					array(
						'river_id' => 1,
						'date_pub' => '2012-11-14 20:00:00',
						'tag' => 'New York',
						'tag_type' => 'place',
						'count' => 7,
					)
				), 
				array(
					'id' => 3,
					'hash' => '5da768f7bedc49294a936764043ef691',
					'river_id' => 1,
					'date_pub' => '2012-11-14 20:00:00',
					'tag' => 'New York',
					'tag_type' => 'place',
					'count' => 8
				)
			),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_create_from_array
	* @depends test_get_trend
	*/
	public function test_create_from_array($trends, $expected)
	{
		Model_River_Tag_Trend::create_from_array($trends);
		
		$trend = ORM::factory("River_Tag_Trend", $expected['id']);
		$this->assertEquals($expected, $trend->as_array());
	}
}