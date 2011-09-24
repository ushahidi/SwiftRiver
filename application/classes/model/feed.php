<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Feeds
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
class Model_Feed extends ORM
{
	/**
	 * A feed has many items
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'items' => array(),
		'feed_options' => array()
		);

	/**
	 * A feed belongs to a project and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'project' => array(),
		'user' => array()
		);

	
	/**
	 * Overload saving to perform additional functions on the feed
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date the feed was first added
			$this->feed_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->feed_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
}