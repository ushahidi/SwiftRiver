<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Identities
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
class Model_Identity extends ORM
{
	/**
	 * An identity belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array()
		);

	/**
	 * An identity has and belongs to many sources
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'sources' => array(
			'model' => 'sources',
			'through' => 'identities_sources'
			)
		);

	/**
	 * An has many droplets
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array('droplets' => array());

	/**
	 * Overload saving to perform additional functions on the identity
	 */
	public function save(Validation $validation = NULL)
	{
		// Swiftriver Plugin Hook
		Event::run('swiftriver.identity.pre_save', $this);

		// Do this for first time identities only
		if ($this->loaded() === FALSE)
		{
			// Save the date the identity was first added
			$this->identity_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->identity_date_modified = date("Y-m-d H:i:s", time());
		}		

		return parent::save();
	}
}