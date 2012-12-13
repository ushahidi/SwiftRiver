<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Channel_Filter tests.
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
class Model_Channel_FilterTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('rivers', 'channel_filters', 'account_channel_quotas');
	
	/**
	* Provides test data for test_save()
	*/
	public function provider_save()
	{
		 return array(
			 // Disable and active channel
			 array(1, FALSE, 'swiftriver.channel.disable'),
			 // Enable an inactive channel
			  array(2, TRUE, 'swiftriver.channel.enable'),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_save
	*/
	public function test_save($filter_id, $filter_enabled, $event)
	{
		$channel = ORM::factory("Channel_Filter", $filter_id);
		$channel->filter_enabled = $filter_enabled;
		$channel->save();
		
		$this->assertTrue(Swiftriver_Event::has_run($event));
	}
	
	/*
	* @test
	*/
	public function test_delete()
	{
		$channel = ORM::factory('Channel_Filter', 1);
		$channel->delete();
		
		// Check quota update on delete
		$this->assertEquals(
			133, 
			Model_Account_Channel_Quota::get_remaining_quota(3, 'rss', 'url')
		);
	}
	
	/**
	* Provides test data for test_get_channel_filters()
	*/
	public function provider_get_channel_filters()
	{
		 return array(
			 // Existing channel filter
			 array('rss', 1, TRUE),
			 // Non existent channel filter
			 array('facebook', 1, FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_channel_filters
	*/
	public function test_get_channel_filters($channel, $river_id, $expected)
	{
		$channel = Model_Channel_Filter::get_channel_filters($channel, $river_id);
		
		$this->assertEquals($expected, $channel->loaded());
	}
	
	/**
	* Provides test data for test_get_channel_filter_options()
	*/
	public function provider_get_channel_filter_options()
	{
		 return array(
			 // Existing channel filter
			 array('rss', 1, 
			 	array(
					array(
						'id' => '1',
						'key' => 'url',
						'data' => array(
							'value' => 'http://feeds.bbci.co.uk/news/rss.xml',
							'title' => 'BBC News - Home',
							'quota_usage' => 1,
						),
					),
					array(
						'id' => '2',
						'key' => 'url',
						'data' => array(
							'value' => 'http://feeds.feedburner.com/ommalik',
							'title' => 'GigaOM',
							'quota_usage' => 1,
						),
					),
					array(
						'id' => '3',
						'key' => 'url',
						'data' => array(
							'value' => 'http://www.engadget.com/rss.xml',
							'title' => 'Engadget',
							'quota_usage' => 1,
						),
					),
				),
			),
			// Channel without options
			array('sms', 1, array()),
			// Non existent channel
			array('facebook', 1, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_channel_filter_options
	*/
	public function test_get_channel_filter_options($channel, $river_id, $expected)
	{
		$options = Model_Channel_Filter::get_channel_filter_options($channel, $river_id);
		
		$this->assertEquals($expected, $options);
	}
	
	/**
	* Provides test data for test_get_channel_filters_by_run_date()
	*/
	public function provider_get_channel_filters_by_run_date()
	{
		 return array(
			 // Null since date
			array(NULL, 
				array(
					array(
						'river_id' => '1',
						'channel' => 'rss',
					),
					array(
						'river_id' => '1',
						'channel' => 'sms',
					),
				),
			),
			// Specific since date
			array('2012-11-15 00:00:02', 
				array(
					array(
						'river_id' => '1',
						'channel' => 'rss',
					),
				),
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_channel_filters_by_run_date
	*/
	public function test_get_channel_filters_by_run_date($since_date, $expected)
	{
		$channels = Model_Channel_Filter::get_channel_filters_by_run_date($since_date);
		
		$this->assertEquals($expected, $channels);
	}
	
	/**
	* Provides test data for test_update_runs()
	*/
	public function provider_update_runs()
	{
		return array(
			// Successful run
			array(1, 'rss', TRUE, '2012-12-10 10:07:43', 
				array(
					array(
						'filter_last_run' => '2012-12-10 10:07:43',
						'filter_last_successful_run' => '2012-12-10 10:07:43',
						'filter_runs' => '1',
					),
			 	),
			),
			// Unsuccessful run
			array(1, 'rss', FALSE, '2012-12-10 10:07:43', 
				array(
					array(
						'filter_last_run' => '2012-12-10 10:07:43',
						'filter_last_successful_run' => '2012-11-14 00:00:00',
						'filter_runs' => '1',
					),
				),
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_update_runs
	*/
	public function test_update_runs($river_id, $channel, $success, $run_date, $expected)
	{
		Model_Channel_Filter::update_runs($river_id, $channel, $success, $run_date);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `filter_last_run`, `filter_last_successful_run`, `filter_runs` ".
			"FROM `channel_filters` ".
			"WHERE `river_id` = $river_id ".
			"AND `channel` = '$channel' "
		)->execute()->as_array();
		
		$this->assertEquals($expected, $results);
	}
	
	/**
	* Provides test data for test_update_option()
	*/
	public function provider_update_option()
	{
		return array(
			// Existing option
			array(
				1,
				array(
					'key' => 'url',
					'value' => 'http://ushahidi.com/rss.xml',
					'title' => 'Ushahidi',
					'quota_usage' => 1
				), 
				1, 
				array(
					'key' => 'url',
					'value' => '{"value":"http:\\/\\/ushahidi.com\\/rss.xml","title":"Ushahidi","quota_usage":1}'
				),
				'swiftriver.channel.option.pre_delete' 
			),
			// New option
			array(
				1,
				array(
					'key' => 'url',
					'value' => 'http://ushahidi.com/rss.xml',
					'title' => 'Ushahidi',
					'quota_usage' => 1
				), 
				0, 
				array(
					'key' => 'url',
					'value' => '{"value":"http:\\/\\/ushahidi.com\\/rss.xml","title":"Ushahidi","quota_usage":1}'
				),
				NULL
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_update_option
	*/
	public function test_update_option($channel_id, $value, $option_id, $expected, $event)
	{
		$channel = ORM::factory("Channel_Filter", $channel_id);
		$channel->update_option($value, $option_id);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `key`, `value` ".
			"FROM `channel_filter_options` ".
			"WHERE `channel_filter_id` = $channel_id ".
			"AND `key` = '".$value['key']."' "
		)->execute()->as_array();
		
		$this->assertContains($expected, $results);
		
		if (isset($event))
		{
			$this->assertTrue(SwiftRiver_Event::has_run($event));
		}
	}
	
	/**
	* Provides test data for test_delete_option()
	*/
	public function provider_delete_option()
	{
		 return array(
			// Existing option
			array(1, 1, FALSE),
			// Non existent option
			array(1, 999, FALSE),
			// Option in another channel
			array(1, 4, TRUE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_delete_option
	*/
	public function test_delete_option($channel_id, $option_id, $expected)
	{
		$channel = ORM::factory("Channel_Filter", $channel_id);
		$channel->delete_option($option_id);
		
		$option = ORM::factory('Channel_Filter_Option', $option_id);
		$this->assertEquals($expected, $option->loaded());
	}
	
	/**
	* Provides test data for test_get_quota_usage()
	*/
	public function provider_get_quota_usage()
	{
		return array(
			// Channel with rss
			array(1, 
				array(
					'url' => 3
				)
			),
			// Channel with twitter
			array(2,
				array(
					'keyword' => 2,
					'user' => 1
				)
			),
			// Channelw with no quota usage
			array(3, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_quota_usage
	*/
	public function test_get_quota_usage($channel_id, $expected)
	{
		$channel = ORM::factory("Channel_Filter", $channel_id);
		
		$this->assertEquals($expected, $channel->get_quota_usage());
	}
}