<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filter_Options
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
class Model_Channel_Filter_Option extends ORM
{
	/**
	 * A channel_filter_option belongs to a channel_filter
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'channel_filter' => array()
		);
}