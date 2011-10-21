<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filters
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
class Model_Channel_Filter extends ORM
{
	/**
	 * A channel_filter has many droplets and channel_filter_options
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(),
		'channel_filter_options' => array()
		);

	/**
	 * A channel_filter belongs to an account and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array(),
		'user' => array()
		);

	
	/**
	 * Overload saving to perform additional functions on the channel_filter
	 */
	public function save(Validation $validation = NULL)
	{

		// Do this for first time channel_filters only
		if ($this->loaded() === FALSE)
		{
			// Save the date the channel_filter was first added
			$this->filter_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->filter_date_modified = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
}