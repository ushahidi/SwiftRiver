<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Plugins Helper Class
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
class Swiftriver_Plugins {
	
	/**
	 * List of plugins configured as channels
	 * @var array
	 */
	private static $channels = array();

	/**
	 * Registry for plugin installers and their respective callbacks
	 * @var array
	 */
	protected static $_installers = array();

	/**
	 * Register an installer
	 *
	 * @param   string   plugin namespace
	 * @param   array    http://php.net/callback
	 * @return  void
	 */
	public static function register($plugin, $callback)
	{
		self::$_installers[$plugin] = $callback;
	}
	
	/**
	 * Load all active plugins into the Kohana system
	 *
	 * @return	void
	 */
	public static function load()
	{
		if ( ! ($plugin_entries = Cache::instance()->get('site_plugin_entries', FALSE)))
		{
			$plugin_entries = array();
			
			$active_plugins = ORM::factory("plugin")
				->where("plugin_enabled", "=", 1)
				->find_all();

			foreach ($active_plugins as $plugin)
			{
				$plugin_path = PLUGINPATH.$plugin->plugin_path;
				if ( ! is_dir(realpath($plugin_path.DIRECTORY_SEPARATOR)))
				{
					// Plugin no longer exists. Delete.
					$plugin->delete();
					continue;
				}

				$plugin_entries[$plugin->plugin_path] = $plugin_path;
			}
			
			Cache::instance()->set('site_plugin_entries', $plugin_entries, 86400 + rand(0,86400));
		}
		else
		{
			foreach ($plugin_entries as $plugin_path => $value)
			{
				if ( ! is_dir(realpath(PLUGINPATH.$plugin_path.DIRECTORY_SEPARATOR)))
				{
					// Plugin no longer exists. Remove from DB.
					$plugin = ORM::factory("plugin")
						->where("plugin_path", "=", $plugin_path)
						->find();
					$plugin->delete();

					// Log this event
					Kohana::$log->add(Log::INFO, "Plugin directory for ':plugin' not found. Deleting from DB",
						array(':plugin' => $plugin_path));
					
					unset($plugin_entries[$plugin_path]);
					continue;
				}
			}

			// Update Cache
			Cache::instance()->set('site_plugin_entries', $plugin_entries, 86400 + rand(0,86400));
		}
		
		
		// Add the plugin entries to the list of Kohana modules
		Kohana::modules(Kohana::modules() + $plugin_entries);
	}

	/**
	 * Run Plugin Installer Script if Available
	 *
	 * @param   string   plugin namespace
	 * @return  bool
	 */
	public static function install($plugin)
	{
		// Dynamically load the new module into the system
		Kohana::modules(array_merge(array(PLUGINPATH.$plugin), Kohana::modules()));
		
		// Does the plugin have an installer script?
		if ( isset(self::$_installers[$plugin]) )
		{
			try
			{
				call_user_func(self::$_installers[$plugin]);
				return TRUE;
			}
			catch (Exception $e)
			{
				Kohana::$log->add(Log::ERROR, "Could not execute plugin installer callback function :callback", 
					array(':callback' => self::$_installers[$plugin]));

				return FALSE;
			}
		}

		return FALSE;
	}
	
