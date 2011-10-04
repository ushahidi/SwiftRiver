<?php defined('SYSPATH') OR die('No direct access allowed.');

class Facebook {
	
	protected static $table_prefix = '';
	
	public static function install()
	{
		$db_config = Kohana::$config->load('database');
		$default = $db_config->get('default');
		self::$table_prefix = $default['table_prefix'];
		
		self::_sql();
	}
	
	private static function _sql()
	{
		$db = Database::instance('default');
		
		$create = "
			CREATE TABLE IF NOT EXISTS `".self::$table_prefix."facebook_settings`
			(
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `key` varchar(100) NOT NULL DEFAULT '',
			  `value` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uniq_key` (`key`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
		$db->query(NULL, $create, true);
		
		// Insert Default Settings Keys
		// Use ORM to prevent issues with unique keys
		
		// Facebook Application ID / API Key
		$setting = ORM::factory('facebook_setting')
			->where('key', '=', 'application_id')
			->find();
		$setting->key = 'application_id';
		$setting->save();

		// Facebook Application Secret
		$setting = ORM::factory('facebook_setting')
			->where('key', '=', 'application_secret')
			->find();
		$setting->key = 'application_secret';
		$setting->save();

		// Access token for offline access to Facebook
		$setting = ORM::factory('facebook_setting')
			->where('key', '=', 'access_token')
			->find();
		$setting->key = 'access_token';
		$setting->save();

		// Unique ID on Facebook of the user Authorizing the Facebook Application
		$setting = ORM::factory('facebook_setting')
			->where('key', '=', 'access_user_id')
			->find();
		$setting->key = 'access_user_id';
		$setting->save();

		// Name of the user Authorizing the Facebook Application
		$setting = ORM::factory('facebook_setting')
			->where('key', '=', 'access_name')
			->find();
		$setting->key = 'access_name';
		$setting->save();
	}
}