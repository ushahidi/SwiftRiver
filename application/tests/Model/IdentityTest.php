<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Identity tests
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
class Model_IdentityTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'identities');
	
	/**
	* Provides test data for test_get_ids()
	*/
	public function provider_get_ids()
	{
		 return array(
			 // Get one ID
			 array(1, 99, 100),
			 // Get a range of 10 IDs
			 array(10, 99, 109),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_ids
 	*/
	public function test_get_ids($num, $expected_start, $expected_next)
	{
		$this->assertEquals($expected_start, Model_Identity::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'identities'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* Provides test data for test_get_identities()
	*/
	public function provider_get_identities()
	{
		 return array(
			 // No identities array in drop
			 array(array(), array()),
			 //// Identities array in drop for existing identity
			 array(
			 	 array(
			 		array(
						'channel' => 'twitter',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar',
					)
			 	), 
			 	 array(
			 		array(
						'identity_id' => '1',
						'channel' => 'twitter',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar',
					)
			 	)
			 ),
			 // Identities array for a new identity
			 array(
			 	 array(
			 		array(
						'channel' => 'facebook',
						'identity_orig_id' => '3',
						'identity_name' => 'identity3_name',
						'identity_username' => 'identity3',
						'identity_avatar' => 'identity3_avatar',
					)
			 	), 
			 	 array(
			 		array(
						'identity_id' => '99',
						'channel' => 'facebook',
						'identity_orig_id' => '3',
						'identity_name' => 'identity3_name',
						'identity_username' => 'identity3',
						'identity_avatar' => 'identity3_avatar',
					)
			 	)
			 ),
			 // Identities array with existing and new identity
			 array(
			 	 array(
			 		array(
						'channel' => 'twitter',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar',
					),
			 		array(
						'channel' => 'sms',
						'identity_orig_id' => '4',
						'identity_name' => 'identity4_name',
						'identity_username' => 'identity4',
						'identity_avatar' => 'identity4_avatar',
					)
			 	), 
			 	 array(
			 		array(
						'identity_id' => '1',
						'channel' => 'twitter',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar',
					),
			 		array(
						'identity_id' => '99',
						'channel' => 'sms',
						'identity_orig_id' => '4',
						'identity_name' => 'identity4_name',
						'identity_username' => 'identity4',
						'identity_avatar' => 'identity4_avatar',
					)
			 	)
			 ),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_identities
 	*/
	public function test_get_identities($drop, $expected)
	{
		Model_Identity::get_identities($drop);
		$this->assertEquals($expected, $drop);
	}
 }
