<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account_Channel_Quota tests
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
class Model_Account_Channel_QuotaTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('plugins', 'channel_quotas', 'account_channel_quotas');
	
	/**
	* Provides test data for test_validation()
	*/
	public function provider_validation()
	{
		 return array(
			 // Empty channel
			 array(NULL, 'channel_option'),
			 // Empty channel option
			 array('channel', NULL),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_validation
	*/
	public function test_validation($channel, $channel_option)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$quota = ORM::factory("Account_Channel_Quota");
		$quota->channel = $channel;
		$quota->channel_option = $channel_option;
		$quota->save();
	}
	
	/**
	* Provides test data for test_create_new()
	*/
	public function provider_create_new()
	{
		return array(
			// Use default quota
			array(3, 'twitter', 'user', NULL, 
				array(
					array(
						'account_id' => 3,
						'channel' => 'twitter',
						'channel_option' => 'user',
						'quota' => '999',
						'quota_used' => 0
					)
				)
			),
			// Use specific quota
			array(3, 'twitter', 'user', 143, 
				array(
					array(
						'account_id' => 3,
						'channel' => 'twitter',
						'channel_option' => 'user',
						'quota' => '143',
						'quota_used' => 0
					)
				)
			),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_create_new
 	*/
	public function test_create_new($account_id, $channel, $option, $quota, $expected)
	{
		Model_Account_Channel_Quota::create_new($account_id, $channel, $option, $quota);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `account_id`, `channel`, `channel_option`, `quota`, `quota_used` ".
			"FROM `account_channel_quotas` ".
			"WHERE `account_id` = $account_id ".
			"AND `channel` = 'twitter' ".
			"AND `channel_option` = 'user'; "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected, $results);
	}
	
	/**
	* Provides test data for test_get_remaining_quota()
	*/
	public function provider_get_remaining_quota()
	{
		return array(
			// Existing quota
			array(3, 'rss', 'url', 130),
			// No quota, return default
			array(3, 'twitter', 'user', 999),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_remaining_quota
	*/
	public function test_get_remaining_quota($account_id, $channel, $option, $expected)
	{
		$this->assertEquals(
			$expected, 
			Model_Account_Channel_Quota::get_remaining_quota($account_id, $channel, $option)
		);
	}
	
	/**
	* Provides test data for test_decrease_quota_usage()
	*/
	public function provider_decrease_quota_usage()
	{
		return array(
			// Existing quota
			array(3, 'rss', 'url', 10, 3),
			// Non existing quota
			array(3, 'twitter', 'user', 10, 0),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_decrease_quota_usage
	*/
	public function test_decrease_quota_usage($account_id, $channel, $option, $usage, $expected)
	{
		Model_Account_Channel_Quota::decrease_quota_usage(
			$account_id, 
			$channel, 
			$option, 
			$usage
		);
		
		$result = DB::query(
			Database::SELECT, 
			"SELECT `quota_used` ".
			"FROM `account_channel_quotas` ".
			"WHERE `channel` = '$channel' ".
			"AND `channel_option` = '$option' ".
			"AND `account_id` = '$account_id';"
		)->execute()->get("quota_used", 0);
				
		$this->assertEquals(
			$expected, 
			intval($result)
		);
	}
	
	/**
	* Provides test data for test_increase_quota_usage()
	*/
	public function provider_increase_quota_usage()
	{
		return array(
			// Existing quota
			array(3, 'rss', 'url', 10, 23),
			// Non existing quota
			array(3, 'twitter', 'user', 10, 10),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_increase_quota_usage
	*/
	public function test_increase_quota_usage($account_id, $channel, $option, $usage, $expected)
	{
		Model_Account_Channel_Quota::increase_quota_usage(
			$account_id, 
			$channel, 
			$option, 
			$usage
		);
		
		$result = DB::query(
			Database::SELECT, 
			"SELECT `quota_used` ".
			"FROM `account_channel_quotas` ".
			"WHERE `channel` = '$channel' ".
			"AND `channel_option` = '$option' ".
			"AND `account_id` = '$account_id';"
		)->execute()->get("quota_used", 0);
				
		$this->assertEquals(
			$expected, 
			intval($result)
		);
	}
 }

