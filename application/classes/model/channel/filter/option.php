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
		$channel_filter = ORM::factory('channel_filter', $this->channel_filter_id);

		$option_data = array(
			'user_id' => $channel_filter->river->account->user_id,
			'channel' => $channel_filter->channel,
			'channel_option' => $this->key
		);

		// Update the quota usage
		Model_User_Quota::update_quota_usage($option_data);

		// Check the quota for this channel option
		$ret = parent::save();
		
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

		$option_data = array(
			'user_id' => $this->channel_filter->river->account->user_id,
			'channel' => $this->channel_filter->channel,
			'channel_option' => $this->key
		);

		// Update quota usage
		Model_User_Quota::update_quota_usage($option_data, FALSE);

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
