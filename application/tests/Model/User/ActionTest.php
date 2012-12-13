<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_User_Action tests
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
class Model_User_ActionTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('settings', 'accounts', 'users', 'user_actions');
	
	/**
	* Provides test data for test_get_activity_stream()
	*/
	public function provider_get_activity_stream()
	{
		 return array(
			// User6 requesting User3's activity stream. Does not follow user3
			array(3, 6, TRUE, NULL, NULL, 10, 
				array(
					array(
					  'id' => '8',
					  'action_date_add' => 'Nov 19, 2012 00:00:07 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '7',
					  'action_date_add' => 'Nov 19, 2012 00:00:06 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '4',
					  'confirmed' => true,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user2 name',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '6',
					  'action_date_add' => 'Nov 19, 2012 00:00:05 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
					array(
					  'id' => '5',
					  'action_date_add' => 'Nov 19, 2012 00:00:04 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '4',
					  'confirmed' => true,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user2 name',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
					array(
					  'id' => '3',
					  'action_date_add' => 'Nov 19, 2012 00:00:02 UTC',
					  'user_id' => '3',
					  'action' => 'create',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '0',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '1',
					  'action_date_add' => 'Nov 19, 2012 00:00:00 UTC',
					  'user_id' => '3',
					  'action' => 'create',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '0',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
				)
			),
			// User4(follows user3) requesting their activity stream
			array(4, 4, FALSE, NULL, NULL, 10, 
				array(
					array(
					  'id' => '8',
					  'action_date_add' => 'Nov 19, 2012 00:00:07 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '7',
					  'action_date_add' => 'Nov 19, 2012 00:00:06 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '4',
					  'confirmed' => true,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user2 name',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '6',
					  'action_date_add' => 'Nov 19, 2012 00:00:05 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
					array(
					  'id' => '5',
					  'action_date_add' => 'Nov 19, 2012 00:00:04 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '4',
					  'confirmed' => true,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user2 name',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
					array(
					  'id' => '3',
					  'action_date_add' => 'Nov 19, 2012 00:00:02 UTC',
					  'user_id' => '3',
					  'action' => 'create',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '0',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
					array(
					  'id' => '1',
					  'action_date_add' => 'Nov 19, 2012 00:00:00 UTC',
					  'user_id' => '3',
					  'action' => 'create',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '0',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
				)
			),
			// User7 requesting their empty activity stream
			array(7, 7, FALSE, NULL, NULL, 10, array()),
			// With last ID set
			array(4, 4, FALSE, 7, NULL, 1, 
				array(
					array(
					  'id' => '6',
					  'action_date_add' => 'Nov 19, 2012 00:00:05 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'river',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing River 1',
					  'action_on_url' => '/user1/river/testing-river-1',
					),
				),
			),
			// With since ID set
			array(4, 4, FALSE, NULL, 7, 1, 
				array(
					array(
					  'id' => '8',
					  'action_date_add' => 'Nov 19, 2012 00:00:07 UTC',
					  'user_id' => '3',
					  'action' => 'invite',
					  'action_on' => 'bucket',
					  'action_on_id' => '1',
					  'action_to_id' => '5',
					  'confirmed' => false,
					  'avatar' => 'https://secure.gravatar.com/avatar/111d68d06e2d317b5a59c2c6c5bad808?s=80&d=mm&r=g',
					  'user_url' => '/user1',
					  'user_name' => 'user1 name',
					  'username' => 'user1 username',
					  'email' => 'user1@example.com',
					  'action_to_name' => 'user3 name',
					  'action_on_name' => 'Testing Bucket 1',
					  'action_on_url' => '/user1/bucket/testing-bucket-1',
					),
				),
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_activity_stream
	*/
	public function test_get_activity_stream($user_id, $visitor_id, $self, $last_id, $since_id, $limit, $expected)
	{
		$activities = Model_User_Action::get_activity_stream(
			$user_id, $visitor_id, $self, 
			$last_id, $since_id, $limit
		);
		
		$this->assertEquals($expected, $activities);
	}
	
	/**
	* @test
	*/
	public function test_create_action()
	{
		$comment = Model_User_Action::create_action(3, 'river', 'invite', 1, 7);
		
		$expected = array(
			array(
				'user_id' => 3,
				'action' => 'invite',
				'action_on' => 'river',
				'action_on_id' => 1,
				'action_to_id' => 7,
				'confirmed' => 0
			)
		);
		$results = DB::query(
			Database::SELECT, 
			"SELECT `user_id`, `action`, `action_on`, `action_on_id`, `action_to_id`, `confirmed` ".
			"FROM `user_actions` ".
			"WHERE `user_id` = 3 ".
			"AND action_to_id = 7 "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected, $results);
	}
	
	/**
	* @test
	*/
	public function test_delete_invite()
	{
		Model_User_Action::delete_invite(3, 'river', 1, 7);
		
		$action = Model::factory('user_action')
		    ->where('action', '=', 'invite')
		    ->where('user_id', '=', 3)
		    ->where('action_on', '=', 'river')
		    ->where('action_on_id', '=', 1)
		    ->where('action_to_id', '=', 7)
		    ->where('confirmed', '=', 0)
		    ->find();
		
		$this->assertFalse($action->loaded());
	}
	
	/**
	* Provides test data for test_count_notifications()
	*/
	public function provider_count_notifications()
	{
		return array(
			// User with unconfirmed invites
			array(5, 2),
			// User with confirmed invites
			array(4, 0)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_count_notifications
	*/
	public function test_count_notifications($user_id, $expected)
	{
		$this->assertEquals(
			$expected, 
			Model_User_Action::count_notifications($user_id)
		);
	}
 }