<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Items
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
class Model_Item extends ORM
{
	/**
	 * A feed has many links, locations, stories and tags
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'links' => array(),
		'locations' => array(
			'model' => 'location',
			'through' => 'item_location'
			),
		'stories' => array(
			'model' => 'story',
			'through' => 'item_story'
			),
		'tags' => array(
			'model' => 'story',
			'through' => 'item_tag'
			)
		);
		
	/**
	 * An item belongs to a project, a feed, a source and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'project' => array(),
		'feed' => array(),
		'source' => array(),
		'user' => array()
		);
}