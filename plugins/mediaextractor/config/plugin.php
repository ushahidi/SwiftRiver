<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Image Extractor Plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Plugin Configs
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

return array(
	//same name as plugin folder
	'mediaextractor' => array(
		'name'			=> 'Media Extractor',
		'description'	=> 'Extract image, video and audio entities from content',
		'author'		=> 'David Kobia',
		'email'			=> 'david@ushahidi.com',
		'version'		=> '0.1.0',
		'settings'		=> FALSE,	// Plugin has settings
		'dependencies'	=> array(
			'core' => array(
				'min' => '0.2.0',
				'max' => '10.0.0',
			),
			'plugins' => array()	// unique plugin names
		)
	),
);