<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Stories
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
class Model_Story extends ORM
{
	/**
	 * A story has and belongs to many items and attachments
	 * A story has many discussions
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'discussions' => array(),
		'items' => array(
			'model' => 'item',
			'through' => 'items_stories'
			),
		'attachments' => array(
			'model' => 'attachment',
			'through' => 'attachments_stories'
			)			
		);
	
	/**
	 * A story belongs to a project and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'project' => array(),
		'user' => array()
		);

	/**
	 * Validation for stories
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('story_title', 'not_empty')
			->rule('story_title', 'min_length', array(':value', 3))
			->rule('story_title', 'max_length', array(':value', 255))
			->rule('story_summary', 'max_length', array(':value', 255));
	}

	/**
	 * Overload saving to perform additional functions on the story
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date the story was first added
			$this->story_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->story_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}	
}