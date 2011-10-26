<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Link Extractor Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Swiftriver - http://source.swiftly.org
 * @category   Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class link_extractor_init {
	
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
			$droplet = Swiftriver_Event::$data;

			$links = Swiftriver_Links::extract($droplet['droplet_content']);
			foreach ($links as $link)
			{
				$droplet['links'][] = $link;
			}
		}
		catch (Exception $e)
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}
}

new link_extractor_init;