	/**
	 * Find and load all the plugin config files regardless of whether
	 * they've been loaded by the Kohana system or not
	 *
	 * @return	array $configs
	 */
	public static function load_configs()
	{
		$d = @dir(PLUGINPATH); 
		
		// Trap for errors
		if ( ! $d)
		{
			Kohana::$log->add(Log::ERROR, "Failed opening directory :dir for reading", 
			    array(':dir' => PLUGINPATH));
			
			return FALSE;
		}
		
		$directories = array();
		$configs = array();
		while (($entry = $d->read()) !== FALSE )
		{
			// Don't include hidden folders (only applies to UNIX-like systems)
			if ($entry[0] == '.') continue;
			{
				$directories[$entry] = FALSE;
			}
		}
		
		// Cycle through each plugin directory and load config files
		foreach ($directories as $dir => $found)
		{
			$file = PLUGINPATH.$dir.'/config/plugin.php';
			if (file_exists($file))
			{
				$config_array = include $file;
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
	 * @param   bool $reload Reloads the channel configs when TRUE
	 * @return	array $channels
	 */
	public static function channels($reload = FALSE)
	{
		if ( ! $reload AND ! empty(self::$channels))
			return self::$channels;

		self::$channels = array();
		
		// Load the plugin configs and fetch only those that 
		// have the channel property set to TRUE
		$config_plugins = Kohana::$config->load('plugin');
		$active_plugins = ORM::factory('plugin')
		                     ->where('plugin_enabled', '=', '1')
		                     ->find_all();
		foreach ($active_plugins as $active_plugin)
		{
			if ( ! isset($config_plugins[$active_plugin->plugin_path]))
				continue;

			$plugin_config = $config_plugins[$active_plugin->plugin_path];
			if (isset($plugin_config['channel']) AND $plugin_config['channel'] == TRUE )
			{
				$channel_config = self::_validate_channel_plugin_config($plugin_config);
				if ( ! isset($channel_config))
					continue;

				self::$channels[] = array(
					'name' => $plugin_config['name'],
					'channel' => $active_plugin->plugin_path,
					'options' => $channel_config
				);
			}
		}

		return self::$channels;
	}

	/**
	 * Gets the configuration data of a channel
	 *
	 * @param string $channel Name of the channel
	 * @return mixed An array with the channel configuration on succeed, FALSE otherwise
	 */
	public static function get_channel_config($channel_name)
	{
		if (empty(self::$channels))
		{
			// Load the channels
			self::channels();
		}
		
		foreach (self::$channels as $channel)
		{
			if ($channel['channel'] == $channel_name)
				return $channel;
		}

		return FALSE;
	}
	
	/**
	 * Validates the configuration of a channel plugin
	 *
	 * @param array $plugin Plugin configuration
	 * @param array $channel_config Channel config parameters to store
	 * @return mixed array if the config for the channel plugin is in order, 
	 *     NULL otherwise
	 */
	private static function _validate_channel_plugin_config($plugin)
	{
		if (! isset($plugin['channel_options']) OR ! is_array($plugin['channel_options']))
		{
			// Log the config error
			Kohana::$log->add(Log::ERROR, ":plugin plugin config error. "
			    . "'channel_options' MUST be an array.", array(':plugin' => $plugin['name']));
			
			return NULL;
		}
		
		foreach ($plugin['channel_options'] as $key =>  & $option)
		{
			$option['key'] = $key;
			if (isset($option['placeholder']))
			{
				$option['placeholder'] = htmlentities($option['placeholder']);
			}
			
			if(isset($option['group_options'])) {
				// Has group options, set the key for each
				foreach ($option['group_options'] as $key =>  & $opt)
				{
					$opt['key'] = $key;
				}
			}
		}

		return $plugin['channel_options'];
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
			if
			(
				is_array($plugin_configs) 
				AND isset($plugin_configs['settings']) 
				AND $plugin_configs['settings'] == TRUE
			)
			{
				return TRUE;
			}
		}
		return FALSE;
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
			if
			(
				is_array($plugin_configs)
				AND isset($plugin_configs['service']) 
				AND $plugin_configs['service'] == TRUE
			)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Validates the option data for the specified plugin. If an item
	 * fails the validation test, it's removed from the option data
	 *
	 * @param array $options Plugin option data to validate
	 * @param string $plugin Name of the plugin
	 */
	public static function validate_channel_options(array & $options, $channel)
	{
		// Verify that the channel exists in the submitted data
		if ( ! isset($options[$channel]))
			return;
			
		// Check if the plugin uses grouped options
		$configs = self::channels();
		$has_group = array_key_exists('group', $configs[$channel]);
		
		// Validation for each of the options
		foreach ($options[$channel] as $index => $option)
		{
			foreach ($option as $key => $values)
			{
				if ($has_group)
				{
					$valid = TRUE;
					// Grouped option items - validate each item in the group
					foreach ($values as $option_key => $items)
					{
						if (empty($items['value']))
						{
							$valid = FALSE;
						}
					}
					
					// If all the options did not pass the validation checks,
					// remove the group from the list of options
					if ( ! $valid)
					{
						unset ($options[$channel][$index][$key]);
					}
				}
				else
				{
					// Single option item
					if (empty($values['value']))
					{
						// Remove the option item from the list of options
						unset ($options[$channel][$index][$key]);
					}
				}
			}
		}
	}
	
}
