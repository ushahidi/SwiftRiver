<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Media
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Media extends ORM
{
	/**
	 * Media has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'droplets_media'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_media'
			),				
		);
		
	/**
	 * Overload saving to perform additional functions on the media
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time media only
		if ($this->loaded() === FALSE)
		{
			// Save the date the media was first added
			$this->media_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}			
}
