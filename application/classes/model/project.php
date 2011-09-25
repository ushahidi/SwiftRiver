<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Projects
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
class Model_Project extends ORM
{
	/**
	 * A project has many items, feeds, stories and projects
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'items' => array(),
		'feeds' => array(),
		'stories' => array(),
		'projects' => array()
		);
	
	/**
	 * A project belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());
	
	/**
	 * Validation for projects
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('project_title', 'not_empty')
			->rule('project_title', 'min_length', array(':value', 3))
			->rule('project_title', 'max_length', array(':value', 255));
	}

	/**
	 * Overload saving to perform additional functions on the project
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date the feed was first added
			$this->project_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->project_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}	
}