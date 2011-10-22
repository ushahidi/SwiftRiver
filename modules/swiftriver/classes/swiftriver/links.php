<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Links Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Links {

	/**
	 * Extract URL's from text
	 * @param   string $text
	 * @return	array $urls
	 */
	public static function extract($text = NULL)
	{
		if ($text)
		{
			$regex = '(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?';
			if ( preg_match_all("/".$regex."/is", $text, $match) )
			{
				if ( isset($match[0]) AND count($match[0]) )
				{
					return $match[0];
				}
			}			
		}

		return array();
	}

	/**
	 * Take short url's and determine full urls
	 * using cURL
	 * @param   string $url - short url
	 * @return	string $url - long url
	 */
	public static function full($url = NULL)
	{
		if ($url)
		{
			try
			{
				$headers = get_headers($url,1);
			}
			catch (Exception $e)
			{
				// Some kind of error
				// Abandon and return original url
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
				return $url;
			}

			if ( empty($headers) )
			{
				return $url;
			}

			if ( ! isset($headers['Location']) )
			{
				return $url;
			}
			$url = $headers['Location'];
			
			// If an Array is returned for redirects
			// Return the last item in the array
			if (is_array($url))
			{
				return end($url);
			}
			else
			{
				return $url;
			}
		}

		return $url;
	}
}