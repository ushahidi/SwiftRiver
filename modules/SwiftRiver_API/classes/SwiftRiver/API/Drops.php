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
	 * @param  int     $id ID of the drop
	 * @param  string  $tag  Name of the tag to be added
	 * @param  string   $tag_type Type of the tag being added
	 */
	public function add_drop_tag($id, $tag, $tag_type)
	{
		$parameters = array(
			'tag' => $tag,
			'tag_type' => $tag_type
		);

		return $this->post('/drops/'.$id.'/tags', $parameters);
	}
	
	/**
	 * Adds a a place to the specified drop via the API
	 *
	 * @param  int     $id ID of the drop
	 * @param  string  $name Name of the place
	 * @param  flaot   latitude
	 * @param  float   longitude
	 * @return array
	 */
	public function add_drop_place($id, $name, $latitude, $longitude)
	{
		return $this->post('/drops/'.$id.'/places', array(
			'name' => $place_name,
			'latitude' => $latitude,
			'longitude' => $longitude
		));
	}
	
	/**
	 * Adds a link to the specified drop via the API
	 *
	 * @param   int     $id
	 * @param   string  $url
	 * @return  array
	 */
	public function add_drop_link($id, $url)
	{
		return $this->post('/drops/'.$id.'/links', array('url' => $url));
	}
	
	/**
	 * Deletes the tag with the specified tag_id from the drop
	 * specified in $id
	 *
	 * @param   int  $id
	 * @param   int  $tag_id
	 * @return  bool
	 */
	public function delete_drop_tag($id, $tag_id)
	{
		return $this->delete('/drops/'.$id.'/tags/'.$tag_id);
	}
	
	/**
	 * Deletes the place with the specified placed_id from the drop
	 * specified in $id
	 *
	 * @param  int  $id
	 * @param  int  $place_id
	 * @return bool
	 */
	public function delete_drop_place($id, $place_id)
	{
		return $this->delete('/drops/'.$id.'/places/'.$place_id);
	}
	
	/**
	 * Deletes the link specified in $link_id from the drop
	 * specified in $drop_id
	 *
	 * @param  int   $id
	 * @param  int   $link_id
	 * @return bool 
	 */
	public function delete_drop_link($id, $link_id)
	{
		return $this->delete('/drops/'.$id.'/links/'.$link_id);
	}
	
	/**
	 * Gets and returns the comments for the drop with the specified $id
	 *
	 * @param  int   $id
	 * @param  array $params
	 * @return array
	 */
	public function get_drop_comments($id, $params = array())
	{
		return $this->get('/drops/'.$id.'/comments');
	}
	
	/**
	 * Adds the comment in $comment_text to the drop with the id specified
	 * in $id
	 *
	 * @param  int     $id
	 * @param  string  $comment_text
	 * @return array
	 */
	public function add_drop_comment($id, $comment_text)
	{
		return $this->post('/drops/'.$id.'/comments', array('comment_text' => $comment_text));
	}

	/**
	 * Deletes the comment with the specified $comment_id from the drop
	 * in $id
	 *
	 * @param  int $id          ID of the drop
	 * @param  int  $comment_id ID of the comment
	 * @return bool
	 */
	public function delete_drop_comment($id, $comment_id)
	{
		return $this->delete('/drops/'.$id.'/comments/'.$comment_id);
	}
}