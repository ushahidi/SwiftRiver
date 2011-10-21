<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Items/Stories (Pivot)
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
class Model_Item_Story extends ORM
{
	/**
	 * Item_Story is a pivot for items and stories
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'item' => array(),
		'story' => array()
		);
}