<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Config for Email Plugin
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
	'email' => array(
		// Name of the plugin
		'name'			=> 'Email',
		// Description of the plugin
		'description'	=> 'Adds an Email channel to SwiftRiver.',
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
			'username' => array(
				// Label of the option (*tip use i18n __('xxx'))
				'label' => __('Username'),
				// Option type (text, textarea, password, radio)
				'type' => 'text',
				// Available values for this option
				'values' => array()
			),
			'password' => array(
				'label' => __('Password'),
				'type' => 'password',
				'values' => array()
			),
			'server_host' => array(
				'label' => __('Host'),
				'type' => 'text',
				'values' => array()
			),
			'server_port' => array(
				'label' => __('Port'),
				'type' => 'text',
				'values' => array()
			),
			'server_type' => array(
				'label' => __('Type (IMAP/POP3)'),
				'type' => 'text',
				'values' => array()
			),
			'server_ssl' => array(
				'label' => __('SSL'),
				'type' => 'select',
				'values' => array(
					'yes' => __('yes'),
					'no' => __('no')
				)
			),					
		),
		// Group options?
		'channel_group_options' => TRUE,
		// Group Key and Label
		'channel_group' => array(
			'key' => 'email',
			'label'=> __('Email Account')
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