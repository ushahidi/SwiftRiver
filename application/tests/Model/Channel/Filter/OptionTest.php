<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Channel_Filter_Option tests.
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
class Model_Channel_Filter_OptionTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('rivers', 'accounts', 'account_channel_quotas', 'channel_filters');
	
	
	/**
	* Provides test data for test_get_quota_usage()
	*/
	public function provider_get_quota_usage()
	{
		 return array(
			 array(12, 2),
			 array(2, 1),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_get_quota_usage
	*/
	public function test_get_quota_usage($option_id, $expected)
	{
		$option = ORM::factory("Channel_Filter_Option", $option_id);
		
		$this->assertEquals($expected, $option->get_quota_usage());
	}
	
	/**
	* Provides test data for test_save()
	*/
	public function provider_save()
	{
		 return array(
			 // Within quota
			 array(
				 1, 
				 'url', 
				 '{"value":"http:\/\/ushahidi.com\/rss.xml","title":"Ushahidi","quota_usage":10}', 
				 120, 
				 NULL
			 ),
			 // Beyond quota
			  array(
				  1, 
				  'url', 
				  '{"value":"http:\/\/ushahidi.com\/rss.xml","title":"Ushahidi","quota_usage":100000000}', 
				  NULL, 
				  'Swiftriver_Exception_Channel_Option'
			  ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_save
	*/
	public function test_save($filter_id, $key, $value, $expected_quota_usage, $expected_exception)
	{
		if (isset($expected_exception))
		{
			$this->setExpectedException($expected_exception);
		}
		
		$option = ORM::factory("Channel_Filter_Option");
		$option->channel_filter_id = $filter_id;
		$option->key = $key;
		$option->value = $value;
		$option->save();
		
		if ( ! isset($expected_exception))
		{
			$account_id = $option->channel_filter->river->account->id;
			$channel = $option->channel_filter->channel;
			$this->assertEquals(
				$expected_quota_usage, 
				Model_Account_Channel_Quota::get_remaining_quota($account_id, $channel, $key)
			);
		}
	}
	
	/**
	* @test
	*/
	public function test_delete()
	{
		$option = ORM::factory("Channel_Filter_Option", 1);
		$account_id = $option->channel_filter->river->account->id;
		$channel = $option->channel_filter->channel;
		$key = $option->key;
		$option->delete();
		
		// Check quota update on delete
		$this->assertEquals(
			131, 
			Model_Account_Channel_Quota::get_remaining_quota($account_id, $channel, $key)
		);
	}
}