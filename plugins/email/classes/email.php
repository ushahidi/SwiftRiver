<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Email plugin installer
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Plugins
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Email {
	
	/**
	 * Database table prefix
	 * @var string
	 */
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
			  `user_id` int(11) usigned NOT NULL,
			  `server_host` varchar(100) NOT NULL,
			  `server_type` varchar(5) NOT NULL,
			  `server_ssl` tinyint(1) NOT NULL DEFAULT 0,
			  `mailbox_name` varchar(50) NOT NULL DEFAULT 'INBOX',
			  `username` varchar(60) NOT NULL,
			  `password` varchar(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";

		$db->query(NULL, $create, TRUE);
	}
}