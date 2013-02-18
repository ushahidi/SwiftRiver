<?php defined('SYSPATH') or die('No direct script access.');
/**
 * River Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage  Exceptions
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Service_River {
	
	private $api = NULL;
	
	public function __construct($api)
	{
		$this->api = $api;
	}
	
	/**
	 * Return a river array with subscription and collaboration
	 * status populated for $querying_account
	 *
	 * @param Model_User $user
	 * @param Model_User $querying_account
	 * @return array
	 *
	 */
	public static function get_array($river, $querying_account) {
		$river['url'] = self::get_base_url($river);
		$river['expired'] = FALSE;
		$river['is_owner'] = $river['account']['id'] == $querying_account['id'];
		
		// Is the querying account collaborating on the river?
		$river['collaborator'] = FALSE;
		foreach($querying_account['collaborating_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				$river['is_owner'] = TRUE;
				$river['collaborator'] = TRUE;
			}
		}
		
		// Is the querying account following the river?
		$river['subscribed'] = FALSE;
		foreach($querying_account['following_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				$river['subscribed'] = TRUE;
			}
		}
		
		// Get display name from channel plugins
		if (isset($river['channels']))
		{
			foreach($river['channels'] as & $channel) {
				$channel['display_name'] = '';
				$channel['parameters'] = json_decode($channel['parameters'], TRUE);
				Swiftriver_Event::run('swiftriver.channel.format', $channel);
			}
		}
		
		
		return $river;
	}
	
	/**
	 * Return the Account array for the given account path
	 *
	 * @return	Array
	 */
	public function get_river_by_id($id, $querying_account)
	{
		$river = $this->api->get_rivers_api()->get_river_by_id($id);
		
		
		return $this->get_array($river, $querying_account);
	}
	
	/**
	 * Return URL to the given River
	 *
	 * @return	Array
	 */
	public static function get_base_url($river)
	{
		return URL::site($river['account']['account_path'].'/river/'.URL::title($river['name']));
	}
	
	
	/**
	 * Create a river
	 *
	 * @param   string  $river_name
	 * @return Array
	 */
	public function create_river_from_array($river_array) {
		
		$river_array = $this->api->get_rivers_api()->create_river(
			$river_array['name'], 
			$river_array['description'], 
			$river_array['public']
		);
		
		$river_array['url'] = self::get_base_url($river_array);
		$river_array['is_owner'] = TRUE;
		$river_array['collaborator'] = FALSE;
		$river_array['subscribed'] = FALSE;
		
		return $river_array;
	}
	
	/**
	 * Add a channal to the given river
	 *
	 * @return Array
	 */
	public function create_channel_from_array($river_id, $channel_array)
	{
		Swiftriver_Event::run('swiftriver.channel.validate', $channel_array);
		
		$channel_array = $this->api->get_rivers_api()->create_channel(
					$river_id, 
					$channel_array["channel"], 
					json_encode($channel_array["parameters"])
				);
				
		$channel_array['parameters'] = json_decode($channel_array['parameters'], TRUE);
		$channel_array['display_name'] = '';
		Swiftriver_Event::run('swiftriver.channel.format', $channel_array);
		return $channel_array;
	}
}