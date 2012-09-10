<?php defined('SYSPATH') or die('No direct script access');

/**
 * Model for the user_quotas table
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Model_User_Quota extends ORM {

	/**
	 * Updates a users quota usage
	 * @param  array $option
	 * @param  bool $add When TRUE, increments the no. of options used
	 */
	public static function update_quota_usage($option, $add = TRUE)
	{
		// Get the quota entry		
		$quota_orm = ORM::factory('user_quota')
		   ->where('user_id', '=', $option['user_id'])
		   ->where('channel', '=', $option['channel'])
		   ->where('channel_option', '=', $option['channel_option'])
		   ->find();

		// Create a new quota entry when add = TRUE
		if ( ! $quota_orm->loaded() AND $add)
		{
			$quota_orm = new Model_User_Quota();
			$quota_orm->user_id = $option['user_id'];
			$quota_orm->channel = $option['channel'];
			$quota_orm->channel_option = $option['channel_option'];
			$quota_orm->quota_usage = 1;
			$quota_orm->save();
		}

		if ($quota_orm->loaded())
		{
			if ($add)
			{
				// Get the limits
				$option_quota = Model_Channel_Quota::get_channel_quota(
					$option['channel'], $option['channel_option']);

				if ($option_quota === 0 OR ($quota_orm->quota_usage < $option_quota))
				{
					$quota_orm->quota_usage += 1;
					$quota_orm->save();
				}
				else
				{
					// Quota exhausted, throw exception
					throw new HTTP_Exception_400(__("You have exhausted your quota for :channel",
						array(":channel" => $option['channel'])));
				}
			}
			else
			{
				$quota_orm->quota_usage -= 1;
				$quota_orm->save();
			}
		}
	}

}