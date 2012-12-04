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
		$compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
		
		foreach ($this->datasets as $dataset)
		{
			$file = Kohana::find_file('tests', 'test-data/'.$dataset, 'xml');
			$ds = $this->createFlatXmlDataSet($file);
			$compositeDs->addDataSet($ds);
		}
	    return $compositeDs;
	}
	
	/**
	 * Tests will use test database
	 * @var string
	 */
	 protected $_database_connection = 'unittest';

}