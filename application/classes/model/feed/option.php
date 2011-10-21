<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Feed_Options
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
class Model_Feed_Option extends ORM
{
	/**
	 * A feeds_option belongs to a feed
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'feed' => array()
		);
}