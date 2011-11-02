<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Snapshots
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Channel_Snapshot extends ORM
{
	/**
	 * A snapshot has many snapshot_options
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array('snapshot_options' => array());

	/**
	 * A channel_filter belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('account' => array());
}
