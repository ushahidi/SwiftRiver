<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Default Theme
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Theme Configs
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */

return array
(
	'default' => array //same name as theme folder
	(
		'name'			=> 'Default Theme',
		'author'		=> 'David Kobia',
		'email'			=> 'david@ushahidi.com'
		'version'		=> '0.1.0',
		'dependencies'	=> array
		(
			'core' => array
			(
				'min' => '0.2.0',
			),
		)
	),
);