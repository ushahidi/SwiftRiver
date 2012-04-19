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

	private $links = array();
	private $images = array();
	
	public function __construct()
	{	
		// Load Simple_HTML_DOM
		$path = Kohana::find_file( 'vendor', 'simple_html_dom/simple_html_dom' );
		if( false === $path ) {
			throw new Kohana_Cache_Exception('Simple_HTML_DOM vendor code not found');
		}
		require_once( $path );


		// Hook into routing
		Swiftriver_Event::add('swiftriver.droplet.extract_metadata', array($this, 'parse_media'));
	}

	/**
	 * Event callback for the swiftriver.droplet.extract_metadata event
	 *
	 * @return void
	 */
	public function parse_media()
	{
		$this->links = array();
		$this->images = array();
		
		try
		{
			// Get the droplet content
			$droplet_arr = Swiftriver_Event::$data;
			
			$droplet = ORM::factory('droplet', $droplet_arr['id']);
			
			// 1. Get the links in the droplet
			$this->links = Swiftriver_Links::extract_links($droplet->droplet_content);
			// Remove regular image links
			$this->_remove_images();
			// Remove service image links
			$this->_remove_service_images();
			// Save the links
			Model_Droplet::add_links($droplet, $this->links);



			// 2. Get all the image anchors in the droplet
			$html = str_get_html($droplet->droplet_content);
			foreach($html->find('img') as $element)
			{
				// We'll start using absolute urls to images soon :)
				//$images[] = url_to_absolute($url, $element->src);
				$this->images[] = $element->src;
			}
			
			// Remove Images we don't need
			$this->images = Mediaextractor_Filter::dejunk($this->images);
			// Remove dupes
			$this->images = array_unique($this->images);
			// Save the Images
			Model_Droplet::add_media($droplet, $this->images, 'image');
		}
		catch (Exception $e) //FIXME: Catch specific exceptions...
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}

	/**
	 * Remove image links
	 * 
	 * @return void
	 */
	private function _remove_images()
	{
		if($this->links)
		{		
			foreach ($this->links as $key => $value)
			{
				if( preg_match('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $value, $matches) )
				{
					// Remove from links
					unset($this->links[$key]);

					// Add to images array
					$this->images[] = $value;
				}
			}
		}
	}


	private function _remove_service_images()
	{
		if($this->links)
		{
			foreach($this->links as $key => $value)
			{
				if(stristr($value,'yfrog.com'))
				{
					unset($this->links[$key]);
					$this->images[] = $this->_extractyfrog($value);
				}
				else if(stristr($value,'plixi.com'))
				{
					unset($this->links[$key]);
					$this->images[] = $this->_extractplixi($value);
				}
				else if(stristr($value,'instagr.am'))
				{
					unset($this->links[$key]);
					$this->images[] = $this->_extractinstagram($value);
				}
				else if(stristr($value,'twitpic.com'))
				{
					unset($this->links[$key]);
					$this->images[] = $this->_extracttwitpic($value);
				}
				else if(stristr($value,'flic.kr'))
				{
					unset($this->links[$key]);
					$this->images[] = $this->_extractflickr($value);
				}
			}
		}		
	}

	private function _extractyfrog($link)
	{
		return trim($link,'”."').':iphone';
	}

	private function _extracttwitpic($link)
	{
		$linkparts = explode('/',$link);
		return 'http://twitpic.com/show/large/'.$linkparts[3];
	}

	private function _extractflickr($link)
	{
		$html = file_get_html($link);
		foreach($html->find('img.photo') as $element)
		{
			return $element->src;
		}
	}

	private function _extractinstagram($link)
	{
		$html = file_get_html($link);
		foreach($html->find('img.photo') as $element)
		{
			return $element->src;
		}
	}

	private function _extractplixi($link)
	{
		$html = file_get_html($link);
		foreach($html->find('img[id=photo]') as $element)
		{
			return $element->src;
		}
	}
}

new MediaExtractor_Init;