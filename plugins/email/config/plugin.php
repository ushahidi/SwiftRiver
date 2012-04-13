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
	'email' => array(
		'name'			=> 'Email',
		'description'	=> 'Adds the Email service to SwiftRiver.',
		'author'		=> 'David Kobia',
		// Email of the author the plugin
		'email'			=> 'david@ushahidi.com',
		// Version the plugin
		'version'		=> '0.1.0',

		'channel'		=> TRUE,
		
		// Fields for an email account
		'channel_options' => array(
			'email' => array(
				
				'label' => __('Email Account'),	
						
				'type' => 'group',
			
				'group_options' => array(
					// Server host
					'host' => array(
						'label' => __('Server Host'),
						'type' => 'text',
					),
					
					// Server port
					'port' => array(
						'label' => __('Server Port'),
						'type' => 'text'
					),
					// Email server username
					'username' => array(
						'label' => __('Username'),
						'type' => 'text'
					),
					// Password for the email account above
					'password' => array(
						'label' => __('Password'), 
						'type' => 'password'
					),
					'servertype' => array(
						'label' => __('Server Type (IMAP/POP3)'),
						'type' => 'select',
						'values' => array('IMAP', 'POP')
					),
					'ssl' => array(
						'label' => __('SSL Enabled?'),
						'type' => 'select',
						'values' => array('Yes', 'No')
					)
				)
			)
		),
		
		// Plugin dependencies
		'dependencies'	=> array(
			'core' => array(
				'min' => '0.2.0',
				'max' => '10.0.0',
			),
		)	
	),
);