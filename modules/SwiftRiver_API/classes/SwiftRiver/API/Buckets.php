<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Rivers API
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     SwiftRiver - http://github.com/ushahidi/SwiftRiver
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class SwiftRiver_API_Buckets extends SwiftRiver_API {	
	
/**
	 * Gets and return the bucket with the given id
	 *
	 * @return array
	 */
	public function get_bucket_by_id($bucket_id)
	{
		return $this->get('/buckets/'.$bucket_id);
	}
	
	/**
	 * Gets and returns a list of drops for the bucket with the given id
	 *
	 * @param  bool  $bucket_id ID of the bucket
	 * @param  array $params Parameters for filtering the drops
	 * @return array
	 */
	public function get_drops($bucket_id, $params = array())
	{
		$path = sprintf("/buckets/%d/drops", $bucket_id);
		return $this->get($path, $params);
	}
	
	/**
	 * Gets and returns the list of users collaborating on the bucket
	 * with the specified id
	 *
	 * @return array
	 */
	public function get_collaborators($bucket_id)
	{
		$path = sprintf("/buckets/%d/collaborators", $bucket_id);
		return $this->get($path);
	}
	
	/**
	 * Delete collaborator
	 *
	 * @param   long  $bucket_id
	 * @param   long  $account_id
	 * @return  array
	 */
	public function delete_collaborator($bucket_id, $account_id)
	{
		return $this->delete('/buckets/'.$bucket_id.'/collaborators/'.$account_id);
	}
	
	/**
	 * Add a collaborator
	 *
	 * @param   long  $bucket_id
	 * @param   long  $collaborator_array
	 * @return Array
	 */
	public function add_collaborator($bucket_id, $collaborator_array)
	{
		return $this->post('/buckets/'.$bucket_id.'/collaborators', $collaborator_array);
	}
	
	/**
	 * Adds the drop specified in $drop_id to the bucket specified in $bucket_id
	 *
	 * @param int    bucket_id
	 * @param int    drop_id
	 * @param array  source_data
	 */
	public function add_drop($bucket_id, $drop_id)
	{
		$this->put("/buckets/".$bucket_id."/drops/".$drop_id);
	}
	
	/**
	 * Removes the drop specified in $drop_id from the bucket specified
	 * in $bucket_id
	 *
	 * @param   int  bucket_id
	 * @param   int  drop_id
	 */
	public function delete_drop($bucket_id, $drop_id)
	{
		$this->delete("/buckets/".$bucket_id."/drops/".$drop_id);
	}
	
	/**
	 * Creates a bucket with the specified $bucket_name via the API and
	 * returns an array representing the created bucket
	 *
	 * @param   string bucket_name
	 * @return  array
	 */
	public function create_bucket($bucket_name)
	{
		return $this->post("/buckets", array('name' => $bucket_name));
	}
	
	/**
	 * Deletes the bucket with the specified $bucket_id via the API
	 *
	 * @param  int bucket_id
	 */
	public function delete_bucket($bucket_id)
	{
		$this->delete("/buckets/".$bucket_id);
	}
	
	/**
	 * Sets the properties of the bucket specified in $bucket_id
	 * to the ones in $parameters. The return value is the modified
	 * bucket returned by the API
	 *
	 * @param  int     bucket_id
	 * @param  array   parameters
	 * @return array
	 */
	public function modify_bucket($bucket_id, array $parameters)
	{
		if ( ! array_key_exists('name', $parameters))
		{
			throw new SwiftRiver_API_Exception(__("The 'name' parameter must be specified"));
		}
		return $this->put("/buckets/".$bucket_id, $parameters);
	}

	/**
	 * Verifies whether the account specified in $account_id is following
	 * the bucket specified in $id
	 *
	 * @param  int bucket_id
	 * @param  int account_id
	 * @return bool
	 */	
	public function is_bucket_follower($bucket_id, $account_id)
	{
		try
		{
			$result = $this->get('/buckets/'.$bucket_id.'/followers', array('follower' => $account_id));
		}
		catch (SwiftRiver_API_Exception_NotFound $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Adds the account in $account_id to the list of followers for
	 * the bucket in $bucket_id
	 *
	 * @param  int bucket_id
	 * @param  int account_id
	 */
	public function add_follower($bucket_id, $account_id)
	{
		$this->put('/buckets/'.$bucket_id.'/followers/'.$account_id);
	}
	
	/**
	 * Removes the account in $account_id from the list of followers for
	 * the bucket in $bucket_id
	 *
	 * @param  int bucket_id
	 * @param  int account_id
	 */
	public function delete_follower($bucket_id, $account_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/followers/'.$account_id);
	}

	/**
	 * Adds a tag to a bucket drop
	 *
	 * @param  int   bucket_id
	 * @param  int   drop_id
	 * @param  array tag_data
	 * @return array
	 */
	public function add_drop_tag($bucket_id, $drop_id, $tag_data)
	{
		return $this->post('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/tags', $tag_data);
	}

	/**
	 * Removes a tag from a bucket drop
	 *
	 * @param int  bucket_id
	 * @param int  drop_id
	 * @param int  tag_id
	 */
	public function delete_drop_tag($bucket_id, $drop_id, $tag_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/tags/'.$tag_id);
	}

	/**
	 * Adds a link to a bucket drop
	 *
	 * @param  int   bucket_id
	 * @param  int   drop_id
	 * @param  array link_data
	 * @return array
	 */
	public function add_drop_link($bucket_id, $drop_id, $link_data)
	{
		return $this->post('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/links', $link_data);
	}

	/**
	 * Removes a link from a bucket drop
	 *
	 * @param int  bucket_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_link($bucket_id, $drop_id, $link_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/links/'.$link_id);
	}

	/**
	 * Adds a place to a bucket drop
	 *
	 * @param  int   bucket_id
	 * @param  int   drop_id
	 * @param  array place_data
	 * @return array
	 */
	public function add_drop_place($bucket_id, $drop_id, $place_data)
	{
		return $this->post('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/places', $place_data);
	}

	/**
	 * Removes a place from a bucket drop
	 *
	 * @param int  bucket_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_place($bucket_id, $drop_id, $place_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/places/'.$place_id);
	}

	/**
	 * Adds a comment to a bucket drop
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  string comment_text
	 */
	public function add_drop_comment($bucket_id, $drop_id, $comment_text)
	{
		$parameters = array('comment_text' => $comment_text);

		return $this->post('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/comments', $parameters);
	}
	
	/**
	 * Get the comments for the bucket drop specified in $drop_id
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @return array
	 */
	public function get_drop_comments($bucket_id, $drop_id)
	{
		return $this->get('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/comments');
	}
	
	/**
	 * Deletes the comment specified in $comment_id from the bucket drop
	 * specified in $drop_id
	 */
	public function delete_drop_comment($bucket_id, $drop_id, $comment_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/comments/'.$commenti_id);
	}

	/**
	 * Adds a comment to a bucket
	 * @param  int    bucket_id
	 * @param  string comment_text
	 * @return array
	 */
	public function add_bucket_comment($bucket_id, $comment_text)
	{
		return $this->post('/buckets/'.$bucket_id.'/comments', array('comment_text' => $comment_text));
	}
	
	/**
	 * Gets the list of comments for the bucket specified in $bucket_id
	 * @param  int bucket_id
	 * @return array
	 */
	public function get_bucket_comments($bucket_id)
	{
		return $this->get('/buckets/'.$bucket_id.'/comments');
	}
	
	/**
	 * Deletes a comment from a bucket
	 *
	 * @param int bucket_id
	 * @param int comment_id
	 */
	public function delete_bucket_comment($bucket_id, $comment_id)
	{
		$this->delete('/buckets/'.$bucket_id.'/comments/'.$comment_id);
	}
	
	/**
	 * Adds a form to a drop
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function add_drop_form($bucket_id, $drop_id, $form_id, $values)
	{
		$parameters = array(
			'id' => $form_id,
			'values' => $values
		);

		return $this->post('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/forms', $parameters);
	}
	
	/**
	 * Modify custom drop fields
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function modify_drop_form($bucket_id, $drop_id, $form_id, $values)
	{
		$parameters = array(
			'values' => $values
		);
		
		return $this->put('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/forms/'.$form_id, $parameters);
	}
	
	/**
	 * Delete custom drop fields
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function delete_drop_form($bucket_id, $drop_id, $form_id)
	{
		return $this->delete('/buckets/'.$bucket_id.'/drops/'.$drop_id.'/forms/'.$form_id);
	}
	
	/**
	 * Adds the drop with the specified $droplet_id to the list of
	 * read drops for the bucket identified by $bucket_id
	 *
	 * @param  int bucket_id
	 * @param  int droplet_id
	 */
	public function mark_drop_as_read($bucket_id, $droplet_id)
	{
		$this->put('/buckets/'.$bucket_id.'/drops/read/'.$droplet_id);
	}
}