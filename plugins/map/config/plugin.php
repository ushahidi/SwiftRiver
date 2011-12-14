<?php defined('SYSPATH') OR die('No direct access allowed.');

 /**
  * Config for Email Plugin
  *
  * PHP version 5
  * LICENSE: This source file is subject to GPLv3 license 
  * that is available through the world-wide-web at the following URI:
  * http://www.gnu.org/copyleft/gpl.html
  * @author        Ushahidi Team <team@ushahidi.com> 
  * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
  * @subpackage    Plugin Configs
  * @copyright     Ushahidi - http://www.ushahidi.com
  * @license       http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
  */

 return array(
 	'map' => array(
 		'name'			=> 'Map',
 		'description'	=> 'Visualize droplets in a map',
 		'author'		=> 'Brian Muita',
 		'email'			=> 'brian.muita@ushahidi.com',
 		'version'		=> '1.0.0',
 		'settings'		=> TRUE,
 		'channel'		=> TRUE,
 		
		
 		'dependencies'	=> array(
 			'core' => array(
 				'min' => '0.2.0',
 				'max' => '10.0.0',
 			)
 		)	
 	),
 );   
?>