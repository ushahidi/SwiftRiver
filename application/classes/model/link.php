<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Links
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Link extends ORM
{
	/**
	 * A link has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'droplets_links'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_links'
			),				
		);

	/**
	 * Overload saving to perform additional functions on the link
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time links only
		if ($this->loaded() === FALSE)
		{
			// Save the date the link was first added
			$this->link_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}		
}
