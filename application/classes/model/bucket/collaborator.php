<?php defined('SYSPATH') or die('No direct script access'); 
/**
 * Model for bucket_collaborators
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Bucket_Collaborator extends ORM {
	
	/**
	 * Many-to-one relationship definition
	 * @var array
	 */
	protected $_belongs_to = array(
		'bucket' => array(),
		'user' => array()
	);
}
?>