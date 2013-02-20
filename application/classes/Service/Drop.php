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
	 * @param  int     drop_id
	 * @param  string  tag_name
	 * @param  string  tag_type
	 */
	public function add_drop_tag($drop_id, $tag_name, $tag_type)
	{
		return $this->drops_api->add_drop_tag($drop_id, $tag_name, $tag_type);
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
	public function get_drop_comments($drop_id, $since_id = NULL)
	{
		$comments = $this->drops_api->get_drop_comments($drop_id, $since_id);

		foreach ($comments as & $comment)
		{
			$comment['comment_text'] = Markdown::instance()->transform($comment['comment_text']);
			$comment['account']['avatar'] = Swiftriver_Users::gravatar($comment['account']['email'], 55);
		}

		return $comments;
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
		$comment = $this->drops_api->add_drop_comment($drop_id, $comment_text);
		$comment['account']['avatar'] = Swiftriver_Users::gravatar($comment['account']['email'], 55);
		return $comment;
	}
	
	public function delete_drop_comment($drop_id, $comment_id)
	{
		return $this->drops_api->delete_drop_comment($drop_id, $comment_id);
	}

	/**
	 * Adds missing properties that are consumed by the UI to each of the
	 * drops in $drops.
	 *
	 * @param array drops
	 */
	public static function marshall_drops(array & $drops)
	{
		foreach ($drops as & $drop)
		{
			// Buckets
			if (empty($drop['buckets']))
			{
				$drop['buckets'] = array();
			}
			
			// Drop image
			if ( ! array_key_exists('image', $drop))
			{
				$drop['image'] = NULL;
			}
		}
	}
}