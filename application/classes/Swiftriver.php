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
		$theme = Swiftriver::get_setting('site_theme');

		if (isset($theme) AND $theme != "default")
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
		I18n::$lang = Swiftriver::get_setting('site_locale');
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
	
	/**
	 * Get a single setting value
	 *
	 * @param string $key
	 * @return string Value for the key
	 */
	public static function get_setting($key)
	{
		$value = NULL;
		$cache_key = 'site_setting_'.$key;
		if ( ! ($value = Cache::instance()->get($cache_key, FALSE)))
		{
			$value = Kohana::$config->load('site')->get($key);
			
			Cache::instance()->set($cache_key, $value, 86400 + rand(0,86400));
		}
			
		return $value;
	}

	/**
	 * Given an array of keys, returns an an array of the key-value pairs from the db
	 *
	 * @param array $setting_keys Array of keys to be fetched
	 * @return Array hash of the key value pairs from the db
	 */
	public static function get_settings($setting_keys)
	{
		if (empty($setting_keys) OR ! is_array($setting_keys))
			return NULL;
        
		$settings_array = array();
		foreach ($setting_keys as $key)
		{
			$settings_array[$key] = get_setting($key);
		}
		
		return $settings_array;
	}

	/**
	 * Creates and returns the base view for rendering error pages
	 * Error handlers that use this method must set the $content
	 * property of the view
	 *
	 * @return    View
	 */
	public static function get_base_error_view()
	{
		$view = View::factory('template/layout')
			->set('footer', View::factory('template/footer'))
			->bind('header', $header);
		
		// Header
		// Params for the <head> section
		$dashboard_url =  URL::site('/', TRUE);
		$_head_params = array(
			'meta' => "",
			'js'=> "",
			'css' => "",
			'messages' => json_encode(array()),
			'dashboard_url' => $dashboard_url,
		);
		
		$header = View::factory('template/header')
			->set('show_nav', TRUE)
			->set('site_name', Swiftriver::get_setting('site_name'))
			->set($_head_params)
			->bind('nav_header', $nav_header);
		
		// Navigation header
		$nav_header = View::factory('template/nav/header')
			->set('user', NULL)
			->set('anonymous', FALSE)
			->set('dashboard_url', $dashboard_url);
		
		return $view;
	}

}
