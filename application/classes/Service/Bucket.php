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
 * @category    Service
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Service_Bucket {
	
	/**
	 * Buckets API
	 * @var SwiftRiver_API_Buckets
	 */
	private $buckets_api;
	
	public function __construct($api)
	{
		// Initialize the Buckets API
		$this->buckets_api = $api->get_buckets_api();
	}
	
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
	public function get_drops($bucket_id, $since_id = 0, $photos = FALSE)
	{
		$parameters = array();
		
		// Build the API request parameter list
		if (isset($since_id) AND $since_id > 0)
		{
			$parameters['since_id'] = $since_id;
		}

		if ($photos)
		{
			$parameters['photos'] = TRUE;
		}

		// Fetch the drops
		$drops = $this->buckets_api->get_drops($bucket_id, $parameters);
		
		Service_Drop::marshall_drops($drops);

		return $drops;
	}

	/**
	 * Gets the bucket with the specified id
	 * @return array
	 */
	public function get_bucket_by_id($id, $querying_account)
	{
		// Fetch the bucket
		$bucket =  $this->buckets_api->get_bucket_by_id($id);
		
		return $this->marshall_bucket($bucket, $querying_account);		
	}

	/**
	 * Adds the drop specified in $drop_id to the bucket specified
	 * in $bucket_id
	 *
	 * @param  int bucket_id
	 * @param  int drop_id
	 */
	public function add_drop($bucket_id, $drop_id)
	{
		$this->buckets_api->add_drop($bucket_id, $drop_id);
	}
	
	/**
	 * Removes the drop specified in $drop_id from the bucket specified
	 * in $bucket_id
	 *
	 * @param  int  bucket_id
	 * @param  int  drop_id
	 */
	public function delete_drop($bucket_id, $drop_id)
	{
		$this->buckets_api->delete_drop($bucket_id, $drop_id);
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
		$bucket = $this->buckets_api->create_bucket($bucket_name);
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
		$this->buckets_api->delete_bucket($bucket_id);
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
		return $this->buckets_api->get_collaborators($bucket_id);
	}
	
	/**
	 * Remove collaborator
	 *
	 * @param   long  $bucket_id
	 * @param   long  $collaborator_id	 
	 * @return Array
	 */
	public function delete_collaborator($bucket_id, $collaborator_id)
	{
		return $this->buckets_api->delete_collaborator($bucket_id, $collaborator_id);
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
		return $this->buckets_api->add_collaborator($bucket_id, $collaborator_array);
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
		$bucket = $this->buckets_api->modify_bucket($bucket_id, $parameters);

		return $this->marshall_bucket($bucket, $querying_account);
	}
	

	/**
	 * Helper method for setting the bucket properties that are consumed
	 * by the UI
	 *
	 * @param  array  bucket            Bucket to be modified
	 * @param  array  querying_account  Account that requested for the bucket
	 * @return array
	 */
	private function marshall_bucket($bucket, $querying_account)
	{
		// Set the bucket url
		$bucket['url'] = self::get_base_url($bucket);

		// Is the querying account an owner of the river
		$bucket['is_owner'] = $bucket['account']['id'] == $querying_account['id'];		
		
		// Is the querying account collaborating on the fetched bucket?
		$bucket['collaborator'] = FALSE;
		foreach ($querying_account['collaborating_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				$bucket['is_owner'] = TRUE;
				$bucket['collaborator'] = TRUE;
			}
		}
		
		// Is the querying account following the fetched bucket?
		$bucket['subscribed'] = FALSE;
		foreach ($querying_account['following_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				$bucket['subscribed'] = TRUE;
			}
		}
		
		return $bucket;
		
	}
}