<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Bucket Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category    Services
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Service_Bucket extends Service_Base {
	
	/**
	 * Return the URL to the given bucket
	 *
	 * @return	Array
	 */
	public static function get_base_url($bucket)
	{
		return URL::site($bucket['account']['account_path'].'/bucket/'.URL::title($bucket['name']));
	}
	
	/**
	 * Gets the drops for the bucket with the specified $bucket_id
	 *
	 * @param  int   bucket_id
	 * @param  int   since_id
	 * @param  bool  photos
	 * @return array
	 */
	public function get_drops($bucket_id, $since_id = 0, $photos = FALSE, $page = 1, $filters = array())
	{
		$parameters = array('page' => $page);
		
		// Build the API request parameter list
		if (isset($since_id) AND $since_id > 0)
		{
			$parameters['since_id'] = $since_id;
		}

		if ($photos)
		{
			$parameters['photos'] = TRUE;
		}

		// Filters
		if ( ! empty($filters))
		{
			$filter_keys = array(
				'keywords' => 'list',
				'channels' => 'list',
				'channel_ids' => 'list',
				'date_from' => 'string', 
				'date_to' => 'string', 
				'state' => 'string',
				'locations' => 'string',
			);

			foreach ($filter_keys as $key => $type) 
			{
				if (isset($filters[$key])) 
				{
					$value = $filters[$key];
					if ($type === 'list')
					{
						$value = implode(',', $value);
					}

					$parameters[$key] = $value;

				}
			}
		}

		// Fetch the drops
		$drops = $this->api->get_buckets_api()->get_drops($bucket_id, $parameters);
		
		Service_Drop::format_drops($drops);

		return $drops;
	}

	/**
	 * Gets the bucket with the specified id
	 * @return array
	 */
	public function get_bucket_by_id($id, $querying_account)
	{
		// Fetch the bucket
		$bucket =  $this->api->get_buckets_api()->get_bucket_by_id($id);
		
		return self::get_array($bucket, $querying_account);		
	}

	/**
	 * Adds the drop specified in $drop_id to the bucket specified
	 * in $bucket_id
	 *
	 * @param  int    bucket_id
	 * @param  int    drop_id
	 */
	public function add_drop($bucket_id, $drop_id)
	{
		$this->api->get_buckets_api()->add_drop($bucket_id, $drop_id);
	}
	
	/**
	 * Removes the drop specified in $drop_id from the bucket specified
	 * in $bucket_id
	 *
	 * @param  int    bucket_id
	 * @param  int    drop_id
	 */
	public function delete_drop($bucket_id, $drop_id)
	{
		$this->api->get_buckets_api()->delete_drop($bucket_id, $drop_id);
	}
	
	/**
	 * Creates the bucket with the specified $bucket_name
	 *
	 * @param  string  bucket_name
	 * @param  array   account Account of of the user creating the bucket
	 * @return array
	 */
	public function create_bucket($bucket_name, $account)
	{
		$bucket = $this->api->get_buckets_api()->create_bucket($bucket_name);
		if ($bucket['account']['id'] == $account['id'])
		{
			$bucket['is_owner'] = TRUE;
		}
		
		// Additional properties required by the UI
		$bucket['url'] = self::get_base_url($bucket);
		$bucket['name_namespaced'] = $bucket['account']['account_path'].'/'.$bucket['name'];
		$bucket['display_name'] = $bucket['name'];
		
		return $bucket;
	}
	
	/**
	 * Deletes the bucket specified in $bucket_id
	 *
	 * @param  int bucket_id
	 */
	public function delete_bucket($bucket_id)
	{
		$this->api->get_buckets_api()->delete_bucket($bucket_id);
	}
	
	/**
	 * Gets and returns the collaborators for the bucket specified in
	 * $bucket_id
	 *
	 * @param  int bucket_id
	 * @return array
	 */
	public function get_collaborators($bucket_id)
	{
		return $this->api->get_buckets_api()->get_collaborators($bucket_id);
	}
	
	/**
	 * Remove collaborator
	 *
	 * @param   long  $bucket_id
	 * @param   long  $account_id
	 * @return Array
	 */
	public function delete_collaborator($bucket_id, $account_id)
	{
		return $this->api->get_buckets_api()->delete_collaborator($bucket_id, $account_id);
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
		return $this->api->get_buckets_api()->add_collaborator($bucket_id, $collaborator_array);
	}
	
	/**
	 * Changes the details of the bucket specified in $bucket_id
	 * and returns an array representation of the modified bucket
	 *
	 * @param  int     bucket_id
	 * @param  array   parameters
	 * @param  array   querying_account
	 * @return array
	 */
	public function modify_bucket($bucket_id, $parameters, $querying_account)
	{
		$bucket = $this->api->get_buckets_api()->modify_bucket($bucket_id, $parameters);

		return self::get_array($bucket, $querying_account);
	}
	

	/**
	 * Helper method for setting the bucket properties that are consumed
	 * by the UI
	 *
	 * @param  array  bucket            Bucket to be modified
	 * @param  array  querying_account  Account that requested for the bucket
	 * @return array
	 */
	public static function get_array($bucket, $querying_account)
	{
		// Set the bucket url
		$bucket['url'] = self::get_base_url($bucket);

		// Is the querying account an owner of the bucket
		$bucket['is_owner'] = $bucket['account']['id'] == $querying_account['id'];

		// Is the querying account collaborating on the fetched bucket?
		$bucket['is_collaborator'] = FALSE;
		foreach ($querying_account['collaborating_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				// $bucket['is_owner'] = TRUE;
				$bucket['is_collaborator'] = TRUE;
			}
		}

		// Is the querying account following the fetched bucket?
		$bucket['following'] = FALSE;
		foreach ($querying_account['following_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				$bucket['following'] = TRUE;
			}
		}

		return $bucket;
	}
	
	/**
	 * Verifies whether the account specified in $account_id is following
	 * the bucket specified in $id
	 *
	 * @param  int bucket_id
	 * @param  int account_id
	 * @return bool
	 */
	public function is_bucket_follower($bucket_id,  $account_id)
	{
		return $this->api->get_buckets_api()->is_bucket_follower($bucket_id, $account_id);
	}
	
	/**
	 * Adds a user to the list of bucket followers
	 *
	 * @param  int bucket_id
	 * @param  int account_id
	 */
	public function add_follower($bucket_id, $account_id)
	{
		$this->api->get_buckets_api()->add_follower($bucket_id, $account_id);
	}
	
	/**
	 * Removes a user from the list of bucket followers
	 *
	 * @param int bucket_id
	 * @param int account_id
	 */
	public function delete_follower($bucket_id, $account_id)
	{
		$this->api->get_buckets_api()->delete_follower($bucket_id, $account_id);
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
		// Validation
		$validation = Validation::factory($tag_data)
			->rule('tag', 'not_empty')
			->rule('tag_type', 'not_empty');

		if ($validation->check())
		{
			return $this->api->get_buckets_api()->add_drop_tag($bucket_id, $drop_id, $tag_data);
		}
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
		$this->api->get_buckets_api()->delete_drop_tag($bucket_id, $drop_id, $tag_id);
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
		// Validation
		$validation = Validation::factory($link_data)
			->rule('url', 'url');

		if ($validation->check())
		{
			return $this->api->get_buckets_api()->add_drop_link($bucket_id, $drop_id, $link_data);
		}
	}

	/**
	 * Removes a tag from a bucket drop
	 *
	 * @param int  bucket_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_link($bucket_id, $drop_id, $link_id)
	{
		$this->api->get_buckets_api()->delete_drop_link($bucket_id, $drop_id, $link_id);
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
		// Validation
		$validation = Validation::factory($place_data)
			->rule('name', 'not_empty')
			->rule('longitude', 'range', -90, 90)
			->rule('latitude', 'range', -180, 180);

		if ($validation->check())
		{
			return $this->api->get_buckets_api()->add_drop_place($bucket_id, $drop_id, $place_data);
		}
	}

	/**
	 * Removes a place from a bucket drop
	 *
	 * @param int  bucket_id
	 * @param int  drop_id
	 * @param int  place_id
	 */
	public function delete_drop_place($bucket_id, $drop_id, $place_id)
	{
		$this->api->get_buckets_api()->delete_drop_place($bucket_id, $drop_id, $place_id);
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
		return $this->api->get_buckets_api()->add_drop_comment($bucket_id, $drop_id, $comment_text);
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
		return $this->api->get_buckets_api()->get_drop_comments($bucket_id, $drop_id);
	}
	
	/**
	 * Deletes the comment specified in $comment_id from the bucket drop
	 * specified in $drop_id
	 */
	public function delete_drop_comment($bucket_id, $drop_id, $comment_id)
	{
		$this->api->get_buckets_api()->delete_drop_comment($bucket_id, $drop_id, $comment_id);
	}
	
	/**
	 * Adds a comment to a bucket
	 * @param  int    bucket_id
	 * @param  string comment_text
	 * @return array
	 */
	public function add_bucket_comment($bucket_id, $comment_text)
	{
		return $this->api->get_buckets_api()->add_bucket_comment($bucket_id, $comment_text);
	}
	
	/**
	 * Gets the list of comments for the bucket specified in $bucket_id
	 * @param  int bucket_id
	 * @return array
	 */
	public function get_bucket_comments($bucket_id)
	{
		return $this->api->get_buckets_api()->get_bucket_comments($bucket_id);
	}
	
	/**
	 * Deletes a comment from a bucket
	 *
	 * @param int bucket_id
	 * @param int comment_id
	 */
	public function delete_bucket_comment($bucket_id, $comment_id)
	{
		$this->api->get_buckets_api()->delete_bucket_comment($bucket_id, $comment_id);
	}
	
	/**
	 * Adds a form to a drop
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  string id of the form being added
	 * @param  mixed form field values
	 */
	public function add_drop_form($bucket_id, $drop_id, $form_id, $values)
	{
		return $this->api->get_buckets_api()->add_drop_form($bucket_id, $drop_id, $form_id, $values);
	}
	
	/**
	 * Modify existing form fields a form to a drop
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  string id of the form being modified
	 * @param  mixed form field values
	 */
	public function modify_drop_form($bucket_id, $drop_id, $form_id, $values)
	{
		return $this->api->get_buckets_api()->modify_drop_form($bucket_id, $drop_id, $form_id, $values);
	}
	
	/**
	 * Delete custom drop fields
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 * @param  string id of the form being modified
	 */
	public function delete_drop_form($bucket_id, $drop_id, $form_id)
	{
		return $this->api->get_buckets_api()->delete_drop_form($bucket_id, $drop_id, $form_id);
	}
	
	/**
	 * Adds the drop with the specified $droplet_id to the list of read
	 * drops for the bucket in $bucket_id
	 *
	 * @param  int bucket_id
	 * @param  int droplet_id
	 */
	public function mark_drop_as_read($bucket_id, $droplet_id)
	{
		$this->api->get_buckets_api()->mark_drop_as_read($bucket_id, $droplet_id);
	}

}