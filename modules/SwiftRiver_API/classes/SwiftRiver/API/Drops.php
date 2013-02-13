<?php defined('SYSPATH') OR die('No direct access allowed. ');
/**
 * SwiftRiver Drops API
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class SwiftRiver_API_Drops extends SwiftRiver_API {
	
	/**
	 * Adds a tag to the specified drop via the API
	 *
	 * @param  int     $drop_id ID of the drop
	 * @param  string  $tag  Name of the tag to be added
	 */
	public function add_drop_tag($drop_id, $tag)
	{
		
	}
	
	/**
	 * Addsa a place to the specified drop via the API
	 *
	 * @param  int     $drop_id
	 * @param  string  $place_name
	 */
	public function add_drop_place($drop_id, $place_name)
	{
		
	}
	
	/**
	 * Adds a link to the specified drop via the API
	 *
	 * @param   int     $drop_id
	 * @param   string  $url
	 */
	public function add_drop_link($drop_id, $url)
	{
		
	}
	
	/**
	 * Deletes the tag with the specified tag_id from the drop
	 * with the id specified drop_id 
	 *
	 * @param   int $drop_id
	 * @param   int  $tag_id
	 * @return  bool
	 */
	public function delete_drop_tag($drop_id, $tag_id)
	{
		
	}
	
	/**
	 * Deletes the place with the specified placed_id from the drop
	 * with the id specified in drop_id
	 *
	 * @param  int  $drop_id
	 * @param  int  $place_id
	 * @return bool
	 */
	public function delete_drop_place($drop_id, $place_id)
	{
		
	}
	
	/**
	 * Deletes the link specified in $link_id from the drop
	 * specified in $drop_id
	 *
	 * @param  int   $drop_id
	 * @param  int   $link_id
	 * @return bool 
	 */
	public function delete_drop_link($drop_id, $link_id)
	{
		
	}
}