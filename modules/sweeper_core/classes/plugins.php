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
	
	public static function unload()
	{
		
	}
	
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
	
}