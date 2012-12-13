<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account_Droplet_TagTest tests
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
class Model_Account_Droplet_TagTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'tags', 'account_droplet_tags');
	
	/**
	* Provides test data for test_get_tag()
	*/
	public function provider_get_tag()
	{
		return array(
			// Existing account drop tag
			array("Custom Tag", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'tag_id' => 11
					)
				)
			),
			// New account drop tag
			array("Another Custom Tag", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'tag_id' => 99
					)
				)
			)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_tag
 	*/
	public function test_get_tag($tag, $droplet_id, $account_id, $expected)
	{
		Model_Account_Droplet_Tag::get_tag($tag, $droplet_id, $account_id);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `account_id`, `droplet_id`, `tag_id` ".
			"FROM `account_droplet_tags` `adt`, `tags` `t` ".
			"WHERE adt.tag_id = t.id ".
			"AND `t`.`tag_type` = 'user_generated' ".
			"AND `t`.`tag` = '$tag' ".
			"AND `droplet_id` = '$droplet_id' ".
			"AND `account_id` = '$account_id'; "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected, $results);
	}
 }

