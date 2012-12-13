<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Swiftriver_Mutex tests
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @group      swiftriver
 * @group      swiftriver.core
 * @group      swiftriver.core.util
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_MutexTest extends Unittest_TestCase {
	
	public function tearDown()
	{
		// Always clean up by releasing mutexes, if any.
		Swiftriver_Mutex::release(get_class());
	}
	
	/**
	* @test
	*/
	public function test_obtain()
	{
		$this->setExpectedException('Swiftriver_Exception_Mutex');
		
		Swiftriver_Mutex::obtain(get_class());
		
		// Second call should fail given the mutex already exists
		Swiftriver_Mutex::obtain(get_class());
	}
	
	/**
	* @test
	* @depends test_obtain
	*/
	public function test_release()
	{
		// Acquire new mutex
		Swiftriver_Mutex::obtain(get_class());
		
		// Try to release it
		Swiftriver_Mutex::release(get_class());
		
		// If the above worked, below will go through
		Swiftriver_Mutex::obtain(get_class());
	}
	
 }

