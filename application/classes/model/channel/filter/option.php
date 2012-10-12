<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filter_Options
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Channel_Filter_Option extends ORM {

	/**
	 * A channel_filter_option belongs to a channel_filter
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'channel_filter' => array()
		);
	
	/**
	 * Overload saving to perform additional functions on the channel_filter
	 */
	public function save(Validation $validation = NULL)
	{
		$original_values = $this->original_values();
		$channel_filter = $this->channel_filter;
		$account = $this->channel_filter->river->account;
		
		// Lock the relevant row in the quotas table
		Database::instance()->query(NULL, 'START TRANSACTION');
		$account->account_channel_quotas
		 		->get_remaining_quota($account->id, $channel_filter->channel, $this->key);
		
		// Update quota usage
		$option_data = json_decode($this->value, TRUE);
		if (isset($option_data['quota_usage']))
		{
			$account_id = $account->id;
			$quota_used = (int)$option_data['quota_usage'];
			
			// In case of an update, get previous quota usage
			if (isset($original_values['value']))
			{
				$original_option_data = json_decode($original_values['value'], TRUE);
				if (isset($original_option_data['quota_usage']))
				{
					$quota_used -= (int) $original_option_data['quota_usage'];
				}
			}
			
			Model_Account_Channel_Quota::increase_quota_usage($account_id, $channel_filter->channel, $this->key, $quota_used);
		}
		
		// Check that still within quota
		$quota_remaining = $account->account_channel_quotas
									->get_remaining_quota($account->id, $channel_filter->channel, $this->key);
		if ( ! ($quota_remaining >= 0))
		{
			throw new Swiftriver_Exception_Channel_Option(__('Channel option quota exceeded'));
		}

		// Check the quota for this channel option
		$ret = parent::save();
		Database::instance()->query(NULL, 'COMMIT');
		
		// Run post_save events
		Swiftriver_Event::run('swiftriver.channel.option.post_save', $this);
		
		return $ret;
	}

	/**
	 * Overrides the default behaviour to perform
	 * extra tasks before removing the channel filter
	 * entry 
	 */
	public function delete()
	{
		Swiftriver_Event::run('swiftriver.channel.option.pre_delete', $this);

		// Update quota usage
		$option_data = json_decode($this->value, TRUE);
		if (isset($option_data['quota_usage']))
		{
			$account_id = $this->channel_filter->river->account->id;
			$quota_used = (int)$option_data['quota_usage'];
			Model_Account_Channel_Quota::decrease_quota_usage($account_id, $this->channel_filter->channel, $this->key, $quota_used);
		}

		// Delete the filter option
		parent::delete();
	}

	/**
	 * Parses the "value" column of the channel filter option and returns it 
	 * as an array
	 *
	 * @return array
	 */
	public function get_option_as_array()
	{
		// Decode the JSON string for the options
		$options =  json_decode($this->value, TRUE);
		
		return array($this->key => $options);
	}
}
