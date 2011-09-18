<?php defined('SYSPATH') OR die('No direct access allowed.');

class Email {
	
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
			CREATE TABLE IF NOT EXISTS `".self::$table_prefix."email_settings`
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
		
		// Username
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'username')
			->find();
		$setting->key = 'username';
		$setting->save();
		
		// Password
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'password')
			->find();
		$setting->key = 'password';
		$setting->save();

		// Server Host
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'server_host')
			->find();
		$setting->key = 'server_host';
		$setting->save();

		// Server Port
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'server_port')
			->find();
		$setting->key = 'server_port';
		$setting->save();

		// Server Type
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'server_host_type')
			->find();
		$setting->key = 'server_host_type';
		$setting->save();

		// Server SSL
		$setting = ORM::factory('email_setting')
			->where('key', '=', 'server_ssl')
			->find();
		$setting->key = 'server_ssl';
		$setting->save();
	}
}