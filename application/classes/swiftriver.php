<?php defined('SYSPATH') or die('No direct script access');
/**
 * Initializes the SwiftRiver environment
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage Cookie config
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Swiftriver {

	/**
	 * Default salt value to add to the cookies
	 */
	const DEFAULT_COOKIE_SALT = 'cZjO0Lgfv7QrRGiG3XZJZ7fXuPz0vfcL';

	// Cookie name constants
	const COOKIE_SEARCH_SCOPE = "search_scope";
	const COOKIE_PREVIOUS_SEARCH_SCOPE = "previous_search_scope";
	const COOKIE_SEARCH_ITEM_ID = "search_item_id";

	/**
	 * Application initialization
	 *     - Loads the plugins
	 *     - Sets the cookie configuration
	 */
	public static function init()
	{
		// Set defaule cache configuration
		Cache::$default = Kohana::$config->load('site')->get('default_cache');
		
		try
		{
			$cache = Cache::instance()->get('dummy'.rand(0,99));
		}
		catch (Exception $e)
		{
			// Use the dummy driver
			Cache::$default = 'dummy';
		}
		
		
		// Load the plugins
		Swiftriver_Plugins::load();

		// Add the current default theme to the list of modules
		$theme = Model_Setting::get_setting('site_theme');

		if ($theme != "default")
		{
			Kohana::modules(array_merge(
				array('themes/'.$theme->value => THEMEPATH.$theme->value),
				Kohana::modules()
			));
		}

		// Clean up
		unset ($active_plugins, $theme);

		// Load the cookie configuration
		$cookie_config = Kohana::$config->load('cookie');
		Cookie::$httponly = TRUE;
		Cookie::$salt = $cookie_config->get('salt', Swiftriver::DEFAULT_COOKIE_SALT);
		Cookie::$domain = $cookie_config->get('domain') OR '';
		Cookie::$secure = $cookie_config->get('secure') OR FALSE;
		Cookie::$expiration = $cookie_config->get('expiration') OR 0;

		// Set the default site locale
		I18n::$lang = Model_Setting::get_setting('site_locale');
	}
	
	/**
	 * Returns the CDN url for $file
	 *
	 * @param   string   file name
	 * @return  string
	 */
	public static function get_cdn_url($file)
	{
		$cdn_url = Kohana::$config->load('site')->get('cdn_url');
		if (isset($cdn_url))
		{
			$cdn_dirs = Kohana::$config->load('site')->get('cdn_directories');
			foreach ($cdn_dirs as $dir)
			{
				$file = preg_replace('|^('.$dir.')|', $cdn_url.'/$1', $file);
			}
		}
		
		return $file;
	}

}
