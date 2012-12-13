<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Auth_Token tests
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
class Model_Auth_TokenTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array("auth_tokens");
	
	/**
	* @test
	*/
	public function test_create_token()
	{
		$token = Model_Auth_Token::create_token("test_token", "test_token_data", 3640);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `token`, `type`, `data`, `created_date`, `expire_date` ".
			"FROM `auth_tokens` ".
			"WHERE `token` = '".$token->token."' "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		
		$result = array_pop($results);
		$this->assertEquals('test_token', $result['type']);
		$this->assertEquals(
			'test_token_data', 
			json_decode($result['data'])
		);
		$this->assertEquals(
			3640, 
			strtotime($result['expire_date']) - strtotime($result['created_date'])
		);
	}
	
	/**
	* Provides test data for test_get_token()
	*/
	public function provider_get_token()
	{
		return array(
			// Existing valid token
			array('2b96dde1c2ab0860d82a6c9266f7a940', 'new_registration', 1),
			array('', '', NULL),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_token
 	*/
	public function test_get_token($token, $type, $expected_id)
	{
		if (isset($expected_id))
		{
			$expected = ORM::factory("Auth_Token", $expected_id);
			$this->assertEquals(
				$expected, 
				Model_Auth_Token::get_token($token, $type)
			);
		}
		else
		{
			$this->assertFalse(Model_Auth_Token::get_token($token, $type));
		}
	}
 }