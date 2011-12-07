<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Link Extractor Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class LinkExtractor_Init {
	
	public function __construct()
	{	
		// Hook into routing
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'filter'));
	}

	public function filter()
	{
		try
		{
			// Get the droplet content
			$droplet_arr = & Swiftriver_Event::$data;
			
			$droplet = ORM::factory('droplet', $droplet_arr['id']);
			
			//FIXME: This significantly slows down post processing
			//TODO: Create a dedicated stream for droplets with links...
			$links = Swiftriver_Links::extract($droplet->droplet_content);
			
			foreach ($links as $link) {
			    Kohana::$log->add(Log::DEBUG, $link);
			    Kohana::$log->write();			    
			}
			Model_Droplet::add_links($droplet, $links);
		}
		catch (Exception $e) //FIXME: Catch specific exceptions...
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}
}

new LinkExtractor_Init;