<?php defined('SYSPATH') or die('No direct script access');

/**
 * Miscellaneous helper functions
 *
 * @author     Ushahidi Team
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  (c) 2008-2011 Ushahidi Inc - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Swiftriver_Util {
	
	/**
	 * Initializes and returns a Gearman client
	 *
	 * @return GearmanClient
	 */
	public static function init_gearman_client()
	{
		$client = new GearmanClient();
		
		// TODO Fetch the list of servers from a config file
		$client->addServer();
		
		// Return
		return $client;
	}
}
?>