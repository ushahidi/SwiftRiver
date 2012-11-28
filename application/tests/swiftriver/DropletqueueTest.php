<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Swiftriver_Dropletqueue Unit test
 *
 * This test uses the story framework phpunit/PHPUnit_Story to test the dropletqueue. This
 * is because the SUT is analgous to a conveyor belt where items(droplets) are passed from
 * one stage to the next. It therefore allows the different components of the queue to be 
 * chained in the order in which they are executed
 *
 * @see         Swiftriver_Dropletqueue
 * @package     Swiftriver
 * @category    Unit Tests
 * @author      Ushahidi Team
 * @author      Emmanuel Kala <emmanuel(at)ushahidi.com>
 * @copyright   (c) 2008-2011 Ushahidi Inc
 * @license     For license information, see LICENSE file
 */
class Swiftriver_Dropletqueue_Test extends PHPUnit_Extensions_Story_TestCase {
	
	/**
	 * Generates and returns a sample droplet - subtitute for dataProvider
	 * @return array
	 */
	private function get_sample_droplet()
	{
		return array(
			'channel' => 'twitter',
			'channel_filter_id' => '1',
			'identity_orig_id' => '193958229',
			'identity_username' => 'twitteruser',
			'identity_name' => 'Twitter User',
			'droplet_orig_id' => '2124569790',
			'droplet_type' => 'tweet',
			'droplet_title' => '',
			'droplet_content' => "Professor Wangari Maathai leads the votes for the 'Forbes Africa, Person of the Year Awards 2011'. Vote now - bit.ly/stzTju",
			'droplet_locale' => 'en',
			'droplet_date_pub' => '2011-10-15 22:15:10',
		);
	}
	
	/**
	 * Scenario for adding a droplet to the queue, processing it and fetching it
	 * from the "processed queue"
	 * @scenario
	 */
	public function queue_droplet()
	{
		$this->given('init_queue')
			 ->when('add_droplet', $this->get_sample_droplet()) // Call Dropletqueue::add()
			 ->then('process_queue', 'rss');	// Call Dropletqueue::process
	}
	
	public function runGiven(&$world, $action, $arguments)
	{
		switch ($action)
		{
			case 'init_queue': {
				$world['dropletqueue'] = 'Swiftriver_Dropletqueue';
				$world['queue_length'] = 0;
			}
			break;
			
			default: {
				return $this->notImplemented($action);
			}
		}
	}
	
	public function runWhen(&$world, $action, $arguments)
	{
		switch ($action)
		{
			case 'add_droplet':
			{
				$world['dropletqueue']::add($arguments[0], FALSE);
				$world['queue_length']++;
				
				// Check if the 'id' array key exists
				$this->assertArrayHasKey('id', $arguments[0]);
			}
			break;
			
			default:
			{
				return $this->notImplemented($action);
			}
		}
	}
	
	public function runThen(&$world, $action, $arguments)
	{
		switch($action)
		{
			case 'process_queue':
			{
				$world['dropletqueue']::process($arguments[0]);
				
				// Verify that the "procesed" queue is not empty
				$processed = $world['dropletqueue']::get_processed_droplets();
				$this->assertTrue(!empty($processed), 'No droplets processed');
				
				// Cleanup
				foreach ($processed as $test_droplet)
				{
					// Delete the test droplet(s) from the database
					ORM::factory('Droplet', $test_droplet['id'])->delete();
				}
				unset ($processed, $empty_queue);
			}
			break;
			
			default:
			{
				return $this->notImplemented($action);
			}
		}
	}
}
?>