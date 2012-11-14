<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Comments
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
	 * A comment has many droplet_scores
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'comment_scores' => array()
		);	

	/**
	 * Validation for comments
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('comment_content', 'min_length', array(':value', 3))
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
