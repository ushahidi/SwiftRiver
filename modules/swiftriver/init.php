<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Sweeper Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

/**
 * Activate Enabled Plugins
 */
Swiftriver_Plugins::load();

/**
 * Load Active Theme
 * First, load default theme!
 */
Kohana::modules(array_merge(array('themes/default' => THEMEPATH.'default',), Kohana::modules()));

$theme = ORM::factory('setting')
	->where('key', '=', 'site_theme')
	->find();
if ($theme->loaded() AND $theme->value
	AND $theme->value != "default")
{
	Kohana::modules(array_merge(array
	(
		'themes/'.$theme->value => THEMEPATH.$theme->value,
	), Kohana::modules()));
}

// Clean up
unset($active_plugins, $theme);