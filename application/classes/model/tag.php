<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
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
class Model_Tag extends ORM
{
	/**
	 * A tag has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplets',
			'through' => 'droplets_tags'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_tags'
			),
		);

	/**
	 * Overload saving to perform additional functions on the tag
	 */
	public function save(Validation $validation = NULL)
	{
		// Ensure Tag Goes In as Lower Case
		$this->tag = strtolower($this->tag);

		// Ensure Tag Type Goes In as Lower Case
		$this->tag_type = strtolower($this->tag_type);

		// Do this for first time items only
		if ($this->loaded() === FALSE)
		{
			// Save the date the tag was first added
			$this->tag_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
	
	/**
	 * Checks if a given tag already exists
	 *
	 * @param string $tag_name Name of the tag
	 * @param bool $save Optionally saves the tag if does not exist
	 * @return mixed Model_Tag if the tag exists, FALSE otherwise
	 */
	public static function get_tag_by_name($tag_name, $save = FALSE)
	{
		$result = ORM::factory('tag')->where('tag', '=', $tag_name)->find();
		if ($result->loaded())
		{
			return $result;
		}
		elseif ( ! $result->loaded() AND $save == TRUE)
		{
			// Save the tag
			$orm_tag = new Model_Tag;
			$orm_tag->tag  =$tag_name;
			// $orm_tag->tag_source = '';
			$orm_tag->tag_date_add = date('Y-m-d H:i:s', time());
			
			return $orm_tag->save();
		}
		else
		{
			return FALSE;
		}
	}
}
