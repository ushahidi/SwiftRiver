<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Media tests
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
class Model_MediaTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'media');
	
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
		$this->assertEquals($expected_start, Model_Media::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'media'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* Provides test data for test_get_media()
	*/
	public function provider_get_media()
	{
		 return array(
			 // No media array in drop
			 array(array(), array()),
			 // Media array in drop for existing media
			 array(
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
			 					 'url' => 'http://gigaom2.files.wordpress.com/2012/11/img_0145.jpg?w=300&#038;h=225',
			 					 'type' => 'image',
			 				 )
			 			 )
			 		 )
			 	 ), 
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
								 'id' => 6,
			 					 'url' => 'http://gigaom2.files.wordpress.com/2012/11/img_0145.jpg?w=300&#038;h=225',
			 					 'type' => 'image',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 // Media array for a new media
			 array(
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
			 					 'url' => 'http://new.url.com',
			 					 'type' => 'image',
			 				 )
			 			 )
			 		 )
			 	 ), 
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
								 'id' => 99,
			 					 'url' => 'http://new.url.com',
			 					 'type' => 'image',
			 				 )
			 			 )
			 		 )
			 	 )
			 ),
			 // Media array with existing and new media
			 array(
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
			 					 'url' => 'http://gigaom2.files.wordpress.com/2012/11/img_0145.jpg?w=300&#038;h=225',
			 					 'type' => 'image',
			 				 ),
			 				 array(
			 					 'url' => 'http://new.url.com',
			 					 'type' => 'image',
			 				 )
			 			 )
			 		 ),
			 	 ), 
			 	 array(
			 		 array(
			 			 'media' => array(
			 				 array(
								 'id' => 6,
			 					 'url' => 'http://gigaom2.files.wordpress.com/2012/11/img_0145.jpg?w=300&#038;h=225',
			 					 'type' => 'image',
			 				 ),
			 				 array(
								 'id' => 99,
			 					 'url' => 'http://new.url.com',
			 					 'type' => 'image',
			 				 )
			 			 ),
			 		 ),
			 	 )
			 ),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_media
 	*/
	public function test_get_media($drop, $expected)
	{
		Model_Media::get_media($drop);
		$this->assertEquals($expected, $drop);
	}
 }