<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account tests
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
class Model_AccountTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('users', 'accounts', 'settings');
	
	/**
	* Provides test data for test_save_invalid()
	*/
	public function provider_invalid_account_path()
	{
		 return array(
			 array(NULL),
			 array("!!invalid")
		);
	}
	
	/**
	* @test
	* @dataProvider provider_invalid_account_path
	*/
	public function test_save_invalid($account_path)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$account = ORM::factory("Account");
		$account->account_path = $account_path;
		$account->save();
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$account = ORM::factory("Account");
		$account->account_path = "valid_account_path";
		$account->save();
		
		$this->assertTrue($account->loaded());
		$this->assertEquals(
			Model_Setting::get_setting('default_river_quota'),
			$account->river_quota_remaining
		);
	}
	
	/**
	* Provides test data for test_account_path_available()
	*/ 
	public function provider_account_path()
	{
		 return array(
			 // Existing user
			 array('user1', FALSE),
			 // Imaginary user
			 array('non_existent', TRUE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_account_path
	*/
 	public function test_account_path_available($account_path, $expected)
 	{
		$this->assertEquals(
			$expected, 
			Model_Account::account_path_available($account_path)
		);
 	}
	
	/**
	* @test
	*/
	public function test_get_remaining_river_quota()
	{
		$account = ORM::factory("Account", 3);
		
		$this->assertEquals(93, $account->get_remaining_river_quota());
	}
	
	/**
	* Provides test data for test_increase_river_quota()
	*/
	public function provider_increase_river_quota()
	{
		 return array(
			 // Increment without save
			 array(2, FALSE, 93),
			 // Increment with save
			 array(2, TRUE, 95),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_increase_river_quota
 	*/
	public function test_increase_river_quota($increment, $save, $expected)
	{
		$account = ORM::factory("Account", 3);
		$account->increase_river_quota($increment, $save);
		
		$account = ORM::factory("Account", 3);
		$this->assertEquals($expected, $account->river_quota_remaining);
	}
	
	/**
	* Provides test data for test_decrease_river_quota()
	*/
	public function provider_decrease_river_quota()
	{
		 return array(
			 // Increment without save
			 array(2, FALSE, 93),
			 // Increment with save
			 array(2, TRUE, 91),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_decrease_river_quota
	*/
	public function test_decrease_river_quota($decrement, $save, $expected)
	{
		$account = ORM::factory("Account", 3);
		$account->decrease_river_quota($decrement, $save);
		
		$account = ORM::factory("Account", 3);
		$this->assertEquals($expected, $account->river_quota_remaining);
	}
 }