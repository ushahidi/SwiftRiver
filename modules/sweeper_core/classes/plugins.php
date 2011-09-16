<?php
/**
 * Plugins Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Plugins {
	
	public static function init()
	{
		
	}
	
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
		foreach ($active_plugins as $plugin)
		{
			Kohana::modules(array_merge(Kohana::modules(), array(
				$plugin->plugin_path => PLUGINPATH.$plugin->plugin_path
			)));
		}
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
	 * Find all the active plugins with feed services
	 *
	 * @return	array $services
	 */
	public static function services()
	{
		$services = array();
		// Load each plugins configs and find out which plugins
		// are Feed Service Providers
		$plugins = Kohana::$config->load('plugin');
		foreach($plugins as $key => $plugin)
		{
			if (isset($plugin['service']) AND $plugin['service'] == TRUE)
			{
				$active = ORM::factory('plugin')
					->where('plugin_path', '=', $key)
					->where('plugin_enabled', '=', '1')
					->find();
				if ($active->loaded())
				{
					$services[$key] = $plugin['name'];
				}
			}
		}

		return $services;
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

	/**
	 * Find and return the service options available for a feed
	 * service.
	 *
	 * @return array/bool
	 */
	public static function get_service_options($plugin = NULL)
	{
		if ($plugin)
		{
			$all_plugin_configs = Kohana::$config->load('plugin');
			$plugin_configs = $all_plugin_configs->get($plugin);
			if ( is_array($plugin_configs) 
				AND isset($plugin_configs['service']) 
				AND $plugin_configs['service'] == TRUE )
			{
				if ( isset($plugin_configs['service_options']) )
				{
					return $plugin_configs['service_options'];
				}
				else
				{
					return array();
				}
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