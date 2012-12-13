<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account_Droplet_LinkTest tests
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
class Model_Account_Droplet_LinkTest extends Unittest_Database_TestCase {
	
	/**
	 * @var  array  data used by this test
	 */
	protected $datasets = array('seq', 'links', 'account_droplet_links');
	
	/**
	* Provides test data for test_get_link()
	*/
	public function provider_get_link()
	{
		return array(
			// Existing account drop link
			array("http://www.bbc.co.uk/nature/20273855", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'link_id' => 3
					)
				)
			),
			// New account drop link
			array("http://create.me.please.com", 1, 1, 
				array(
					array(
						'account_id' => 1,
						'droplet_id' => 1,
						'link_id' => 99
					)
				)
			)
		);
	}
	
 	/**
 	* @test
 	* @dataProvider provider_get_link
 	*/
	public function test_get_link($url, $droplet_id, $account_id, $expected)
	{
		Model_Account_Droplet_Link::get_link($url, $droplet_id, $account_id);
		
		$results = DB::query(
			Database::SELECT, 
			"SELECT `account_id`, `droplet_id`, `link_id` ".
			"FROM `account_droplet_links` `adl`, `links` `l` ".
			"WHERE adl.link_id = l.id ".
			"AND `l`.`url` = '$url' ".
			"AND `droplet_id` = '$droplet_id' ".
			"AND `account_id` = '$account_id'; "
		)->execute()->as_array();
		
		$this->assertEquals(1, count($results));
		$this->assertEquals($expected, $results);
	}
 }

