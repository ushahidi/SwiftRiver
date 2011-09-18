<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for RSS Plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Plugin Configs
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

return array(
	'rss' => array(				//same name as plugin folder
		'name'			=> 'RSS',
		'description'	=> 'Adds the RSS/Atom service to Sweeper.',
		'author'		=> 'David Kobia',
		'email'			=> 'david@ushahidi.com',
		'version'		=> '0.1.0',
		'dependencies'	=> array(
			'core' => array(
				'min' => '0.2.0',
				'max' => '10.0.0',
			),
			'plugins' => array()	// unique plugin names
		),

		// Service and Service Options
		'service'		=> TRUE,
		'service_options' => array(
			'url' => array(
				'name' => 'RSS/Atom Feed URL',
				'fields' => array(
					'url' => __('URL (e.g. http://feeds.bbci.co.uk/news/rss.xml)') 		// Field and Label
				),
			)
		)
	),
);