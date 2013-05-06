<?php defined('SYSPATH') OR die('No direct script access');
/**
 * Search Service
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

class Service_Search extends Service_Base {

	/**
	 * @var SwiftRiver_API_Search
	 */
	private $search_api;

	/**
	 * Creates an instance of the search API
	 *
	 * @param  SwiftRiver_API_Client api
	 */
	public function __construct($api)
	{
		parent::__construct($api);
		
		$this->search_api = $this->api->get_search_api();
	}

	/**
	 * Returns all drops that contain the term specified in $search_term
	 *
	 * @param  string  search_term
	 * @param  int     page
	 * @return array
	 */
	public function find_drops($search_term, $page = 1)
	{
		try
		{
			return $this->search_api->find_drops($search_term, $page);
		}
		catch (SwiftRiver_API_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
		
		return array();
	}

	/**
	 * Returns all buckets that match the specified $search_term
	 *
	 * @param  string   search_term
	 * @param  array    querying_account
	 * @param  int      page
	 * @return array
	 */
	public function find_buckets($search_term, $querying_account, $page = 1)
	{
		try
		{
			$buckets = $this->search_api->find_buckets($search_term, $page);
		
			foreach ($buckets as & $bucket)
			{
				$bucket = Service_Bucket::get_array($bucket, $querying_account);
			}
			
			return $buckets;
		}
		catch (SwiftRiver_API_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
		
		return array();
	}

	/**
	 * Returns all rivers that match the specified $search_term
	 *
	 * @param  string   search_term
	 * @param  array    querying_account
	 * @param  int      page
	 * @return array
	 */
	public function find_rivers($search_term, $querying_account, $page = 1)
	{
		try
		{
			$rivers = $this->search_api->find_rivers($search_term, $page);
		
			foreach ($rivers as & $river)
			{
				$river = Service_River::get_array($river, $querying_account);
			}
			return $rivers;
		}
		catch (SwiftRiver_API_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
		
		return array();
	}

	/**
	 * Returns all users that match the specified $search_term
	 *
	 * @param  string   search_term
	 * @param  int      page
	 * @return array
	 */
	public function find_users($search_term, $page = 1)
	{
		try
		{
			return $this->search_api->find_users($search_term, $page);
		}
		catch (SwiftRiver_API_Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
		
		return array();
	}
	
}