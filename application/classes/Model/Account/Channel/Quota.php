<?php defined('SYSPATH') or die('No direct script access');

/**
 * Model for the user_quotas table
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */

class Model_Account_Channel_Quota extends ORM {
	
	protected $_belongs_to = array(
		'account' => array()
	);
	
	/**
	 * Validation rules for comments
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'channel' => array(
				array('not_empty'),
			),
			'channel_option' => array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Create a new quota for the given account
	 *
	 * @param  string  $account_id
	 * @param  string  $channel
	 * @param  string  $option
	 * @param  string  $quota 
	 * @return void
	 */
	public static function create_new($account_id, $channel, $option, $quota = NULL)
	{
		if ( ! isset($quota))
		{
			$quota = Model_Channel_Quota::get_channel_quota($channel, $option);
		}
		
		$channel_quota = ORM::factory("Account_Channel_Quota");
		$channel_quota->account_id = $account_id;
		$channel_quota->channel = $channel;
		$channel_quota->channel_option = $option;
		$channel_quota->quota = $quota;
		$channel_quota->save();
		
		return $channel_quota;
	}
	
	
	/**
	 * Get remaining river quota in the account.
	 *
	 * Obtains an exclusive lock so if called within a transaction, the quota
	 * can be updated.
	 *
	 * @param  string  $account_id
	 * @param  string  $channel
	 * @param  string  $option
	 * @return int
	 */	
	public static function get_remaining_quota($account_id, $channel, $option)
	{
		$quota = Model_Channel_Quota::get_channel_quota($channel, $option);
		$quota_used = 0;

		$query = DB::query(Database::SELECT, "/*ms=master*/select quota, quota_used from account_channel_quotas where account_id = ".$account_id." and channel = '".$channel."' and channel_option = '".$option."' for update;");
		$results = $query->execute();
		
		if ($results->count() > 0) 
		{
			$quota = intval($results->get('quota'));
			$quota_used = intval($results->get('quota_used'));
		}
		else
		{
			self::create_new($account_id, $channel, $option, $quota);
		}
		
		return $quota - $quota_used;
	}
	
	/**
	 * Decrease an account's quota usage
	 *
	 * @param  string  $account_id
	 * @param  string  $channel
	 * @param  string  $option
	 * @param  string  $count 
	 * @return void
	 */
	public static function decrease_quota_usage($account_id, $channel, $option, $count = 1)
	{
		$channel_quota = ORM::factory("Account_Channel_Quota")
							->where("account_id", "=", $account_id)
							->where("channel", "=", $channel)
							->where("channel_option", "=", $option)
							->find();
		
		if ($channel_quota->loaded())
		{
			$channel_quota->quota_used = (int)$channel_quota->quota_used - (int)$count;
			$channel_quota->save();
		}
	}
	
	/**
	 * Increase an account's quota usage
	 *
	 * @param  string  $account_id
	 * @param  string  $channel
	 * @param  string  $option
	 * @param  string  $count 
	 * @return void
	 */
	public static function increase_quota_usage($account_id, $channel, $option, $count = 1)
	{
		$channel_quota = ORM::factory("Account_Channel_Quota")
							->where("account_id", "=", $account_id)
							->where("channel", "=", $channel)
							->where("channel_option", "=", $option)
							->find();
		
		if ( ! $channel_quota->loaded())
		{
			$channel_quota = self::create_new($account_id, $channel, $option);	
		}
		
		$channel_quota->quota_used = (int)$channel_quota->quota_used + (int)$count;
		$channel_quota->save();
	}

}