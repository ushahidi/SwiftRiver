<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Bucket_Comment tests.
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
class Model_Bucket_CommentTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('buckets');
	
	
	/**
	* Provides test data for test_validation()
	*/
	public function provider_validation()
	{
		 return array(
			 // Empty comment
			 array(NULL),
			 // Short comment
			 array('aa'),
		);
	}
	 
	/**
	* @test
	* @dataProvider provider_validation
	*/
	public function test_validation($comment_content)
	{
		$this->setExpectedException('ORM_Validation_Exception');
		
		$comment = ORM::factory("Bucket_Comment");
		$comment->comment_content = $comment_content;
		$comment->save();
	}
	
	/**
	* @test
	*/
	public function test_create_new()
	{
		$comment = Model_Bucket_Comment::create_new('Comment Text', 2, 3);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `bucket_id`, `user_id`, `comment_content` ".
			"FROM `bucket_comments` ".
			"WHERE `bucket_id` = 2 ".
			"AND user_id = 3 "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($results[0]['comment_content'], 'Comment Text');
	}
}