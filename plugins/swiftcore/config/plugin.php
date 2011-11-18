<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Swiftcore Plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - http://source.swiftly.org
 * @category   Plugins
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

return array(
	'rss' => array(				//same name as plugin folder
		'name'			=> 'Swiftcore',
		'description'	=> 'Extract entities from text using the Swiftcore API',
		'author'		=> 'Emmanuel Kala',
		'email'			=> 'emmanuel@ushahidi.com',
		'version'		=> '0.1.0',
		'service'		=> FALSE,	// Plugin is a service		
		'dependencies'	=> array(
			'core' => array(
				'min' => '0.2.0',
				'max' => '10.0.0',
			),
			'plugins' => array()	// unique plugin names
		)
	),
);