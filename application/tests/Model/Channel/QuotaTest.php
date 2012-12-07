<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Channel_Quota tests
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
class Model_Channel_QuotaTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('plugins', 'channel_quotas');
	
	/**
	* @test
	*/
	public function test_get_quotas_array()
	{
		$expected = array(
			array(
				'channel_name' => 'Twitter',
				'quota_options' => 
				array(
					array(
						'id' => NULL,
						'channel' => 'twitter',
						'label' => 'Keyword',
						'channel_option' => 'keyword',
						'quota' => 999,
					),
					array(
						'id' => NULL,
						'channel' => 'twitter',
						'label' => 'User',
						'channel_option' => 'user',
						'quota' => 999,
					),
				),
			),
			array(
				'channel_name' => 'RSS',
				'quota_options' => 
				array(
					array(
						'id' => '1',
						'channel' => 'rss',
						'label' => 'Feed URL',
						'channel_option' => 'url',
						'quota' => '949',
					),
				),
			),
		);
		
		$this->assertEquals($expected, Model_Channel_Quota::get_quotas_array());
	}
	
	/**
	* Provides test data for test_get_channel_quota()
	*/
	public function provider_get_channel_quota()
	{
		 return array(
			 // Customized quota setting
			 array('rss', 'url', 949),
			 // Default quota setting
			 array('twitter', 'user', 999),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_channel_quota
 	*/
	public function test_get_channel_quota($channel, $option, $expected)
	{
		$this->assertEquals(
			$expected, 
			Model_Channel_Quota::get_channel_quota($channel, $option)
		);
	}
	
	/**
	* @test
	*/
	public function test_add_quota()
	{
		$quota = array(
			array(
				'channel' => 'facebook',
				'channel_option' => 'page',
				'quota' => '1093'
			)
		);
		
		Model_Channel_Quota::add_quota($quota[0]);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `channel`, `channel_option`, `quota` ".
			"FROM `channel_quotas` ".
			"WHERE `channel` = 'facebook' ".
			"AND `channel_option` = 'page';"
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($quota, $results);
		
	}
 }
