<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
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
class Model_Tag extends ORM
{
	/**
	 * A tag has many items
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'items' => array(
			'model' => 'item',
			'through' => 'items_tags'
			)
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

		return parent::save();
	}
}