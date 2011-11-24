<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Plugins Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Plugins {
	
	/**
	 * Load all active plugins into the Kohana system
	 *
	 * @return	void
	 */
	public static function load()
	{
		$active_plugins = ORM::factory("plugin")
			->where("plugin_enabled", "=", 1)
			->find_all();
		
		$plugin_entries = array();
		foreach ($active_plugins as $plugin)
		{
			$plugin_entries[$plugin->plugin_path] = PLUGINPATH.$plugin->plugin_path;
		}
		
		// Add the plugin entries to the list of Kohana modules
		Kohana::modules(Kohana::modules() + $plugin_entries);
	}
	
	/**
	 * Unload a plugin from the Kohana System
	 *
	 * @return	void
	 */
	public static function unload()
	{
		
	}

	/**
	 * Find and load all the plugin config files regardless of whether
	 * they've been loaded by the Kohana system or not
	 *
	 * @return	array $configs
	 */
	public static function load_configs()
	{
		$d = dir(PLUGINPATH)
		 	or die("Failed opening directory $dir for reading");
		
		$directories = array();
		$configs = array();
		while ( ($entry = $d->read()) !== FALSE )
		{
			// Don't include hidden folders
			if ($entry[0] != '.') $directories[$entry] = FALSE;
		}
		
		// Cycle through each plugin directory and load
		// config files
		foreach ($directories as $dir => $found)
		{
			$file = PLUGINPATH.$dir.'/config/plugin.php';
			if ( file_exists($file) )
			{
				$config_array = include($file);
				if (is_array($config_array))
				{
					$configs = array_merge($configs, $config_array);
				}
			}
		}
		
		return $configs;
	}

	/**
	 * Find all the active plugins with channel services
	 *
	 * @return	array $channels
	 */
	public static function channels()
	{
		$channels = array();
		// Load each plugins configs and find out which plugins
		// are Feed Service Providers
		$plugins = Kohana::$config->load('plugin');
		foreach($plugins as $key => $plugin)
		{
			if (isset($plugin['channel']) AND $plugin['channel'] == TRUE)
			{
				$active = ORM::factory('plugin')
					->where('plugin_path', '=', $key)
					->where('plugin_enabled', '=', '1')
					->find();
				if ($active->loaded())
				{
					$channels[$key]['name'] = $plugin['name'];

					// Get Channel Options
					if (isset($plugin['channel_options']) AND is_array($plugin['channel_options']))
					{
						$channels[$key]['options'] = $plugin['channel_options'];
					}
				}
			}
		}

		return $channels;
	}

	/**
	 * Determine if a plugin has settings options
	 *
	 * @return bool 
	 */
	public static function has_settings($plugin = NULL)
	{
		if ($plugin)
		{
			$all_plugin_configs = Kohana::$config->load('plugin');
			$plugin_configs = $all_plugin_configs->get($plugin);
			if ( is_array($plugin_configs) 
				AND isset($plugin_configs['settings']) 
				AND $plugin_configs['settings'] == TRUE )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Determine if a plugin has feed service options
	 *
	 * @return bool 
	 */
	public static function has_service($plugin = NULL)
	{
		if ($plugin)
		{
			$all_plugin_configs = Kohana::$config->load('plugin');
			$plugin_configs = $all_plugin_configs->get($plugin);
			if ( is_array($plugin_configs) 
				AND isset($plugin_configs['service']) 
				AND $plugin_configs['service'] == TRUE )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}	
}
