<?php
/**
 * Model_Droplet Unit test
 *
 * @see         Model_Droplet
 * @package     Swiftriver
 * @category    Tests
 * @author      Ushahidi Team
 * @author      Emmanuel Kala <emmanuel(at)ushahidi.com>
 * @copyright   (c) 2008-2011 Ushahidi Inc
 * @license     For license information, see LICENSE file
 */
class Swiftriver_Model_DropletTest extends Unittest_TestCase {
	
	/**
	 * @covers Model_Droplet::get_unprocessed_droplets
	 */
	public function test_get_unprocessed_droplets()
	{
		// Get the unprocessed items
		$unprocessed = Model_Droplet::get_unprocessed_droplets();
		
		// Verify that $unprocessed is an array
		$this->assertTrue(is_array($unprocessed));
		
		// Verify that the items are ordered in ascending order
		if (count($unprocessed) > 1)
		{
			// $first_item = $unprocessed[0];
			// $last_item = end($unprocessed);
			
			// TODO Compare the first and last items
			// The pub date of the first item should be earlier than that of the last item
		}
		
		// Garbage collection
		unset ($unprocessed);
	}
}
?>