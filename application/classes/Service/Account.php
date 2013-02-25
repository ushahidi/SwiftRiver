<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Account Service
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage  Exceptions
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Service_Account {
	
	/**
	 * SwiftRiver_API_Accounts instance
	 * @var SwiftRiver_API_Accounts
	 */
	private $accounts_api;

	
	public function __construct($api)
	{
		$this->accounts_api = $api->get_accounts_api();
	}
	
	/**
	 * Return the Account array for the logged in user.
	 *
	 * @return	Array
	 */
	public function get_logged_in_account()
	{
		return $this->accounts_api->get_logged_in_account();
	}
	
	/**
	 * Return the Account array for the given account path
	 *
	 * @return	Array
	 */
	public function get_account_by_name($account_path)
	{
		return $this->accounts_api->get_account_by_name($account_path);
	}
	
	/**
	 * Get a single rivers array for the given account.
	 *
	 * @return	Array
	 */
	public function get_rivers($account)
	{
		$rivers = array();
		
		// Own rivers
		foreach ($account['rivers'] as $river)
		{
			$rivers[] = Service_River::get_array($river, $account);
		}
		
		// Collaborating rivers
		foreach ($account['collaborating_rivers'] as $river)
		{
			$rivers[] = Service_River::get_array($river, $account);
		}
		
		// Following rivers
		foreach ($account['following_rivers'] as $river)
		{
			$rivers[] = Service_River::get_array($river, $account);
		}
		
		return $rivers;
	}
	
	/**
	 * Get a single buckets array for the given account.
	 *
	 * @return	Array
	 */
	public function get_buckets($account)
	{
		$buckets = array();
		
		// Own buckets
		foreach ($account['buckets'] as $bucket)
		{
			$buckets[] = Service_Bucket::get_array($bucket, $account);
		}
		
		// Collaborating buckets
		foreach ($account['collaborating_buckets'] as $bucket)
		{
			$buckets[] = Service_Bucket::get_array($bucket, $account);
		}
		
		// Following buckets
		foreach ($account['following_buckets'] as $bucket)
		{
			$buckets[] = Service_Bucket::get_array($bucket, $account);
		}
		
		return $buckets;
	}
	
	/**
	 * Verifies where the account in $query_account_id is following the account in $id
	 *
	 * @param  int  id
	 * @param  int  query_account_id
	 * @return bool
	 */
	public function is_account_follower($id, $query_account_id)
	{
		return $this->accounts_api->is_account_follower($id, $query_account_id);
	}
	
	/**
	 * Adds the account with the specified follower_account_id to the list of followers
	 * for the account specified by id
	 *
	 * @param  int id ID of the account to be followed
	 * @param  int follower_account_id ID of the Account to be added to the list of followers
	 * @return bool TRUE on success, FALSE otherwise
	 */
	public function add_follower($id, $follower_account_id)
	{
		return $this->accounts_api->add_follower($id, $follower_account_id);
	}
	
	/**
	 * Removes the account specified in $follower_account_id from the list of followers
	 * for the account specifid in $id
	 *
	 * @param  int id
	 * @param  int follower_account_id
	 * @return bool TRUE on success, FALSE otherwise
	 */
	public function remove_follower($id, $follower_account_id)
	{
		return $this->accounts_api->remove_follower($id, $follower_account_id);
	}
	
}