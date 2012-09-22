<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for User Subscriptions
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
class Model_User_Subscription extends ORM
{
	/**
	 * An belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());	
		
	/**
	 * Overload saving to perform additional functions on the subscription
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time subscriptions only
		if ($this->loaded() === FALSE)
		{
			// Save the date the subscription was first added
			$this->subscription_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
}
