<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Buckets Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category    Services
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Service_Drop {
	
	/**
	 * Drops API
	 * @var SwiftRiver_API_Drops
	 */
	private $drops_api;
	
	public function __construct($api)
	{
		$this->drops_api = $api->get_drops_api();
	}

	/**
	 * Adds missing properties that are consumed by the UI to each of the
	 * drops in $drops.
	 *
	 * @param array drops
	 */
	public static function format_drops(array & $drops)
	{
		foreach ($drops as & $drop)
		{
			// Buckets
			if (empty($drop['buckets']))
			{
				$drop['buckets'] = array();
			}
			
			// Drop image
			if ( ! array_key_exists('image', $drop))
			{
				$drop['image'] = NULL;
			}
		}
	}
}