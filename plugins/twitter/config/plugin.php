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
	// Unique identifier of the plugin
	// ** same name as the plugin folder
	'twitter' => array(
		// Name of the plugin
		'name'			=> 'Twitter',
		// Description of the plugin
		'description'	=> 'Adds a Twitter channel to SwiftRiver.',
		// Author of the plugin
		'author'		=> 'David Kobia',
		// Email of the author the plugin
		'email'			=> 'david@ushahidi.com',
		// Version the plugin
		'version'		=> '0.1.0',
		// Is plugin a channel?
		'channel'		=> TRUE,
		// Does plugin have a crawler?
		'crawler'		=> TRUE,
		// Array of available channel options
		'channel_options' => array(
			// Channel option with type
			'keyword' => array(
				// Label of the option (*tip use i18n __('xxx'))
				'label' => __('Keyword'),
				// Option type (text, textarea, password, radio)
				'type' => 'text',
				// Available values for this option
				'values' => array()
			),
			'person' => array(
				'label' => __('Person'),
				'type' => 'text',
				'values' => array()
			),
			'place' => array(
				'label' => __('Place'),
				'type' => 'text',
				'values' => array()
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