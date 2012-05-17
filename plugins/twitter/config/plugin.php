<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Twitter Plugin
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
	'twitter' => array(
		'name'			=> 'Twitter',
		'description'	=> 'Adds a Twitter channel to SwiftRiver.',
		'author'		=> 'David Kobia',
		'email'			=> 'david@ushahidi.com',
		'version'		=> '0.1.0',
		'channel'       => TRUE,
		'channel_options' => array(
			'keyword' => array(
				'label' => __('Keyword'),
				'type' => 'text',
				'values' => array(),
				'placeholder' => 'E.g. Ushahidi, "African Tech" For multiple keywords, separate each keyword with a ","'
			),
			'user' => array(
				'label' => __('User'),
				'type' => 'text',
				'values' => array(),
				'placeholder' => 'E.g. @ushahidi To add multiple users, seperate each user with a "," e.g. @ushahidi, @crowdmap'
			)
		),
		'dependencies'	=> array(
			'core' => array(
				'min' => '0.2.0',
				'max' => '10.0.0',
			),
			'plugins' => array()	// unique plugin names
		)
	),
);