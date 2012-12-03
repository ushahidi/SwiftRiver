<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Account Test
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @group swiftriver
 * @group swiftriver.core
 * @group swiftriver.core.model
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
abstract class Unittest_Database_TestCase extends Kohana_Unittest_Database_TestCase {
	
	 /**
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	 public function getDataSet()
	 {
		 $test_data = Kohana::find_file('tests', 'test-data', 'xml');
		 return $this->createFlatXMLDataSet($test_data);
	 }
	
	 
 	/**
 	 * Tests will use test database
 	 * @var string
 	 */
 	protected $_database_connection = 'unittest';
	
	
	/**
	 * SetUp test enviroment
	 */
	public function setUp()
	{
		parent::setUp();
		
		$this->old_default = Database::$default;
		Database::$default = 'unittest';
	}
	
	
	/**
	 * Tear down environment
	 */
	public function tearDown()
	{
		parent::tearDown();
		
		Database::$default = $this->old_default;
	}

 }