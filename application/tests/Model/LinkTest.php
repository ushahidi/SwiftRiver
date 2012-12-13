<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Link tests
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
class Model_LinkTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'links');
	
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
		$this->assertEquals($expected_start, Model_Link::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'links'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$link = ORM::factory("Link");
		$link->url = 'http://fancy.url.example.com';
		$link->save();
		
		$this->assertEquals(md5('http://fancy.url.example.com'), $link->hash);
	}
	
	/**
	* Provides test data for test_get_link_by_url()
	*/
	public function provider_get_link_by_url()
	{
		 return array(
			 // Existing link
			 array('http://www.bbc.co.uk/news/in-pictures-20166740#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa', FALSE, 5),
			 // Non existing link without save
			 array('http://non.existing.url.example.com', FALSE, NULL),
			 // Non existing link with save
			 array('http://create.non.existing.url.example.com', TRUE, 99),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_link_by_url
 	*/
	public function test_get_link_by_url($url, $save, $expected_id)
	{
		$link = Model_Link::get_link_by_url($url, $save);
		
		if (isset($expected_id))
		{
			$expected = ORM::factory('Link', $expected_id);
			$this->assertEquals($expected->as_array(), $link->as_array());
		}
		else
		{
			$this->assertFalse($link->loaded());
		}
	}
	
	/**
	* Provides test data for test_get_links()
	*/
	public function provider_get_links()
	{
		 return array(
			 // No links array in drop
			 array(array(), array()),
			 // Links array in drop for existing link
			 array(
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'url' => 'http://www.bbc.co.uk/news/world-asia-20337183#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa',
			 				 )
			 			 )
			 		 )
			 	 ), 
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'id' => 8,
			 					 'url' => 'http://www.bbc.co.uk/news/world-asia-20337183#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 // Links array for a new link
			 array(
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'url' => 'http://create.me.com',
			 				 )
			 			 )
			 		 )
			 	 ), 
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'id' => 99,
			 					 'url' => 'http://create.me.com',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 // Links array with existing and new link
			 array(
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'url' => 'http://www.bbc.co.uk/news/world-asia-20337183#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa',
			 				 ),
			 				 array(
			 					 'url' => 'http://create.me.too.com/',
			 				 )
			 			 )
			 		 ),
			 	 ), 
			 	 array(
			 		 array(
			 			 'links' => array(
			 				 array(
			 					 'id' => 8,
			 					 'url' => 'http://www.bbc.co.uk/news/world-asia-20337183#sa-ns_mchannel=rss&ns_source=PublicRSS20-sa',
			 				 ),
			 				 array(
			 					 'id' => 99,
			 					 'url' => 'http://create.me.too.com/',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_links
 	*/
	public function test_get_links($drop, $expected)
	{
		Model_Link::get_links($drop);
		$this->assertEquals($expected, $drop);
	}
 }
