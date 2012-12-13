<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_User tests.
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
class Model_UserTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('users', 'roles', 'rivers', 'buckets');
	
	
	/**
	* Provides test data for testSaveInvalid()
	*/
	public function provider_validation()
	{
		 return array(
			 // Empty username
			 array(NULL, 'user@example.com'),
			 // Long username
			 array(str_repeat("a", 256), 'user@example.com'),
			 // Empty email
			 array('test user', NULL),
			 // Invalid email
			 array('test user', 'not an email'),
			 // Duplicate email
			 array('test user', 'user1@example.com'),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_validation
	*/
	public function test_validation($username, $email)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$user = ORM::factory("User");
		$user->username = $username;
		$user->email = $email;
		$user->save();
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$user = ORM::factory("User");
		$user->username = 'test user';
		$user->email = 'tester@example.com';
		$user->save();
		
		$this->assertTrue($user->loaded());
		$this->assertTrue(isset($user->api_key));
	}
	
	/**
	* Provides test data for test_get_like()
	*/
	public function provider_get_like()
	{
		return array(
			// Public user
			array('public', array(), array()),
			// Non existent user
			array('xqwjkz', array(), array()),
			// Existing user by username
			array('user1', array(), 
				array(
					array(
						'id' => 3,
						'name' => 'user1 name',
						'account_path' => 'user1',
						'avatar' => Swiftriver_Users::gravatar('user1@example.com', 40)
					),
				)
			),
			// Existing user by email
			array('user2@ex', array(), 
				array(
					array(
						'id' => 4,
						'name' => 'user2 name',
						'account_path' => 'user2',
						'avatar' => Swiftriver_Users::gravatar('user2@example.com', 40)
					),
				)
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_like
	*/
	public function test_get_like($search_term, $exclude_ids, $expected)
	{
		$this->assertEquals(
			$expected, 
			Model_User::get_like(
				$search_term, 
				$exclude_ids
			)
		);
	}
	
	/**
	* Provides test data for test_get_rivers()
	*/
	public function provider_get_rivers()
	{
		return array(
			// User with 2 rivers, no collaborations, no subscriptions
			array(3, array(1, 2)),
			// User owns river_id 3,4 and collaborating on river_id 1
			array(4, array(3, 4, 1)),
			// User following river_id 1 and owns no rivers
			array(7, array(1)),
			// User with no rivers, and an inactive collaboration
			array(5, array()),
			// User with no rivers
			array(1, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_rivers
	*/
	public function test_get_rivers($user_id, $expected_river_ids)
	{
		$user = ORM::factory("User", $user_id);
		
		$expected = array();
		foreach ($expected_river_ids as $river_id)
		{
			$expected[] = ORM::factory("River", $river_id);
		}
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_rivers());
	}
	
	/**
	* Provides test data for test_get_rivers_array()
	*/
	public function provider_get_rivers_array()
	{
		return array(
			// User_id 3 requesting own river
			array(3, 3, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing River 1',
					 	"type" => 'river',
					 	"url" => '/user1/river/testing-river-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => FALSE,
					 	"public" => TRUE
					),
					array(
						"id" => 2, 
						"name" => 'Testing River 2',
					 	"type" => 'river',
					 	"url" => '/user1/river/testing-river-2',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 0,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => FALSE,
					 	"public" => FALSE
					)
				)
			),
			// User 4 requesting User3's rivers. Collaborates on one, the other is private
			array(3, 4, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing River 1',
					 	"type" => 'river',
					 	"url" => '/user1/river/testing-river-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => TRUE,
					 	"subscribed" => TRUE,
					 	"public" => TRUE
					),
				)
			),
			// User 7 requesting User4's rivers, none of which are public
			array(4, 7, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing River 1',
					 	"type" => 'river',
					 	"url" => '/user1/river/testing-river-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => TRUE,
					 	"public" => TRUE
					),
				),
			), 
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_rivers_array
	*/
	public function test_get_rivers_array($user_id, $visitor_id, $expected)
	{
		$user = ORM::factory("User", $user_id);
		$visiting_user = ORM::factory("User", $visitor_id);
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_rivers_array($visiting_user));
	}
	
	/**
	* Provides test data for test_get_buckets()
	*/
	public function provider_get_buckets()
	{
		return array(
			// User with 2 buckets, no collaborations, no subscriptions
			array(3, array(1, 2)),
			// User owns bucket_id 3,4 and collaborating on bucket_id 1
			array(4, array(3, 4, 1)),
			// User following bucket_id 1 and owns no buckets
			array(7, array(1)),
			// User with no buckets, and an inactive collaboration
			array(5, array()),
			// User with no buckets
			array(1, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_buckets
	*/
	public function test_get_buckets($user_id, $expected_bucket_ids)
	{
		$user = ORM::factory("User", $user_id);
		
		$expected = array();
		foreach ($expected_bucket_ids as $bucket_id)
		{
			$expected[] = ORM::factory("Bucket", $bucket_id);
		}
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_buckets());
	}
	
	/**
	* Provides test data for test_get_buckets_array()
	*/
	public function provider_get_buckets_array()
	{
		return array(
			// User_id 3 requesting own bucket
			array(3, 3, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing Bucket 1',
					 	"type" => 'bucket',
					 	"url" => '/user1/bucket/testing-bucket-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => FALSE,
					 	"public" => TRUE
					),
					array(
						"id" => 2, 
						"name" => 'Testing Bucket 2',
					 	"type" => 'bucket',
					 	"url" => '/user1/bucket/testing-bucket-2',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 0,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => FALSE,
					 	"public" => FALSE
					)
				)
			),
			// User 4 requesting User3's buckets. Collaborates on one, the other is private
			array(3, 4, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing Bucket 1',
					 	"type" => 'bucket',
					 	"url" => '/user1/bucket/testing-bucket-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => TRUE,
					 	"subscribed" => TRUE,
					 	"public" => TRUE
					),
				)
			),
			// User 7 requesting User4's buckets, none of which are public
			array(4, 7, 
				array(
					array(
						"id" => 1, 
						"name" => 'Testing Bucket 1',
					 	"type" => 'bucket',
					 	"url" => '/user1/bucket/testing-bucket-1',
					 	"account_id" => 3,
					 	"user_id" => 3,
					 	"account_path" => 'user1',
					 	"subscriber_count" => 1,
					 	"is_owner" => TRUE,
					 	"collaborator" => FALSE,
					 	"subscribed" => TRUE,
					 	"public" => TRUE
					),
				),
			), 
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_buckets_array
	*/
	public function test_get_buckets_array($user_id, $visitor_id, $expected)
	{
		$user = ORM::factory("User", $user_id);
		$visiting_user = ORM::factory("User", $visitor_id);
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_buckets_array($visiting_user));
	}
	
	/**
	* Provides test data for test_get_followers()
	*/
	public function provider_get_followers()
	{
		return array(
			// User id 3 who has 2 followers
			array(3, 
				array(
					array(
						'id' => '4',
						'user_name' => 'user2 name',
						'account_path' => 'user2',
						'username' => 'user2 username',
					),
					array(
						'id' => '5',
						'user_name' => 'user3 name',
						'account_path' => 'user3',
						'username' => 'user3 username',
					),
				),
			),
			// User without followers
			array(4, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_followers
	*/
	public function test_get_followers($user_id, $expected)
	{
		$user = ORM::factory("User", $user_id);
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_followers());
	}
	
	/**
	* Provides test data for test_get_following()
	*/
	public function provider_get_following()
	{
		return array(
			// User id 4 who is following User3
			array(4, 
				array(
					array(
						'id' => '3',
						'user_name' => 'user1 name',
						'account_path' => 'user1',
						'username' => 'user1 username',
					),
				),
			),
			// User not following anyone
			array(3, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_following
	*/
	public function test_get_following($user_id, $expected)
	{
		$user = ORM::factory("User", $user_id);
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->get_following());
	}
	
	/**
	* Provides test data for test_is_admin()
	*/
	public function provider_is_admin()
	{
		return array(
			// Default admin user
			array(1, TRUE),
			// Ordinary user with admin role
			array(3, TRUE),
			// User without admin role
			array(4, FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_admin
	*/
	public function test_is_admin($user_id, $expected)
	{
		$user = ORM::factory("User", $user_id);
		
		$this->assertTrue($user->loaded());
		$this->assertEquals($expected, $user->is_admin());
	}
	
	/**
	* Provides test data for test_get_users()
	*/
	public function provider_get_users()
	{
		return array(
			// User with 2 buckets, no collaborations, no subscriptions
			array(array(3, 4, 5)),
			array(array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_users
	*/
	public function test_get_users($user_ids)
	{		
		$expected = array();
		foreach ($user_ids as $user_id)
		{
			$expected[] = ORM::factory("User", $user_id);
		}
		
		$this->assertEquals(
			$expected, 
			Model_User::get_users($user_ids)
		);
	}
	
	/**
	* Provides test data for test_get_user_by_email()
	*/
	public function provider_get_user_by_email()
	{
		return array(
			array('user1@example.com', TRUE),
			array('non_existent@example.co.ke', FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_user_by_email
	*/
	public function test_get_user_by_email($email, $expected)
	{
		$this->assertEquals(
			$expected, 
			Model_User::get_user_by_email($email)->loaded()
		);
	}
	
	/**
	* @test
	*/
	public function test_get_profile_url()
	{
		$user = ORM::factory('User', 3);
		$this->assertEquals('/user1', $user->get_profile_url());
	}
}