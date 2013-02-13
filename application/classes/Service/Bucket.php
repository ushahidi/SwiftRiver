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
	
	private $api = NULL;
	
	public function __construct($api)
	{
		$this->api = $api;
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
	 * Gets the drops for the bucket with the specified id
	 * @return array
	 */
	public function get_drops($bucket_id, $params = array())
	{
		return $this->api->get_buckets_api()->get_bucket_drops($bucket_id, $params);
	}
	
	/**
	 * Gets the bucket with the specified id
	 * @return array
	 */
	public function get_bucket_by_id($id, $querying_account)
	{
		// Fetch the bucket
		$bucket =  $this->api->get_buckets_api()->get_bucket_by_id($id);
		
		// Is the querying account an owner of the river
		$bucket['is_owner'] = $bucket['account']['id'] == $querying_account['id'];
		
		// Is the querying account collaborating on the fetched bucket?
		foreach ($querying_account['collaborating_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				$bucket['is_collaborating'] = TRUE;
			}
		}
		
		// Is the querying account following the fetched bucket?
		foreach ($querying_account['following_buckets'] as $b)
		{
			if ($b['id'] === $bucket['id'])
			{
				$bucket['is_following'] = TRUE;
			}
		}
		
		return $bucket;
	}
	
	/**
	 * Get the drops added to the bucket since the specified id ($since_id)
	 *
	 * @param  int $bucket_id
	 * @param  int $since_id
	 * @return array
	 */
	public function get_drops_since_id($bucket_id, $since_id)
	{
		$params = array('since_id' => $since_id);
		return $this->api->get_buckets_api()->get_bucket_drops($bucket_id, $params);
	}
	
	/**
	 * Gets and returns the list of users collaborating on the specified bucket
	 * @param   int  $bucket_id
	 * @return array
	 */
	public function get_collaborators($bucket_id)
	{
		$this->api->get_buckets_api()->get_bucket_collaborators($bucket_id);
	}
	
	
}