<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Bucket tests.
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
class Model_BucketTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array(
		'users', 'accounts', 'drops', 'identities', 'buckets',
		'bucket_droplets'
	);
	 
	/**
	* Provides test data for testSaveInvalid()
	*/
	public function provider_invalid_buckets()
	{
		 return array(
			 // Empty bucket name
			 array(NULL, 0),
			 // Long bucket name
			 array(str_repeat("a", 256), 0),
			 // Invalida bucket publish
			 array('bucket', 2),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_invalid_buckets
	*/
	public function test_save_invalid($bucket_name, $bucket_publish)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$bucket = ORM::factory("Bucket");
		$bucket->bucket_name = $bucket_name;
		$bucket->bucket_publish = $bucket_publish;
		$bucket->save();
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$bucket = ORM::factory("Bucket");
		$bucket->bucket_name = "Bucket Name";
		$bucket->save();
		
		$this->assertTrue($bucket->loaded());
		$this->assertEquals(
			$bucket->bucket_name_url, 
			URL::title($bucket->bucket_name)
		);
		$this->assertTrue(isset($bucket->public_token));
	}
	
	
	/**
	* @test
	*/
	public function test_get_base_url()
	{
		$bucket = ORM::factory("Bucket", 1);
		
		$this->assertEquals(
			'/user1/bucket/testing-bucket-1', 
			$bucket->get_base_url()
		);
	}
	
	/**
	* Provides test data for test_get_array()
	*/
	public function provider_get_array()
	{
		 return array(
			// Onwer user_id 3 requesting own bucket
			array(
				1, 
				3, 
				3,
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
				)
			),
			// Collaborator user_id 4 requesting public bucket owned by user_id 3
			array(
				1, 
				4, 
				4,
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
				)
			),
			// Subscriber user_id 7 requesting public bucket owned by user_id 3
			array(
				1, 
				7, 
				7,
				array(
					"id" => 1, 
					"name" => 'Testing Bucket 1',
				 	"type" => 'bucket',
				 	"url" => '/user1/bucket/testing-bucket-1',
				 	"account_id" => 3,
				 	"user_id" => 3,
				 	"account_path" => 'user1',
				 	"subscriber_count" => 1,
				 	"is_owner" => FALSE,
				 	"collaborator" => FALSE,
				 	"subscribed" => TRUE,
				 	"public" => TRUE
				)
			),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_get_array
	*/
	public function test_get_array($bucket_id, $user_id, $visiting_user_id, $expected)
	{
		 $bucket = ORM::factory("Bucket", $bucket_id);
		 $user = ORM::factory("User", $user_id);
		 $visiting_user = ORM::factory("User", $visiting_user_id);
		 
		 $bucket_array = $bucket->get_array($user, $visiting_user);
		 
		 $this->assertEquals($expected, $bucket_array);
	}
	
	/**
	* @test
	*/
	public function test_create_from_array()
	{
		$bucket = Model_Bucket::create_from_array(array(
			'account_id' => 3,
			'user_id' => 5,
			'name' => 'test bucket'
		));
		
		
		$this->assertEquals(3, $bucket->account_id);
		$this->assertEquals(5, $bucket->user_id);
		$this->assertEquals('test bucket', $bucket->bucket_name);
	}
	
	
	/**
	* Provides test data for test_get_collaborators()
	*/
	public function provider_get_collaborators()
	{
		$all_collaborators = array(
			array(
				'id' => 4, 
				'name' => 'user2 name',
				'email' => 'user2@example.com',
				'account_path' => 'user2',
				'collaborator_active' => TRUE,
				'read_only' => FALSE,
				'avatar' => Swiftriver_Users::gravatar('user2@example.com', 40)
			),
			array(
				'id' => 5, 
				'name' => 'user3 name',
				'email' => 'user3@example.com',
				'account_path' => 'user3',
				'collaborator_active' => FALSE,
				'read_only' => FALSE,
				'avatar' => Swiftriver_Users::gravatar('user3@example.com', 40)
			),
			array(
				'id' => 6, 
				'name' => 'user4 name',
				'email' => 'user4@example.com',
				'account_path' => 'user4',
				'collaborator_active' => TRUE,
				'read_only' => TRUE,
				'avatar' => Swiftriver_Users::gravatar('user4@example.com', 40)
			)
		);
		
		$active_collaborators = array_filter(
			$all_collaborators, 
			function($collaborator) {
				return $collaborator['collaborator_active'] == TRUE;
			}
		);
		$active_collaboratos = array_values($active_collaborators);
		
		return array(
			array(1, FALSE, $all_collaborators),
			array(1, TRUE, $active_collaboratos)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_collaborators
	*/
	public function test_get_collaborators($bucket_id, $active_only, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		$collaborators = $bucket->get_collaborators($active_only);
		
		$this->assertEquals($expected, $collaborators);
	}
	
	
	/**
	* @test
	*/
	public function test_get_comments()
	{
		$bucket = ORM::factory("Bucket", 1);
		$comments = $bucket->get_comments(3);
		
		$this->assertEquals(
			array(
				array(
					'id' => 1, 
					'name' => 'user1 name',
					'user_id' => 3,
					'comment_content' => 'Awesome',
					'date' => '2012-11-20 05:44:23',
					'avatar' => Swiftriver_Users::gravatar('user1@example.com', 40),
					'score' => 1
				)
			),
			$comments
		);
	}
	
	/**
	* Provides test data for test_is_creator()
	*/
	public function provider_is_creator()
	{
		return array(
			array(1, 3, TRUE),
			array(1, 4, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_creator
	*/
	public function test_is_creator($bucket_id, $user_id, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals($expected, $bucket->is_creator($user_id));
	}
	
	/**
	* Provides test data for test_is_owner()
	*/
	public function provider_is_owner()
	{
		return array(
			// Creator
			array(1, 3, TRUE),
			// Active collaborator
			array(1, 4, TRUE),
			// Inactive collaborator
			array(1, 5, FALSE),
			// Read only collaborator
			array(1, 6, FALSE),
			// Non collaborator
			array(1, 7, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_owner
	*/
	public function test_is_owner($bucket_id, $user_id, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals($expected, $bucket->is_owner($user_id));
	}
	
	/**
	* Provides test data for test_is_collaborator()
	*/
	public function provider_is_collaborator()
	{
		return array(
			// Creator
			array(1, 3, FALSE),
			// Active collaborator
			array(1, 4, TRUE),
			// Inactive collaborator
			array(1, 5, TRUE),
			// Read only collaborator
			array(1, 6, TRUE),
			// Non collaborator
			array(1, 7, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_collaborator
	*/
	public function test_is_collaborator($bucket_id, $user_id, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals($expected, $bucket->is_collaborator($user_id));
	}
	
	/**
	* Provides test data for test_is_subscriber()
	*/
	public function provider_is_subscriber()
	{
		return array(
			// Creator
			array(1, 3, FALSE),
			// Active collaborator
			array(1, 4, FALSE),
			// Inactive collaborator
			array(1, 5, FALSE),
			// Read only collaborator
			array(1, 6, FALSE),
			// Subscriber and Non collaborator
			array(1, 7, TRUE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_subscriber
	*/
	public function test_is_subscriber($bucket_id, $user_id, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals($expected, $bucket->is_subscriber($user_id));
	}
	
	/**
	* @test
	*/
	public function test_set_token()
	{
		$bucket = ORM::factory("Bucket", 1);
		$bucket->set_token();
		
		$this->assertNotEquals('token', $bucket->public_token);
	}
	
	/**
	* Provides test data for test_is_valid_token()
	*/
	public function provider_is_valid_token()
	{
		return array(
			array(1, 'token', TRUE),
			array(1, 'invalid token', FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_valid_token
	*/
	public function test_is_valid_token($bucket_id, $token, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals(
			$expected, 
			$bucket->is_valid_token($token)
		);
	}
	
	/**
	* Provides test data for test_get_buckets()
	*/
	public function provider_get_buckets()
	{
		return array(
			// User with 2 buckets, no collaborations, no subscriptions
			array(array(2, 3, 4)),
			array(array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_buckets
	*/
	public function test_get_buckets($bucket_ids)
	{		
		$expected = array();
		foreach ($bucket_ids as $bucket_id)
		{
			$expected[] = ORM::factory("Bucket", $bucket_id);
		}
		
		$this->assertEquals(
			$expected, 
			Model_Bucket::get_buckets($bucket_ids)
		);
	}
	
	
	/**
	* Provides test data for test_get_subscriber_count()
	*/
	public function provider_get_subscriber_count()
	{
		return array(
			array(1, 1),
			array(2, 0),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_subscriber_count
	*/
	public function test_get_subscriber_count($bucket_id, $expected)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		
		$this->assertEquals($expected, $bucket->get_subscriber_count());
	}
	
	/**
	* Provides test data for test_add_drop()
	*/
	public function provider_add_drop()
	{
		return array(
			// Existing drop not in bucket yet
			array(1, 1, TRUE, TRUE),
			// Non existent drop
			array(1, 99, FALSE, FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_add_drop
	*/
	public function test_add_drop($bucket_id, $drop_id, $expectedRet, $expectedHas)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		$drop = ORM::factory("Droplet", $drop_id);
		
		$this->assertEquals($expectedRet, $bucket->add_drop($drop));
		$this->assertEquals($expectedHas, $bucket->has('droplets', $drop_id));
	}
	
	/**
	* Provides test data for test_remove_drop()
	*/
	public function provider_remove_drop()
	{
		return array(
			// Existing drop not in bucket yet
			array(1, 2, TRUE, FALSE),
			// Drop not in bucket
			array(1, 1, FALSE, FALSE),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_remove_drop
	*/
	public function test_remove_drop($bucket_id, $drop_id, $expectedRet, $expectedHas)
	{
		$bucket = ORM::factory("Bucket", $bucket_id);
		$drop = ORM::factory("Droplet", $drop_id);
		
		$this->assertEquals($expectedRet, $bucket->remove_drop($drop));
		$this->assertEquals($expectedHas, $bucket->has('droplets', $drop_id));
	}
	
	/**
	* @test
	*/
	public function test_get_max_droplet_id()
	{
		$bucket = ORM::factory("Bucket", 1);
		
		$this->assertEquals(4, $bucket->get_max_droplet_id());
	}
	
	/**
	* Provides test data for test_get_droplets()
	*/
	public function provider_get_droplets()
	{
		return array(
			// Bucket with no drops
			array(3, 99, 0, 1, 10, FALSE, array(), 0, array()),
			// Drops in page 1 with 2 drops per page
			array(3, 1, 0, 1, 10, FALSE, array(), 2, 
				array(
					array(
					    'id' => '5',
					    'sort_id' => '4',
					    'droplet_title' => 'droplet_5_title',
					    'droplet_content' => 'droplet_5_content',
					    'channel' => 'rss',
					    'identity_name' => 'identity1_name',
					    'identity_avatar' => 'identity1_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:05 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							)
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					),
					array(
					    'id' => '4',
					    'sort_id' => '3',
					    'droplet_title' => 'droplet_4_title',
					    'droplet_content' => 'droplet_4_content',
					    'channel' => 'twitter',
					    'identity_name' => 'identity2_name',
					    'identity_avatar' => 'identity2_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:04 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							)
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					)
				)
			),
			// Drops in page 2 with 2 drops per page
			array(3, 1, 0, 2, 10, FALSE, array(), 2, 
				array(
					array(
					    'id' => '3',
					    'sort_id' => '2',
					    'droplet_title' => 'droplet_3_title',
					    'droplet_content' => 'droplet_3_content',
					    'channel' => 'twitter',
					    'identity_name' => 'identity2_name',
					    'identity_avatar' => 'identity2_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:03 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							),
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					),
					array(
					    'id' => '2',
					    'sort_id' => '1',
					    'droplet_title' => 'droplet_2_title',
					    'droplet_content' => 'droplet_2_content',
					    'channel' => 'rss',
					    'identity_name' => 'identity1_name',
					    'identity_avatar' => 'identity1_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:02 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							),
							array(
								'id' => '2',
								'bucket_name' => 'Testing Bucket 2',
							)
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					)
				)
			),
			// Get specific drop id 3
			array(3, 1, 3, NULL, PHP_INT_MAX, FALSE, array(), 50, 
				array(
					array(
					    'id' => '3',
					    'sort_id' => '2',
					    'droplet_title' => 'droplet_3_title',
					    'droplet_content' => 'droplet_3_content',
					    'channel' => 'twitter',
					    'identity_name' => 'identity2_name',
					    'identity_avatar' => 'identity2_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:03 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							)
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					),
				)
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_droplets
	*/
	public function test_get_droplets($user_id, $bucket_id,
	    $drop_id, $page, $max_id, $photos,
	    $filters, $limit, $expected)
	{
		$drops = Model_Bucket::get_droplets(
			$user_id, $bucket_id, $drop_id,
	    	$page, $max_id, $photos,
	    	$filters, $limit
		);
		
		$this->assertEquals($expected, $drops);
	}
	
	/**
	* Provides test data for test_get_droplets()
	*/
	public function provider_get_droplets_since_id()
	{
		return array(
			// Bucket with no new drops
			array(3, 99, 0, FALSE, array()),
			// 1 new drops since id 3
			array(3, 1, 3, FALSE, 
				array(
					array(
					    'id' => '5',
					    'sort_id' => '4',
					    'droplet_title' => 'droplet_5_title',
					    'droplet_content' => 'droplet_5_content',
					    'channel' => 'rss',
					    'identity_name' => 'identity1_name',
					    'identity_avatar' => 'identity1_avatar',
					    'droplet_date_pub' => 'Nov 15, 2012 00:00:05 UTC',
					    'user_score' => NULL,
					    'original_url' => NULL,
					    'comment_count' => '0',
					    'buckets' => array(
							array(
								'id' => '1',
								'bucket_name' => 'Testing Bucket 1',
							)
						),
					    'tags' => array(),
					    'links' => array(),
					    'media' => array(),
					    'places' => array(),
					),
				)
			),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_droplets_since_id
	*/
	public function test_get_droplets_since_id(
				$user_id, $bucket_id,
				$since_id, $photos, 
				$expected)
	{
		$drops = Model_Bucket::get_droplets_since_id(
			$user_id, $bucket_id, $since_id,
			$photos
		);
		
		$this->assertEquals($expected, $drops);
	}
 }