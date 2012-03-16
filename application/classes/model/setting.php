<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Setting extends ORM
{
	/**
	 * Get a single setting's value
	 *
	 * @param string $key
	 * @return string Value for the key
	 */
	public static function get_setting($key)
	{
		$setting = ORM::factory('setting')
		               ->where('key', '=', $key)
		               ->find();
		return $setting->value;		
	}

	/**
	 * Given an array of keys, returns an an array of the key-value pairs from the db
	 *
	 * @param array $setting_keys Array of keys to be fetched
	 * @return Array hash of the key value pairs from the db
	 */
	public static function get_settings($setting_keys)
	{
		$settings = ORM::factory('setting')
		               ->where('key', 'IN', $setting_keys)
		               ->find_all();
		
		$settings_array = array();
		foreach ($settings as $setting)
		{
			$settings_array[$setting->key] = $setting->value;
		}
		
		return $settings_array;
	}
	
	/**
	 * Set a setting
	 *
	 * @param string $key Setting key
	 * @param string $new_value New value for the key
	 * @return Array hash of the key value pairs from the db
	 */
	public static function update_setting($key, $new_value)
	{
		$setting = ORM::factory('setting')
		               ->where('key', '=', $key)
		               ->find();
		if ( ! $setting->loaded())
		{
			$setting->key = $key;
		}
		$setting->value = $new_value;
		$setting->save();
	}
}
