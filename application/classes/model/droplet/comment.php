<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Model_Droplet_Comment
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Droplet_Comment extends ORM {
	
	protected $_belongs_to = array(
		'droplet' => array(),
		'user' => array()
	);
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
    protected $_created_column = array('column' => 'date_added', 'format' => 'Y-m-d H:i:s');

}
?>