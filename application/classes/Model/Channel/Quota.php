<?php defined('SYSPATH') or die('No direct script access');

/**
 * Model for the channel_quotas table
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

class Model_Channel_Quota extends ORM {
	

	/**
	 * Validation rules to be run before save/update operations
	 * @return array
	 */
	public function rules()
	{
		return array(
			'quota' => array(
				array('not_empty'),
				array('digit')
			)
		);
	}

	/**
	 * @return array
	 */
	public static function get_quotas_array()
	{
		$quotas = array();

		// Load the list of channels
		$channels = Swiftriver_Plugins::channels();

		// Ge the channel quota entries from the DB
		$db_channel_quotas = self::get_db_channel_quotas();

		// Populate the return value
		foreach ($channels as $channel)
		{
			$entries = array();

			$channel_options = $channel['options'];
			foreach ($channel_options as $option => $options)
			{
				if ($options['type'] !== 'text')
					continue;
					
				// Initialize the quota id and channel quota
				$default = isset($options['default_quota']) ? $options['default_quota'] : 0;
				list($quota_id, $quota) = array(NULL, $default);

				// Canonical channel name
				$channel_name = $channel['channel'];

				// Get the quota entries stored in the DB
				if (array_key_exists($channel_name, $db_channel_quotas))
				{
					if (array_key_exists($option, $db_channel_quotas[$channel_name]))
					{
						$quota_id = $db_channel_quotas[$channel_name][$option]['id'];
						$quota = $db_channel_quotas[$channel_name][$option]['quota'];
					}
				}

				$entries[] = array(
					'id' => $quota_id,
					'channel' => $channel['channel'],
					'label' => $options['label'],
					'channel_option' => $option,
					'quota' => $quota
				);
			}

			$quotas[] = array('channel_name' => $channel['name'], 'quota_options' => $entries);
		}

		return $quotas;
	}

	/**
	 * Gets the quota for a specific channel option
	 *
	 * @param  string  $channel Name of the channel
	 * @param  string  $option  Channel option
	 */
	public static function get_channel_quota($channel, $option)
	{
		$quota_orm = ORM::factory('Channel_Quota')
		    ->where('channel', '=', $channel)
		    ->where('channel_option', '=', $option)
		    ->find();
		
		$channel_config = Swiftriver_Plugins::get_channel_config($channel);
		$default = 0;
		if (isset($channel_config['options'][$option]) AND isset($channel_config['options'][$option]['default_quota']))
		{
			$default = $channel_config['options'][$option]['default_quota'];
		}

		return $quota_orm->loaded() ? $quota_orm->quota : $default;
	}

	/**
	 * Given an array of the quota properties, adds a chanel quota entry
	 *
	 * @param  array $quota_dat key-value array of the quota properties
	 * @return Model_Channel_Quota
	 */
	public static function add_quota($quota_data)
	{
		$quota_orm = new Model_Channel_Quota();
		$quota_orm->channel = $quota_data['channel'];
		$quota_orm->channel_option = $quota_data['channel_option'];
		$quota_orm->quota = $quota_data['quota'];
		return $quota_orm->save();
	}

	/**
	 * Gets the channel quotas from the DB with the otions grouped per
	 * channel
	 *
	 * @return array
	 */
	private static function get_db_channel_quotas()
	{
		$db_quotas = array();
		foreach (ORM::factory('Channel_Quota')->find_all() as $quota)
		{
			if ( ! array_key_exists($quota->channel, $db_quotas))
			{
				$db_quotas[$quota->channel] = array();
			}

			$db_quotas[$quota->channel][$quota->channel_option] = array(
				'id' => $quota->id,
				'quota' => $quota->quota
			);
		}
		return $db_quotas;
	}
}