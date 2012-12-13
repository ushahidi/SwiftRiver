<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Setting tests.
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
class Model_SettingTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('settings');
	
	/**
	* Provides test data for test_get_setting()
	*/
	public function provider_get_setting()
	{
		 return array(
			 array("site_name", "SwiftRiver"),
			 array("non existent key", NULL)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_setting
	*/
	public function test_get_setting($setting, $expected)
	{
		$this->assertEquals($expected, Model_Setting::get_setting($setting));
	}
	
	/**
	* Provides test data for test_get_settings()
	*/
	public function provider_get_settings()
	{
		 return array(
			 array(
				 array(
					 "site_name", 
					 "site_theme", 
					 "site_locale", 
					 "non_existent"
				 ), 
				 array(
				 	'site_name' => 'SwiftRiver',
				 	'site_theme' => 'default',
				 	'site_locale' => 'en',
				 ),
			 ),
			 // Empty request
			 array(array(), NULL),
			 // Non array argument
			 array("STRING", NULL)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_settings
	*/
	public function test_get_settings($settings, $expected)
	{
		$this->assertEquals($expected, Model_Setting::get_settings($settings));
	}
	
	/**
	* Provides test data for test_update_setting()
	*/
	public function provider_update_setting()
	{
		 return array(
			 array("site_name", "SwiftLiver"),
			 array("non existent key", "now exists")
		);
	}
	
	/**
	* @test
	* @dataProvider provider_update_setting
	*/
	public function test_update_setting($setting, $value)
	{
		Model_Setting::update_setting($setting, $value);
		$this->assertEquals($value, Model_Setting::get_setting($setting));
	}
	
	/**
	* Provides test data for test_update_settings()
	*/
	public function provider_update_settings()
	{
		 return array(
			 array(
				 array(
				 	'site_name' => 'SwifterLiver',
				 	'site_theme' => 'default',
				 	'site_locale' => 'fr',
				 ),
			 )
		);
	}
	
	/**
	* @test
	* @dataProvider provider_update_settings
	*/
	public function test_update_settings($values)
	{
		Model_Setting::update_settings($values);
		$this->assertEquals($values, Model_Setting::get_settings(array_keys($values)));
		
	}
}