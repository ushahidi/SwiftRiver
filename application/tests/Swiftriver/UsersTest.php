<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Swiftriver_Users tests
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
class Swiftriver_UsersTest extends Unittest_TestCase {

	/**
	* Provides test data for test_gravatar()
	*/
	public function provider_gravatar()
	{
		 return array(
			 array('test@example.com', 80, 'mm', 'g', FALSE, array(), '55502f40dc8b7c769880b10874abc9d0?s=80&d=mm&r=g'),
			 array('test2@example.com', 40, '404', 'x', FALSE, array(), '43b05f394d5611c54a1a9e8e20baee21?s=40&d=404&r=x'),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_gravatar
	*/
	public function test_gravatar($email, $s, $d, $r, $img, $atts, $expected)
	{
		$url = Swiftriver_Users::gravatar($email, $s, $d, $r, $img, $atts);
		$parts = explode('/', $url);
		
		$this->assertEquals($expected, array_pop($parts));
	}
 }

