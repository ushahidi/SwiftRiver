<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Tag tests
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
class Model_TagTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'tags');
	
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
		$this->assertEquals($expected_start, Model_Tag::get_ids($num));
		
		$query = DB::query(
			Database::SELECT, 
			"SELECT `id` from `seq` WHERE `name` = 'tags'"
		);
		$this->assertEquals($expected_next, intval($query->execute()->get('id', 0)));
	}
	
	/**
	* @test
	*/
	public function test_save()
	{
		$tag = ORM::factory("Tag");
		$tag->tag = 'Test Tag';
		$tag->tag_type = ' Test Type ';
		$tag->save();
		
		$this->assertEquals('test tag', $tag->tag_canonical);
		$this->assertEquals('test type', $tag->tag_type);
		$this->assertEquals(md5('Test Tagtest type'), $tag->hash);
	}
	
	/**
	* Provides test data for test_get_tag_by_name()
	*/
	public function provider_get_tag_by_name()
	{
		 return array(
			 // Existing tag
			 array(array('tag_name' => 'CIA', 'tag_type' => 'organization'), FALSE, 7),
			 // Non existing tag without save
			 array(array('tag_name' => 'Ninja', 'tag_type' => 'organization'), FALSE, NULL),
			 // Non existing tag with save
			 array(array('tag_name' => 'Ushahidi', 'tag_type' => 'organization'), TRUE, 99),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_tag_by_name
 	*/
	public function test_get_tag_by_name($tag, $save, $expected_id)
	{
		$tag = Model_Tag::get_tag_by_name($tag, $save);
		
		if (isset($expected_id))
		{
			$expected = ORM::factory('Tag', $expected_id);
			$this->assertEquals($expected->as_array(), $tag->as_array());
		}
		else
		{
			$this->assertFalse($tag->loaded());
		}
	}
	
	/**
	* Provides test data for test_get_tags()
	*/
	public function provider_get_tags()
	{
		 return array(
			 // No tags array in drop
			 array(array(), array()),
			 // Tags array in drop for existing tag
			 array(
				 array(
					 array(
						 'tags' => array(
							 array(
								 'tag_name' => 'CIA',
								 'tag_type' => 'organization'
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'tags' => array(
							 array(
								 'id' => 7,
								 'tag_name' => 'CIA',
								 'tag_type' => 'organization'
							 )
						 )
					 )
				 )
			 ),
			 // Tags array for a new tag
			 array(
				 array(
					 array(
						 'tags' => array(
							 array(
								 'tag_name' => 'Ushahidi',
								 'tag_type' => 'organization'
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'tags' => array(
							 array(
								 'id' => 99,
								 'tag_name' => 'Ushahidi',
								 'tag_type' => 'organization'
							 )
						 )
					 )
				 )
			 ),
			 // Tags array with existing and new tag
			 array(
				 array(
					 array(
						 'tags' => array(
							 array(
								 'tag_name' => 'Ushahidi',
								 'tag_type' => 'organization'
							 ),
							 array(
								 'tag_name' => 'Dave Whelan',
								 'tag_type' => 'person'
							 )
						 )
					 )
				 ), 
				 array(
					 array(
						 'tags' => array(
							 array(
								 'id' => 99,
								 'tag_name' => 'Ushahidi',
								 'tag_type' => 'organization'
							 ),
							 array(
								 'id' => 9,
								 'tag_name' => 'Dave Whelan',
								 'tag_type' => 'person'
							 )
						 )
					 )
				 )
			 ),
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_tags
 	*/
	public function test_get_tags($drop, $expected)
	{
		Model_Tag::get_tags($drop);
		$this->assertEquals($expected, $drop);
	}
 }