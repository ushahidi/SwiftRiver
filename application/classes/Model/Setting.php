<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
		$value = NULL;
		$cache_key = 'site_setting_'.$key;
		if ( ! ($value = Cache::instance()->get($cache_key, FALSE)))
		{
			$setting = ORM::factory('Setting')
			               ->where('key', '=', $key)
			               ->find();
			$value = $setting->value;
			
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
		$settings = ORM::factory('Setting')
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
	 */
	public static function update_setting($key, $new_value)
	{
		$setting = ORM::factory('Setting')
		               ->where('key', '=', $key)
		               ->find();
		if ( ! $setting->loaded())
		{
			$setting->key = $key;
		}
		$setting->value = $new_value;
		$setting->save();
		Cache::instance()->set('site_setting_'.$key, $new_value, 86400 + rand(0,86400));
	}

	/**
	 * Updates one or more settings parameters
	 * @param array $settings A key-value array of the settings key
	 * and its corresponding value
	 */
	public static function update_settings($settings)
	{
		foreach ($settings as $key => $value)
		{
			self::update_setting($key, $value);
		}
	}
}
