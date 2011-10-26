<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Swiftriver_Dropletqueue Unit test
 *
 * @see         Dropletqueue
 * @package     Swiftriver
 * @category    Tests
 * @author      Ushahidi Team
 * @author      Emmanuel Kala <emmanuel(at)ushahidi.com>
 * @copyright   (c) 2008-2011 Ushahidi Inc
 * @license     For license information, see LICENSE file
 */

class Swiftriver_Dropletqueue_Test extends Unittest_TestCase {
	
	/**
	 * Dataprovider for test_add
	 *
	 * @return array
	 */
	public function provider_test_add()
	{
		return array(array(
			array(
				'channel' => 'twitter',
				'channel_filter_id' => '1',
				'identity_orig_id' => '193958229',
				'identity_username' => 'twitteruser',
				'identity_name' => 'Twitter User',
				'droplet_orig_id' => '2124569790',
				'droplet_type' => 'tweet',
				'droplet_title' => '',
				'droplet_content' => "Professor Wangari Maathai leads the votes for the 'Forbes Africa, Person of the Year Awards 2011'. Vote now - bit.ly/stzTju",
				'droplet_raw' => "Professor Wangari Maathai leads the votes for the 'Forbes Africa, Person of the Year Awards 2011'. Vote now - bit.ly/stzTju",
				'droplet_locale' => 'en',
				'droplet_date_pub' => '2011-10-15 22:15:10',
			)
		));
	}
	
	/**
	 * @dataProvider provider_test_add
	 * @covers Swiftriver_Dropletqueue:add
	 */
	public function test_add($droplet)
	{
		$this->assertArrayHasKey('droplet_orig_id', $droplet);
		$success = Swiftriver_Dropletqueue::add($droplet);
		
		// Assert that the droplet has been successfully added to the queue
		$this->assertTrue($success, 'The droplet could not be added to the queue.');
		
		// Verify that the droplet has a database ID
		$this->assertArrayHasKey('id', $droplet);
		$this->assertGreatherThan(0, $droplet['id']);
		
		return $droplet;
	}
	
	/**
	 * @covers Swiftriver_Dropletqueue::process
	 * @depends test_add
	 */
	public function test_process($droplet)
	{
		Swiftriver_Dropletqueue::process();
		
		// Load the droplet
		$orm_droplet = ORM::factory('droplet', $droplet['id']);
		
		// Verify that the droplet has been processed and saved
		$this->assertEquals(1, $orm_droplet->droplet_processed);
		$this->assertNotEmtpy($orm_droplet->droplet_date_processed);
		
		// Garbage collection
		unset($orm_droplet);
	}
	
	/**
	 * @covers Swiftriver_Dropletqueue::get_processed_droplets
	 * @depends test_process
	 */
	public function test_get_processed_droplets()
	{
		// Verify that the result is not empty
		$result = Swiftriver_Dropletqueue::get_processed_droplets();
		$this->assertNotEmpty($result);
		
		// Verify that the "processed" queue is empty
		$empty_result = Swiftriver_Dropletqueue::get_processed_droplets();
		$this->assertEmpty($empty_result);
		
		// Garbage colleciton
		unset ($result, $empty_result);
	}
}
?>