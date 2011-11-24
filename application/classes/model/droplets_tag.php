<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Droplets_Tags
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Droplets_Tag extends ORM
{
	/**
	 * A droplet has and belongs to many accounts
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'accounts' => array(
			'model' => 'account',
			'through' => 'accounts_droplets_tags'
		));
}