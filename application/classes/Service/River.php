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
	
	function __construct($api)
	{
		$this->api = $api;
	}
	
	/**
	 * Return the Account array for the given account path
	 *
	 * @return	Array
	 */
	function get_river_by_id($id, $querying_account)
	{
		$river = $this->api->get_river_by_id($id);
		
		$river['expired'] = FALSE;
		$river['is_owner'] = $river['account']['id'] == $querying_account['id'];
		
		// Is the querying account collaborating on the river?
		$river['is_collaborator'] = FALSE;
		foreach($querying_account['collaborating_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				$river['is_collaborating'] = TRUE;
			}
		}
		
		// Is the querying account following the river?
		$river['is_follower'] = FALSE;
		foreach($querying_account['following_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				$river['is_following'] = TRUE;
			}
		}
		return $river;
	}
	
	/**
	 * Return URL to the given River
	 *
	 * @return	Array
	 */
	static function get_base_url($river)
	{
		return URL::site($river['account']['account_path'].'/river/'.URL::title($river['name']));
	}
}