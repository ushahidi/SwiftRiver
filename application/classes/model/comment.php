<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Comments
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Comment extends ORM
{
	/**
	 * A comment belongs to an bucket and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'bucket' => array(),
		'user' => array()
		);

	/**
	 * Validation for comments
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('comment_title', 'not_empty')
			->rule('comment_title', 'min_length', array(':value', 3))
			->rule('comment_title', 'max_length', array(':value', 255))
			->rule('comment_content', 'not_empty');
	}
	
	/**
	 * Overload saving to perform additional functions on the comment
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time comments only
		if ($this->loaded() === FALSE)
		{
			// Save the date the comment was first added
			$this->comment_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->comment_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}		
}
