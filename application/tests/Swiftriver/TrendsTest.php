<?php defined('SYSPATH') or die('No direct script access allowed');

/**
 * Unit test for the analytics trend helper. The tests simpply
 * verify that the methdods in the trend helper are returning the
 * desired data type
 * 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage Analytics - https://github.com/analytics/swiftriver-plugin-analytics
 * @author     Emmanuel Kala <emmanuel@ushahidi.com>
 * @category   Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_TrendsTest extends Unittest_TestCase {

	/**
	 * Provides data for the test_curated_drop_count
	 * 
	 * @return array
	 */
	public function provider_curated_drop_count()
	{
		return array(
			array(1),
		);
	}
	
	/**
	 * @covers Swiftriver_Trends::curated_drop_count
	 * @dataProvider provider_curated_drop_count
	 */
	public function test_curated_drop_count($river_id)
	{
		$curated_drop_count = Swiftriver_Trends::curated_drop_count($river_id);
		$this->assertGreaterThanOrEqual(0, $curated_drop_count);
	}
	
	/**
	 * Provides data for test_river_channels_breakdown
	 * @return array
	 */
	public function provider_river_channels_breakdown()
	{
		return array(
			array(2)
		);
	}
	
	/**
	 * @dataProvider provider_river_channels_breakdown
	 * @covers Swiftriver_Trends::get_river_channels_breakdown
	 */
	public function test_river_channels_breakdown($river_id)
	{
		$breakdown = Swiftriver_Trends::get_river_channels_breakdown($river_id);
		$this->assertTrue(is_array($breakdown));
	}

	/**
	 * Provides data for test_get_sources_trend
	 * @return array
	 */
	public function provider_get_sources_trend()
	{
		return array(
			array(2, 50),
		);
	}

	/**
	 * @dataProvider provider_get_sources_trend
	 * @covers Swiftriver_Trends::get_sources_trend
	 */
	public function test_get_sources_trend($river_id, $count)
	{
		$sources_trend = Swiftriver_Trends::get_sources_trend($river_id, $count);
		$this->assertTrue(is_array($sources_trend));

		foreach ($sources_trend as $channel => $data)
		{
			$this->assertLessThanOrEqual($count, count($data));
		}
	}
}