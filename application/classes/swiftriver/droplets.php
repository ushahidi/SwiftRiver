<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Droplets Helper Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Droplets {

	/**
	 *
	 */
	public static function bucket_action($bucket_id, $droplet_id)
	{
		$results = DB::select()
			->from('buckets_droplets')
			->where('bucket_id', '=', $bucket_id)
			->where('droplet_id', '=', $droplet_id)
			->execute();
		
		if (count($results))
		{
			// It exists so we can remove
			return 'remove';
		}
		else
		{
			// Relationship doesn't exist, so we can add
			return 'add';
		}
	}
}