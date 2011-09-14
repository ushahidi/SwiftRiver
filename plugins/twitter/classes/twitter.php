<?php defined('SYSPATH') OR die('No direct access allowed.');

class Twitter {
	
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
			CREATE TABLE IF NOT EXISTS `".self::$table_prefix."twitter_settings`
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
		
		// Twitter Key
		$setting = ORM::factory('twitter_setting')
			->where('key', '=', 'consumer_key')
			->find();
		$setting->key = 'consumer_key';
		$setting->save();
		
		// Twitter Secret
		$setting = ORM::factory('twitter_setting')
			->where('key', '=', 'consumer_secret')
			->find();
		$setting->key = 'consumer_secret';
		$setting->save();
	}
}