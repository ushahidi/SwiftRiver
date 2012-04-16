<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Navs Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
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
			// Drops
			$nav[] = array(
				'id' => 'drops-navigation-link',
				'active' => ($active == 'drops') ? 'active' : '',
				'url' => '/drops',
				'label' => __('Drops')
			);

			// List
			$nav[] = array(
				'id' => 'list-navigation-link',
				'active' => ($active == 'list') ? 'active' : '',
				'url' => '/list',
				'label' => __('List')
			);
		}

		// Plugins
		Swiftriver_Event::run('swiftriver.river.nav', $nav);

		return $nav;
	}
}