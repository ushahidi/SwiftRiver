<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Buckets Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     Swiftriver - http://github.com/ushahidi/Swiftriver_v2
 * @category    Services
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Service_Drop {
	
	/**
	 * Drops API
	 * @var SwiftRiver_API_Drops
	 */
	private $drops_api;
	
	public function __construct($api)
	{
		$this->drops_api = $api->get_drops_api();
	}
	
	/**
	 * Adds the tag specified in $tag_name to the drop specified
	 * in $drop_id
	 *
	 * @param  int     $drop_id
	 * @param  string  $tag_name
	 */
	public function add_drop_tag($drop_id, $tag_name)
	{
		return $this->drops_api->add_drop_tag($drop_id, $tag_name);
	}
	
	/**
	 * Deletes the tag specified in $tag_id from the drop
	 * with the specified $drop_id
	 *
	 * @param   int $drop_id
	 * @param   int $drop_id
	 */
	public function delete_drop_tag($drop_id, $tag_id)
	{
		return $this->drops_api->delete_drop_tag($drop_id, $tag_id);
	}
	
	/**
	 * Adds the place specified in place_name to the drop specified
	 * in drop_id
	 *
	 * @param  int     $drop_id
	 * @param  string  $place_ame
	 */
	public function add_drop_place($drop_id, $place_name)
	{
		return $this->drops_api->add_drop_place($drop_id, $place_name);
	}
	
	/**
	 * Deltes the place in place_id from the drop specified in drop_id
	 *
	 * @param  int     $drop_id
	 * @param  string  $place_id
	 */
	public function delete_drop_place($drop_id, $place_id)
	{
		return $this->drops_api->delete_drop_place($drop_id, $place_id);
	}

	/**
	 * Adds the link specified in $url to the drop specified in
	 * $drop_id
	 *
	 * @param  int     $drop_id
	 * @param  string  $url
	 */
	public function add_drop_link($drop_id, $url)
	{
		return $this->drops_api->add_drop_link($drop_id, $url);
	}

	/**
	 * Deletes the link specified in $link_id from the drop specified
	 * in $drop_id
	 *
	 * @param  int  $drop_id
	 * @param  int  $link_id
	 */
	public function delete_drop_link($drop_id, $link_id)
	{
		return $this->drops_api->delete_drop_link($drop_id, $link_id);
	}
	
	/**
	 * Gets and returns the comments for the specified drop
	 *
	 * @param  int $drop_id
	 * @return array
	 */
	public function get_drop_comments($drop_id)
	{
		return $this->drops_api->get_drop_comments($drop_id);
	}

	/**
	 * Gets and returns the drop comments whose id is greater
	 * than the since_id
	 *
	 * @param   int $drop_id
	 * @param   int $since_id
	 * @return  array
	 */
	public function get_drop_comments_since_id($drop_id, $since_id)
	{
		$params = array("since_id" => $since_id);
		return $this->drops_api->get_drop_comments($drop_id, $since_id);
	}
	
	/**
	 * Adds the comment in $comment_text to the drop with specified
	 * in $drop_id
	 *
	 * @param  int     $drop_id
	 * @param  string  $comment_text
	 * @return array
	 */
	public function add_drop_comment($drop_id, $comment_text)
	{
		return $this->drops_api->add_drop_comment($drop_id, $comment_text);
	}
	
	
}