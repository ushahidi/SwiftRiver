<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Links Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Links {

	/**
	 * Extract URL's from text
	 *
	 * @param   string $text
	 * @return	array $urls
	 */
	public static function extract_links($text)
	{
		$urls = array();

		// Regex for matching URLs
		// Credits to John Gruber - http://daringfireball.net/2010/07/improved_regex_for_matching_urls
		$pattern = "(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]"
		    . "{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(("
		    . "[^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))";

		// Begin matching
		if (preg_match_all("/".$pattern."/is", $text, $matches))
		{
			foreach ($matches[0] as $key => $url)
			{
				if ( ! in_array($url, $urls))
				{
					$urls[] = self::full($url);
				}
			}
		}

		return $urls;
	}


	/**
	 * Take short url's and determine full urls using cURL
	 *
	 * @param   string $url Short URL
	 * @return  string $url Full/Expanded URL
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

			if (empty($headers) )
			{
				return $url;
			}

			if ( ! isset($headers['Location']))
			{
				return $url;
			}
			$url = $headers['Location'];
			
			// If an Array is returned for redirects
			// Return the last item in the array
			return is_array($url)? end($url) :  $url;
		}

		return $url;
	}
}
