<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Utility class for handling media urls
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     Mediaextractor_Filter - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3)
 */

class Mediaextractor_Filter {

	/**
	 * Remove Media that we don't want to use
	 * Consists of things like tracker images
	 * 
	 * @param array $media
	 * @return array $media
	 */
	public static function dejunk($media)
	{
		foreach ($media as $key => $value)
		{
			// Get the list of domains for which to ignore images
			$ignores = Kohana::$config->load('mediaextractor')->get('ignore');
			foreach ($ignores as $ignore)
			{
				if (strpos($value, $ignore) !== FALSE)
				{
					unset($media[$key]);
				}
			}
		}

		return $media;
	}

}