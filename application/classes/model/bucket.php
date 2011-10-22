<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Buckets
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
class Model_Bucket extends ORM
{
	/**
	 * A bucket has and belongs to many droplets
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'buckets_droplets'
			)			
		);

	/**
	 * A bucket belongs to an account and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array(),
		'user' => array()
		);		
		
	/**
	 * Overload saving to perform additional functions on the bucket
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time buckets only
		if ($this->loaded() === FALSE)
		{
			// Save the date the bucket was first added
			$this->bucket_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}			
}
