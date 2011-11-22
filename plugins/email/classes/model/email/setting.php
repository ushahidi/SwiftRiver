<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Email Settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://source.swiftly.org
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Email_Setting extends ORM
{
	/**
	 * Validation for email settings
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('username', 'not_empty')
			->rule('password', 'not_empty')
			->rule('server_host', 'not_empty')
			->rule('server_port', 'not_empty')
			->rule('server_host_type', 'not_empty')
			->rule('server_ssl', 'not_empty');
	}
}