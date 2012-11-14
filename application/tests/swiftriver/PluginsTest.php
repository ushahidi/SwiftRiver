<?php defined('SYSPATH') or die('No direct script access.'); 
/**
 * Unit test for Swiftriver_Plugins
 *
 * @author     Ushahidi Dev Team
 * @package    SwiftRiver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Tests
 * @copyright  (c) 2008-2011 Ushahidi Inc - http://www.ushahidi.com
 */
class Swiftriver_PluginsTest extends Unittest_TestCase {
	
	/**
	 * @covers Swiftriver_Plugins::channels
	 */
	public function test_channels()
	{
		// Get the no. of enabled plugins from the DB
		$plugin_count = ORM::factory('Plugin')->where('plugin_enabled', '=', 1)->count_all();
		
		if ($plugin_count == 0)
		{
			$this->markTestSkipped('No plugins found in the database');
		}
		
		// Get the list of channels - Requires these to have been enabled in the DB
		$channels = SwiftRiver_Plugins::channels();
		
		// Return value should be an array
		$this->assertTrue(is_array($channels));
		
		// Return value should have at least one item
		$this->assertGreaterThanOrEqual(1, count($channels), "No channel plugins found");
		
		// Clean up
		unset ($channels);
	}
}
?>