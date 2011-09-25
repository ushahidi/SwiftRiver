<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Discussions
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
class Model_Discussion extends ORM
{
	/**
	 * A discussion belongs to a project, a story, an item and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'project' => array(),
		'story' => array(),
		'item' => array(),
		'user' => array()
		);

	/**
	 * Validation for discussions
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('discussion_title', 'not_empty')
			->rule('discussion_title', 'min_length', array(':value', 3))
			->rule('discussion_title', 'max_length', array(':value', 255))
			->rule('discussion_content', 'not_empty');
	}
	
	/**
	 * Overload saving to perform additional functions on the discussion
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date the discussion was first added
			$this->discussion_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->discussion_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}		
}