<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Navs Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_Navs {

	/**
	 * River Navs
	 * 
	 * @param obj $river - the loaded river object
	 * @param string $active - the active menu
	 * @return	array $nav
	 */
	public static function river($river = NULL, $active = NULL)
	{
		$nav = array();

		if ($river)
		{
			// List
			$nav[] = array(
				'id' => 'list-navigation-link',
				'active' => 'list',
				'url' => '/list',
				'label' => __('List')
			);

			// Drops
			$nav[] = array(
				'id' => 'drops-navigation-link',
				'active' => 'drops',
				'url' => '/drops',
				'label' => __('Drops')
			);

			// Photos
			$nav[] = array(
				'id' => 'photos-navigation-link',
				'active' => 'photos',
				'url' => '/photos',
				'label' => __('Photos')
			);
			
			// Photos
			$nav[] = array(
				'id' => 'trends-navigation-link',
				'active' => 'trends',
				'url' => '/trends',
				'label' => __('Trends')
			);			
		}

		// SwiftRiver Plugin Hook -- Add Nav Items
		Swiftriver_Event::run('swiftriver.river.nav', $nav);

		return $nav;
	}
}