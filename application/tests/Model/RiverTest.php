<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_River tests.
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
class Model_RiverTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array(
		'plugins', 'users', 'accounts', 'drops', 
		'identities', 'rivers', 'river_droplets', 'channel_filters',
		'buckets', 'settings'
	);
	
	
	/**
	* Provides test data for testSaveInvalid()
	*/
	public function provider_validation()
	{
		 return array(
			 // Empty river name
			 array(NULL, 0, 'drops'),
			 // Long river name
			 array(str_repeat("a", 256), 0, 'drops'),
			 // Invalid river_public
			 array('river', 2, 'drops'),
			 // Invalid layoyt
			 array('river', 1, 'invalid'),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_validation
	*/
	public function test_validation($river_name, $river_public, $default_layout)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$river = ORM::factory("River");
		$river->river_name = $river_name;
		$river->river_public = $river_public;
		$river->default_layout = $default_layout;
		$river->save();
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$river = ORM::factory("River");
		$river->river_name = 'river';
		$river->river_public = 1;
		$river->default_layout = 'drops';
		$river->save();
		
		$this->assertTrue($river->loaded());
		$this->assertEquals(
			$river->river_name_url, 
			URL::title($river->river_name)
		);
		$this->assertTrue(isset($river->public_token));
		$this->assertEquals(
			Model_Setting::get_setting('default_river_drop_quota'), 
			$river->drop_quota
		);
		
		$river_lifetime = Model_Setting::get_setting('default_river_lifetime');
		$expiry_date = strtotime(sprintf("+%s day", $river_lifetime), strtotime($river->river_date_add));
		$this->assertEquals(date("Y-m-d H:i:s", $expiry_date), $river->river_date_expiry);
	}
	
	/**
	* @test
	*/
	public function test_get_base_url()
	{
		$river = ORM::factory("River", 1);
		
		$this->assertEquals(
			'/user1/river/testing-river-1', 
			$river->get_base_url()
		);
	}
	
	/**
	* Provides test data for test_get_array()
	*/
	public function provider_get_array()
	{
		 return array(
			// Onwer user_id 3 requesting own river
			array(
				1, 
				3, 
				3,
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
				)
			),
			// Collaborator user_id 4 requesting public river owned by user_id 3
			array(
				1, 
				4, 
				4,
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
				)
			),
			// Subscriber user_id 7 requesting public river owned by user_id 3
			array(
				1, 
				7, 
				7,
				array(
					"id" => 1, 
					"name" => 'Testing River 1',
				 	"type" => 'river',
				 	"url" => '/user1/river/testing-river-1',
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
	public function test_get_array($river_id, $user_id, $visiting_user_id, $expected)
	{
		 $river = ORM::factory("River", $river_id);
		 $user = ORM::factory("User", $user_id);
		 $visiting_user = ORM::factory("User", $visiting_user_id);
		 
		 $river_array = $river->get_array($user, $visiting_user);
		 
		 $this->assertEquals($expected, $river_array);
	}
	
	/**
	* @test
	*/
	public function test_create_new()
	{
		$this->setExpectedException('Swiftriver_Exception_Quota');
		
		Model_River::create_new("river", 1, ORM::factory("Account", 4));
	}
	
	/**
	* Provides test data for test_is_valid_river_id()
	*/
	public function provider_is_valid_river_id()
	{
		return array(
			array(99, FALSE),
			array(1, TRUE)
			
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_valid_river_id
	*/
	public function test_is_valid_river_id($river_id, $expected)
	{
		$valid = Model_River::is_valid_river_id($river_id);
		$this->assertEquals($expected, $valid);
	}
	
	/**
	* @test
	*/
	public function test_get_max_droplet_id()
	{
		$this->assertEquals(999, Model_river::get_max_droplet_id(4));
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
	public function test_is_creator($river_id, $user_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals($expected, $river->is_creator($user_id));
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
	public function test_is_owner($river_id, $user_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals($expected, $river->is_owner($user_id));
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
	public function test_is_collaborator($river_id, $user_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals($expected, $river->is_collaborator($user_id));
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
	public function test_is_subscriber($river_id, $user_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals($expected, $river->is_subscriber($user_id));
	}
	
	/**
	* @test
	*/
	public function test_set_token()
	{
		$river = ORM::factory("River", 1);
		$river->set_token();
		
		$this->assertNotEquals('token', $river->public_token);
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
	public function test_is_valid_token($river_id, $token, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals(
			$expected, 
			$river->is_valid_token($token)
		);
	}
	
	/**
	* Provides test data for test_get_rivers()
	*/
	public function provider_get_rivers()
	{
		return array(
			// User with 2 rivers, no collaborations, no subscriptions
			array(array(2, 3, 4)),
			array(array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_rivers
	*/
	public function test_get_rivers($river_ids)
	{		
		$expected = array();
		foreach ($river_ids as $river_id)
		{
			$expected[] = ORM::factory("River", $river_id);
		}
		
		$this->assertEquals(
			$expected, 
			Model_River::get_rivers($river_ids)
		);
	}
	
	private function date_add($date, $days)
	{
		$new_date =  strtotime(sprintf("+%d day", $days), $date);
		return date("Y-m-d H:i:s", $new_date);
	}
	
	/**
	* Provides test data for test_extend_lifetime()
	*/
	public function provider_extend_lifetime()
	{
		$lifetime = Model_Setting::get_setting('default_river_lifetime');
		return array(
			// Expired river
			array(2, $this->date_add(time(), $lifetime), FALSE, 0, 1, 3, 0),
			// Do not extend full rivers
			array(3, '2012-11-29 00:00:03', TRUE, 1, 0, 1, 1),
			// Active river
			array(4, '2022-12-13 00:00:04', TRUE, 0, 1, 1, 0)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_extend_lifetime
	*/
	public function test_extend_lifetime(
		$river_id, $expected_expiry_date, $exact_date_match, 
		$expected_river_expired, $expected_river_active, 
		$expected_extension_count, $expected_expiry_notification_sent)
	{
		$river = ORM::factory("River", $river_id);
		$river->extend_lifetime();
		
		if ($exact_date_match)
		{
			$this->assertEquals($expected_expiry_date, $river->river_date_expiry);
		}
		else
		{
			// Two times are within 10 minutes of each other since extend lifetime
			// uses current time when the river has expired
			$diff = abs(strtotime($expected_expiry_date) - strtotime($river->river_date_expiry));
			$this->assertTrue($diff < 300);
		}
		
		$this->assertEquals($expected_river_expired, $river->river_expired);
		$this->assertEquals($expected_river_active, $river->river_active);
		$this->assertEquals($expected_extension_count, $river->extension_count);
		$this->assertequals(
			$expected_expiry_notification_sent, 
			$river->expiry_notification_sent
		);
	}
	
	/**
	* Provides test data for test_get_days_to_expiry()
	*/
	public function provider_get_days_to_expiry()
	{
		return array(
			// Active
			array(1, '2012-10-01 00:00:00', 59),
			// Expired
			array(2, '2012-10-01 00:00:00', 0),
			// Expired
			array(3, '2012-10-01 00:00:00', 0),
			// Active
			array(4, '2012-10-01 00:00:00', 3711),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_days_to_expiry
	*/
	public function test_get_days_to_expiry($river_id, $current_date, $expected)
	{
		$river = ORM::factory('River', $river_id);
		
		$this->assertEquals(
			$expected, 
			round($river->get_days_to_expiry(strtotime($current_date)))
		);
	}
	
	/**
	* Provides test data for test_is_full()
	*/
	public function provider_is_full()
	{
		return array(
			array(1, FALSE),
			array(2, FALSE),
			array(3, TRUE),
			array(4, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_full
	*/
	public function test_is_full($river_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		$this->assertEquals($expected, $river->is_full());
	}
	
	/**
	* Provides test data for test_is_notified()
	*/
	public function provider_is_notified()
	{
		return array(
			array(1, FALSE),
			array(2, TRUE),
			array(3, TRUE),
			array(4, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_notified
	*/
	public function test_is_notified($river_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		$this->assertEquals($expected, $river->is_notified());
	}
	
	/**
	* Provides test data for test_is_expired()
	*/
	public function provider_is_expired()
	{
		return array(
			array(1, FALSE),
			array(2, TRUE),
			array(3, TRUE),
			array(4, FALSE)
		);
	}
	
	/**
	* @test
	* @dataProvider provider_is_expired
	*/
	public function test_is_expired($river_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		$this->assertEquals($expected, $river->is_expired());
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
	public function test_get_collaborators($river_id, $active_only, $expected)
	{
		$river = ORM::factory("River", $river_id);
		$collaborators = $river->get_collaborators($active_only);
		
		$this->assertEquals($expected, $collaborators);
	}
	
	/**
	* @test
	*/
	public function test_get_channel_by_id()
	{
		$river = ORM::factory('River', 1);
		$channel = $river->get_channel_by_id(2);
		
		$this->assertInstanceOf("Model_Channel_Filter", $channel);
		$this->assertEquals("2", $channel->id);
	}
	
	/**
	* @test
	*/
	public function test_get_channel()
	{
		$river = ORM::factory('River', 1);
		
		// Existing channel
		$channel = $river->get_channel("rss");
		$this->assertInstanceOf("Model_Channel_Filter", $channel);
		$this->assertEquals("1", $channel->id);
		
		// New channel
		$channel = $river->get_channel("new_facebook_channel");
		$this->assertInstanceOf("Model_Channel_Filter", $channel);
		$this->assertTrue($channel->loaded());
		$this->assertEquals("new_facebook_channel", $channel->channel);
		$this->assertTrue($channel->filter_enabled);
	}
	
	/**
	* Provides test data for test_get_channel_options()
	*/
	public function provider_get_channel_options()
	{
		return array(
			// River with rss channel
			array(1, 1, NULL,
				array(
					array(
						'value' => 'http://feeds.bbci.co.uk/news/rss.xml',
						'title' => 'BBC News - Home',
						'quota_usage' => 1,
						'id' => '1',
						'key' => 'url',
					),
					array(
						'value' => 'http://feeds.feedburner.com/ommalik',
						'title' => 'GigaOM',
						'quota_usage' => 1,
						'id' => '2',
						'key' => 'url',
					),
					array(
						'value' => 'http://www.engadget.com/rss.xml',
						'title' => 'Engadget',
						'quota_usage' => 1,
						'id' => '3',
						'key' => 'url',
					),
				)
			),
			// River with twitter channel
			array(1, 2, NULL,
				array(
					array(
						'value' => 'meow',
						'quota_usage' => 1,
						'id' => '4',
						'key' => 'keyword',
					),
					array(
						'value' => 'kudishnyao',
						'quota_usage' => 1,
						'id' => '5',
						'key' => 'keyword',
					),
					array(
						'value' => '69mb',
						'quota_usage' => 1,
						'id' => '6',
						'key' => 'user',
					),
				)
			),
			// Specific channel option
			array(1, 2, 6,
				array(
					array(
						'value' => '69mb',
						'quota_usage' => 1,
						'id' => '6',
						'key' => 'user',
					),
				)
			),
			// Invalid option
			array(1, 2, 999, array()
		),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_channel_options
	*/
	public function test_get_channel_options($river_id, $channel_id, $options_id, $expected)
	{
		$river = ORM::factory('River', $river_id);
		$channel = ORM::factory("Channel_Filter", $channel_id);
		
		$this->assertEquals($expected, $river->get_channel_options($channel, $options_id));
	}
	
	/**
	* Provides test data for test_get_channels()
	*/
	public function provider_get_channels()
	{
		return array(
			// All channels
			array(1, FALSE, array(
				array(
					'id' => '1',
					'channel' => 'rss',
					'name' => 'RSS',
					'enabled' => TRUE,
					'options' => array(
						array(
							'value' => 'http://feeds.bbci.co.uk/news/rss.xml',
							'title' => 'BBC News - Home',
							'quota_usage' => 1,
							'id' => '1',
							'key' => 'url',
						),
						array(
							'value' => 'http://feeds.feedburner.com/ommalik',
							'title' => 'GigaOM',
							'quota_usage' => 1,
							'id' => '2',
							'key' => 'url',
						),
						array(
							'value' => 'http://www.engadget.com/rss.xml',
							'title' => 'Engadget',
							'quota_usage' => 1,
							'id' => '3',
							'key' => 'url',
						),
					)
				),
				array(
					'id' => '2',
					'channel' => 'twitter',
					'name' => 'Twitter',
					'enabled' => FALSE,
					'options' => array(
						array(
							'value' => 'meow',
							'quota_usage' => 1,
							'id' => '4',
							'key' => 'keyword',
						),
						array(
							'value' => 'kudishnyao',
							'quota_usage' => 1,
							'id' => '5',
							'key' => 'keyword',
						),
						array(
							'value' => '69mb',
							'quota_usage' => 1,
							'id' => '6',
							'key' => 'user',
						),
					)
				),
			)),
			// Active Channels Only
			array(2, TRUE, array(
				array(
					'id' => '5',
					'channel' => 'twitter',
					'name' => 'Twitter',
					'enabled' => TRUE,
					'options' => array(
						array(
							'value' => 'meow',
							'quota_usage' => 1,
							'id' => '10',
							'key' => 'keyword',
						),
						array(
							'value' => 'kudishnyao',
							'quota_usage' => 1,
							'id' => '11',
							'key' => 'keyword',
						),
						array(
							'value' => '69mb,ushahidi',
							'quota_usage' => 2,
							'id' => '12',
							'key' => 'user',
						),
					)
				),
			)),
			// River without channels
			array(3, FALSE, array()),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_channels
	*/
	public function test_get_channels($river_id, $active, $expected)
	{
		$river = ORM::factory('River', $river_id);
		
		$this->assertEquals($expected, $river->get_channels($active));
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
	public function test_get_subscriber_count($river_id, $expected)
	{
		$river = ORM::factory("River", $river_id);
		
		$this->assertEquals($expected, $river->get_subscriber_count());
	}
	
	/**
	* Provides test data for test_get_droplets()
	*/
	public function provider_get_droplets()
	{
		return array(
			// River with no drops
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
	public function test_get_droplets($user_id, $river_id,
	    $drop_id, $page, $max_id, $photos,
	    $filters, $limit, $expected)
	{
		$drops = Model_River::get_droplets(
			$user_id, $river_id, $drop_id,
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
			// River with no new drops
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
				$user_id, $river_id,
				$since_id, $photos, 
				$expected)
	{
		$drops = Model_River::get_droplets_since_id(
			$user_id, $river_id, $since_id,
			array(), $photos
		);
		
		$this->assertEquals($expected, $drops);
	}
}