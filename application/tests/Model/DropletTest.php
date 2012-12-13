<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Droplet tests
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
class Model_DropletTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array(
		'settings', 'seq', 'drops', 'tags', 'places', 
		'links', 'media', 'river_tag_trends', 'river_droplets',
		'bucket_droplets', 'account_droplet_tags', 'account_droplet_links',
		'account_droplet_media', 'account_droplet_places'
	);
	
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
		$this->assertEquals($expected_start, Model_Droplet::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'droplets'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* Provides test data for test_create_from_array()
	*/
	public function provider_create_from_array()
	{
		return array(
			// Generic drop
			array(
				array(
					array(
						'channel' => 'drop_channel',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar', 
						'droplet_orig_id' => "drop's original id",
						'droplet_type' => 'original',
						'droplet_title' => 'drop title',
						'droplet_content' => 'drop content',
						'droplet_date_pub' => '2015-03-09 05:44:23',
						// River id 2 is disabled, drop wont' be added
						'river_id' => array(1, 2, 4),
						'tags' => array(
							array(
								'tag_name' => 'CIA',
								'tag_type' => 'organization'
							),
							array(
								'tag_name' => 'A New Tag',
								'tag_type' => 'thing'
							)
						),
						'places' => array(
							array(
								'place_name' => 'Utopia',
								'latitude' => -1,
								'longitude' => 36,
								'source' => 'testing'
							),
							array(
								'place_name' => 'China',
								'latitude' => 35,
								'longitude' => 105,
								'source' => 'testing'
							)
						),
						'links' => array(
							array(
								'url' => 'http://origina.url.com/',
								'original_url' => TRUE
							),
							array(
								'url' => 'http://www.bbc.co.uk/sport/0/football/20319573',
							)
						),
						'media' => array(
							array(
								'url' => 'http://drop.image.com/',
								'type' => 'image',
								'droplet_image' => TRUE
							),
							array(
								'url' => 'http://gigaom2.files.wordpress.com/2012/10/datacapspercentage.jpeg',
								'type' => 'image',
							)
						),
					)
				),
 				array(
					'identities' => array(
						'id' => 99,
						'channel' => 'drop_channel',
						'identity_orig_id' => '2',
						'identity_name' => 'identity2_name',
						'identity_username' => 'identity2',
						'identity_avatar' => 'identity2_avatar',
						'hash' => 'd5581284eb277b68ea5902e45f55e692',
					),
 					'droplets' => array(
						'id' => 99,
						'identity_id' => '99',
 						'channel' => 'drop_channel',
						'droplet_hash' => '95b1a12b1840a7629b27b40c1e5940b6',
 						'droplet_orig_id' => "drop's original id",
 						'droplet_type' => 'original',
 						'droplet_title' => 'drop title',
 						'droplet_content' => 'drop content',
						'droplet_image' => 99,
 						'droplet_date_pub' => '2015-03-09 05:44:23',
						'original_url' => 99,
						'comment_count' => 0
 					),
					'droplet_tags' => array(
						array('droplet_id' => 99, 'tag_id' => 7),
						array('droplet_id' => 99, 'tag_id' => 99),
					),
					'droplet_places' => array(
						array('droplet_id' => 99, 'place_id' => 3),
						array('droplet_id' => 99, 'place_id' => 99),
					),
					'droplet_links' => array(
						array('droplet_id' => 99, 'link_id' => 10),
						array('droplet_id' => 99, 'link_id' => 99),
					),
					'droplet_media' => array(
						array('droplet_id' => 99, 'media_id' => 1),
						array('droplet_id' => 99, 'media_id' => 99),
					),
					'river_droplets' => array(
						array('droplet_id' => 99, 'river_id' => 4, 'droplet_date_pub' => '2015-03-09 05:44:23'),
						array('droplet_id' => 99, 'river_id' => 1, 'droplet_date_pub' => '2015-03-09 05:44:23'),
					),
					'river_tag_trends' => array(
						array('river_id' => 1, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'A New Tag', 'tag_type' => 'thing', 'count' => 1),
						array('river_id' => 1, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'China', 'tag_type' => 'place', 'count' => 1),
						array('river_id' => 1, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'CIA', 'tag_type' => 'organization', 'count' => 1),
						array('river_id' => 1, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'Utopia', 'tag_type' => 'place', 'count' => 1),
						
						array('river_id' => 4, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'A New Tag', 'tag_type' => 'thing', 'count' => 1),
						array('river_id' => 4, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'China', 'tag_type' => 'place', 'count' => 1),
						array('river_id' => 4, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'CIA', 'tag_type' => 'organization', 'count' => 1),
						array('river_id' => 4, 'date_pub' => '2015-03-09 05:00:00', 'tag' => 'Utopia', 'tag_type' => 'place', 'count' => 1),
					),
 				 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_create_from_array
	*/
	public function test_create_from_array($drops, $expected)
	{
		list($all_drops, $new_drops) = Model_Droplet::create_from_array($drops);
		
		// Check identities table populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `id`, `hash`, `channel`, `identity_orig_id`, `identity_username`, ".
			"`identity_name`, `identity_avatar` ".
			"FROM `identities` ".
			"WHERE id = ".$expected['identities']['id']
		)->execute()->as_array();
			
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected['identities'], $results[0]);
		
		// Check droplets table populated correctly
		$results = DB::query(
			Database::SELECT, 
			"SELECT `id`, `identity_id`, `channel`, `droplet_hash`, `droplet_orig_id`, ".
			"`droplet_type`, `droplet_title`, `droplet_content`, `droplet_image`, ".
			"`droplet_date_pub`, `original_url`, `comment_count` ".
			"FROM `droplets` ".
			"WHERE id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected['droplets'], $results[0]);
		
		// Check droplet_tags populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `tag_id` ".
			"FROM `droplets_tags` ".
			"WHERE droplet_id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['droplet_tags']), count($results));
		$this->assertEquals($expected['droplet_tags'], $results);
		
		// Check droplet_places populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `place_id` ".
			"FROM `droplets_places` ".
			"WHERE droplet_id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['droplet_places']), count($results));
		$this->assertEquals($expected['droplet_places'], $results);
		
		// Check droplet_links populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `link_id` ".
			"FROM `droplets_links` ".
			"WHERE droplet_id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['droplet_links']), count($results));
		$this->assertEquals($expected['droplet_links'], $results);
		
		// Check droplet_media populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `media_id` ".
			"FROM `droplets_media` ".
			"WHERE droplet_id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['droplet_media']), count($results));
		$this->assertEquals($expected['droplet_media'], $results);
		
		// Check river_droplets populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `river_id`, `droplet_date_pub` ".
			"FROM `rivers_droplets` ".
			"WHERE droplet_id = ".$expected['droplets']['id']
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['river_droplets']), count($results));
		$this->assertEquals($expected['river_droplets'], $results);
		
		// Check river tag trends populated
		$results = DB::query(
			Database::SELECT, 
			"SELECT `river_id`, `date_pub`, `tag`, `tag_type`, `count` ".
			"FROM `river_tag_trends` ".
			"WHERE date_pub = '2015-03-09 05:00:00' ".
			"ORDER BY river_id, tag, tag_type "
		)->execute()->as_array();
		
		$this->assertEquals(count($expected['river_tag_trends']), count($results));
		$this->assertEquals($expected['river_tag_trends'], $results);
	}
	
	/**
	* Provides test data for test_get_buckets()
	*/
	public function provider_get_buckets()
	{
		 return array(
			 // Drop with buckets
			 array(2,
			 	array(
					array(
						'id' => 1,
						'bucket_name' => 'Testing Bucket 1'
					),
					array(
						'id' => 2,
						'bucket_name' => 'Testing Bucket 2'
					),
				)
			 ),
			 // Drop without buckets
			 array(6, array())
		);
	}
	
	/**
	* @test
	* @dataProvider provider_get_buckets
	*/
	public function test_get_buckets($bucket_id, $expected)
	{		
		$drop = ORM::factory("Droplet", $bucket_id);
		$this->assertEquals($expected, $drop->get_buckets());
	}
	
	/**
	* Provides test data for test_populate_buckets()
	*/
	public function provider_populate_buckets()
	{
		 return array(
			 // Drop with buckets
			 array(
				 array(
					 array('id' => 2)
				 ), 
				 array(
					 array(
						 'id' => 2,
						 'buckets' => array(
		 					array(
		 						'id' => 1,
		 						'bucket_name' => 'Testing Bucket 1'
		 					),
		 					array(
		 						'id' => 2,
		 						'bucket_name' => 'Testing Bucket 2'
		 					),
						 )
					 )
				 )
			 ),
			 // Drop without buckets
			 array(
				 array(
					 array('id' => 6)
				 ), 
				 array(
					 array(
						 'id' => 6,
						 'buckets' => array()
					 )
				 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_populate_buckets
	*/
	public function test_populate_buckets($drop, $expected)
	{		
		Model_Droplet::populate_buckets($drop);
		$this->assertEquals($expected, $drop);
	}
	
	/**
	* Provides test data for test_populate_tags()
	*/
	public function provider_populate_tags()
	{
		 return array(
			 // Drop without tags
			 array(1,
				 array(
					 array('id' => 6)
				 ), 
				 array(
					 array(
						 'id' => 6,
						 'tags' => array()
					 )
				 )
			 ),
			 // Drop with tag
			 array(1,
				 array(
					 array('id' => 1)
				 ), 
				 array(
					 array(
						 'id' => 1,
						 'tags' => array(
							 array(
								 'id' => 1,
								 'tag' => 'Jeremy Hunt',
								 'tag_canonical' => 'jeremy hunt'
							 ),
							 array(
								 'id' => 11,
								 'tag' => 'Custom Tag',
								 'tag_canonical' => 'custom tag'
							 )
						 )
					 )
				 )
			 ),
			 // Drop with tag and account with deleted tag
			 array(2,
				 array(
					 array('id' => 1)
				 ), 
				 array(
					 array(
						 'id' => 1,
						 'tags' => array()
					 )
				 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_populate_tags
	*/
	public function test_populate_tags($account_id, $drop, $expected)
	{		
		Model_Droplet::populate_tags($drop, $account_id);
		$this->assertEquals($expected, $drop);
	}
	
	/**
	* Provides test data for test_populate_links()
	*/
	public function provider_populate_links()
	{
		 return array(
			 // Drop without links
			 array(1,
				 array(
					 array('id' => 6)
				 ), 
				 array(
					 array(
						 'id' => 6,
						 'links' => array()
					 )
				 )
			 ),
			 // Drop with link
			 array(1,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'links' => array(
			 				 array(
			 					 'id' => 1,
			 					 'url' => 'http://www.bbc.co.uk/news/uk-wales-south-east-wales-20312645#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa',
			 				 ),
			 				 array(
			 					 'id' => 3,
			 					 'url' => 'http://www.bbc.co.uk/nature/20273855',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 //// Drop with link and account with deleted link
			 array(2,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'links' => array()
			 		 )
			 	 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_populate_links
	*/
	public function test_populate_links($account_id, $drop, $expected)
	{		
		Model_Droplet::populate_links($drop, $account_id);
		$this->assertEquals($expected, $drop);
	}
	
	/**
	* Provides test data for test_populate_media()
	*/
	public function provider_populate_media()
	{
		 return array(
			 // Drop without media
			 array(1,
				 array(
					 array('id' => 6)
				 ), 
				 array(
					 array(
						 'id' => 6,
						 'media' => array()
					 )
				 )
			 ),
			 // Drop with media
			 array(1,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'media' => array(
			 				 array(
								 'id' => 1,
								 'url' => 'http://gigaom2.files.wordpress.com/2012/10/datacapspercentage.jpeg',
								 'type' => 'image',
								 'thumbnails' => array(
									 200 => 'https://2bcbd22fbb0a02d76141-1680e9dfed1be27cdc47787ec5d4ef89.ssl.cf1.rackcdn.com/625dd7cb656d258b4effb325253e880631699d80345016e9e755b4a04341cda1.peg'
								 ),
			 				 ),
			 			 )
			 		 )
			 	 )
			 ),
			 //// Drop with media and account with deleted media
			 array(2,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'media' => array()
			 		 )
			 	 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_populate_media
	*/
	public function test_populate_media($account_id, $drop, $expected)
	{		
		Model_Droplet::populate_media($drop, $account_id);
		
		$this->assertEquals($expected, $drop);
	}
	
	/**
	* Provides test data for test_populate_places()
	*/
	public function provider_populate_places()
	{
		 return array(
			 // Drop without places
			 array(1,
				 array(
					 array('id' => 6)
				 ), 
				 array(
					 array(
						 'id' => 6,
						 'places' => array()
					 )
				 )
			 ),
			 // Drop with place
			 array(1,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'places' => array(
			 				 array(
			 					 'id' => 1,
			 					 'place_name' => 'Wales',
								 'place_name_canonical' => 'wales',
								 'place_hash' => '867da1cf4e6bd9fc5512a19a90e0141f',
								 'latitude' => '-33',
								 'longitude' => '146'
			 				 ),
			 				 array(
			 					 'id' => 9,
			 					 'place_name' => 'Heathrow',
								 'place_name_canonical' => 'heathrow',
								 'place_hash' => '7a17c2a0d99f5f274d882bdc3acc5e79',
								 'latitude' => '28.7633',
								 'longitude' => '-81.3722'
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 // Drop with place and account with deleted place
			 array(2,
			 	 array(
			 		 array('id' => 1)
			 	 ), 
			 	 array(
			 		 array(
			 			 'id' => 1,
			 			 'places' => array()
			 		 )
			 	 )
			 ),
		);
	}
	
	/**
	* @test
	* @dataProvider provider_populate_places
	*/
	public function test_populate_places($account_id, $drop, $expected)
	{		
		Model_Droplet::populate_places($drop, $account_id);
		$this->assertEquals($expected, $drop);
	}
	
	/**
	* @test
	*/
	public function test_populate_metadata()
	{
		$drops = array(
			array('id' => 1)
		);
		
		Model_Droplet::populate_metadata($drops, 3);
		
		$drop = array_pop($drops);
		$this->assertArrayHasKey('buckets', $drop);
		$this->assertArrayHasKey('tags', $drop);
		$this->assertArrayHasKey('links', $drop);
		$this->assertArrayHasKey('media', $drop);
		$this->assertArrayHasKey('places', $drop);
	}
	
	/**
	* @test
	*/
	public function test_get_unprocessed_droplets()
	{
		$expected = array(
			array(
				'id' => '1',
				'droplet_raw' => 'droplet_1_content',
				'channel' => 'rss',
				'identity_orig_id' => '1',
				'identity_username' => 'identity1',
				'identity_name' => 'identity1_name',
				'droplet_orig_id' => '1',
				'droplet_type' => 'original',
				'droplet_title' => 'droplet_1_title',
				'droplet_content' => 'droplet_1_content',
				'droplet_locale' => NULL,
				'droplet_date_pub' => '2012-11-15 00:00:01',
				'river_id' => array(),
			),
		);
		
		$this->assertEquals(
			$expected, 
			Model_Droplet::get_unprocessed_droplets()
		);
	}
	
	/**
	* Provides test data for test_update_from_array()
	*/
	public function provider_update_from_array()
	{
		 return array(
			// New score
			array(1, 3, 
				array(
					'id' => 1,
					'droplet_score' => array(
						'user_id' => 3,
						'user_score' => 1
					),
					'buckets' => array()
			 	),
				array(
					'droplet_scores' => array(
						array(
							'droplet_id' => 1,
							'user_id' => 3,
							'score' => 1
						)
					),
					'buckets' => array()
				)
			),
			// Update score
			array(1, 4, 
				array(
					'id' => 1,
					'droplet_score' => array(
						'user_id' => 4,
						'user_score' => -1
					),
					'buckets' => array()
			 	),
				array(
					'droplet_scores' => array(
						array(
							'droplet_id' => 1,
							'user_id' => 4,
							'score' => -1
						)
					),
					'buckets' => array()
				)
			),
			// Add bucket
			array(1, 4, 
				array(
					'id' => 1,
					'droplet_score' => array(
						'user_id' => 4,
						'user_score' => -1
					),
					'buckets' => array(
						array(
							'id' => 1
						)
					)
			 	),
				array(
					'droplet_scores' => array(
						array(
							'droplet_id' => 1,
							'user_id' => 4,
							'score' => -1
						)
					),
					'buckets' => array(
						array('bucket_id' => 1)
					)
				)
			),
			// Remove bucket
			array(2, 3, 
				array(
					'id' => 2,
					'droplet_score' => array(
						'user_id' => 3,
						'user_score' => -1
					),
					'buckets' => array(
						array(
							'id' => 2
						)
					)
			 	),
				array(
					'droplet_scores' => array(
						array(
							'droplet_id' => 2,
							'user_id' => 3,
							'score' => -1
						)
					),
					'buckets' => array(
						array('bucket_id' => 2)
					)
				)
			),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_update_from_array
 	*/
	public function test_update_from_array($drop_id, $user_id, $update, $expected)
	{
		$drop = ORM::factory('Droplet', $drop_id);
		$drop->update_from_array($update, $user_id);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `droplet_id`, `user_id`, `score` ".
			"FROM `droplet_scores` ".
			"WHERE droplet_id = $drop_id ".
			"AND user_id = $user_id "
		)->execute()->as_array();
			
		$this->assertEquals($expected['droplet_scores'], $results);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `bucket_id` ".
			"FROM `buckets_droplets` ".
			"WHERE droplet_id = $drop_id "
		)->execute()->as_array();
			
		$this->assertEquals($expected['buckets'], $results);
	}
	
	/**
	* Provides test data for test_delete_tag()
	*/
	public function provider_delete_tag()
	{
		 return array(
			 array(1, 1, 1, FALSE),
			 array(1, 11, 1, TRUE)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_delete_tag
 	*/
	public function test_delete_tag($drop_id, $tag_id, $account_id, $is_user_defined)
	{
		Model_Droplet::delete_tag($drop_id, $tag_id, $account_id);
		
		if ( ! $is_user_defined)
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_tags` ".
				"WHERE droplet_id = $drop_id ".
				"AND tag_id = $tag_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEquals(1, count($results));
			$this->assertEquals(1, $results[0]['deleted']);
		}
		else
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_tags` ".
				"WHERE droplet_id = $drop_id ".
				"AND tag_id = $tag_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEmpty($results);
		}
	}
	
	/**
	* Provides test data for test_delete_link()
	*/
	public function provider_delete_link()
	{
		 return array(
			 array(1, 1, 1, FALSE),
			 array(1, 3, 1, TRUE)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_delete_link
 	*/
	public function test_delete_link($drop_id, $link_id, $account_id, $is_user_defined)
	{
		Model_Droplet::delete_link($drop_id, $link_id, $account_id);
		
		if ( ! $is_user_defined)
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_links` ".
				"WHERE droplet_id = $drop_id ".
				"AND link_id = $link_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEquals(1, count($results));
			$this->assertEquals(1, $results[0]['deleted']);
		}
		else
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_links` ".
				"WHERE droplet_id = $drop_id ".
				"AND link_id = $link_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEmpty($results);
		}
	}
	
	/**
	* Provides test data for test_delete_place()
	*/
	public function provider_delete_place()
	{
		 return array(
			 array(1, 1, 1, FALSE),
			 array(1, 9, 1, TRUE)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_delete_place
 	*/
	public function test_delete_place($drop_id, $place_id, $account_id, $is_user_defined)
	{
		Model_Droplet::delete_place($drop_id, $place_id, $account_id);
		
		if ( ! $is_user_defined)
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_places` ".
				"WHERE droplet_id = $drop_id ".
				"AND place_id = $place_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEquals(1, count($results));
			$this->assertEquals(1, $results[0]['deleted']);
		}
		else
		{
			$results = DB::query(
				Database::SELECT, 
				"SELECT `deleted` ".
				"FROM `account_droplet_places` ".
				"WHERE droplet_id = $drop_id ".
				"AND place_id = $place_id ".
				"AND account_id = $account_id "
			)->execute()->as_array();
				
			$this->assertEmpty($results);
		}
	}
	
	/**
	* Provides test data for test_get_comments()
	*/
	public function provider_get_comments()
	{
		return array(
			// Drop with comments
			array(1, 
				array(
					array(
						'id' => '1',
						'droplet_id' => '1',
						'comment_text' => 'Good stuff',
						'deleted' => false,
						'identity_user_id' => '3',
						'identity_name' => 'user1 name',
						'identity_email' => 'user1@example.com',
						'date_added' => 'Nov 20, 2012 05:44 UTC',
						'identity_avatar' => Swiftriver_Users::gravatar('user1@example.com', 80)
					),
			 	)
			),
			// Drop without comments
			array(2, array()),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_comments
 	*/
	public function test_get_comments($drop_id, $expected)
	{
		$this->assertEquals($expected, Model_Droplet::get_comments($drop_id));
	}
}