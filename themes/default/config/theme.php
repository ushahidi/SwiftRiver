<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Default Theme
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Theme Configs
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
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