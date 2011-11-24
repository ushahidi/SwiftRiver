<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * TagThe.net Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class tag_the_net_init {
	
	public function __construct()
	{	
		// Hook into routing
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'extractor'));
	}

	public function extractor()
	{
		try
		{
			// Get the droplet
			$droplet = Swiftriver_Event::$data;
			$content = $droplet['droplet_content'];

			// Connection to TagTheNet
			$tag_url = 'http://tagthe.net/api/?text='.urlencode($content);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $tag_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
			$data = curl_exec($ch);
			curl_close($ch);

			$tags = array();

			if ($xml = @simplexml_load_string($data))
			{
				$data = $xml->xpath("//dim");

				// Get the extracted Entities
				foreach($data as $entities)
				{
					foreach ($entities->item as $entity)
					{
						$tags[] = $entity;
					}
				}
			}

			$droplet['tags'] = $tags;

			// Return the droplet with tags attached
			Swiftriver_Event::$data = $droplet;
		}
		catch (Exception $e)
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}
}

new tag_the_net_init;