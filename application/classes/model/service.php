<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Services
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Service extends ORM
{
	/**
	 * A service has many sources and many items
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'sources' => array(),
		'items' => array()
		);
	
	/**
	 * A service belongs to a plugin
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('plugin' => array());
}