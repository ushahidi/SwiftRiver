<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Media Extractor Init
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

class MediaExtractor_Init {
	
	public function __construct()
	{	
		// Hook into routing
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'filter'));
	}

	/**
	 * Event callback for the swiftriver.droplet.extract_metadata event
	 *
	 * @return void
	 */
	public function filter()
	{
		// Load Simple_HTML_DOM
		$path = Kohana::find_file( 'vendor', 'simple_html_dom/simple_html_dom' );
		if( false === $path ) {
			throw new Kohana_Cache_Exception('Simple_HTML_DOM vendor code not found');
		}
		require_once( $path );

		try
		{
			// Get the droplet content
			$droplet_arr = Swiftriver_Event::$data;
			
			$droplet = ORM::factory('droplet', $droplet_arr['id']);
			
			// Get all the image anchors in the droplet
			$html = str_get_html($droplet->droplet_content);
			$images = array();
			foreach($html->find('img') as $element)
			{
				// We'll start using absolute urls to images soon :)
				//$images[] = url_to_absolute($url, $element->src);
				$images[] = $element->src;
			}
			
			// Remove Images we don't need
			$images = Mediaextractor_Filter::dejunk($images);

			Model_Droplet::add_media($droplet, $images, 'image');
		}
		catch (Exception $e) //FIXME: Catch specific exceptions...
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}

}

new MediaExtractor_Init;