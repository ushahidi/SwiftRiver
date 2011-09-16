<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Twitter Plugin
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
	//same name as plugin folder
	'twitter' => array(
		'name'			=> 'Twitter',
		'description'	=> 'Adds the twitter service to Sweeper.',
		'author'		=> 'David Kobia',
		'email'			=> 'david@ushahidi.com',
		'version'		=> '0.1.0',
		'settings'		=> TRUE,	// Plugin has settings
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
			'hashtag' => array(
				'name' => 'Twitter Hash Tag',
				'fields' => array(
					'hashtag' => __('HashTag (e.g. #ushahidi)') 		// Field and Label
				),
			),
			'user' => array(
				'name' => 'Twitter User',
				'fields' => array(
					'user' => __('Username (e.g. @ushahidi)')			// Field and Label
				),
			),			
			'keywords' => array(
				'name' => 'Twitter Keywords',
				'fields' => array(
					'keywords' => __('Keywords (Separate with commas)')
				),
			),
			'location' => array(
				'name' => 'Twitter Tweet Location',
				'fields' => array(
					'latitude' => __('Latitude'),
					'longitude' => __('Longitude'),
					'place' => __('Place Name (e.g. Nairobi, Kenya)')
				),
			)
		)
	),
);