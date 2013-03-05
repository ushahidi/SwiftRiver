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
	 * SwiftRiver API instance
	 * @var SwiftRiver_API
	 */
	private $api = NULL;

	
	public function __construct($api)
	{
		$this->api = $api;
	}
	
	/**
	 * Register an account.
	 *
	 * @return	Array
	 */
	public function create_account($fullname, $email, $username, $password)
	{
		return $this->api->get_accounts_api()->create_account(
					$fullname, 
					$email, 
					$username, 
					$password
				);
	}
	
	/**
	 * Return the Account array for the logged in user.
	 *
	 * @return	Array
	 */
	public function get_logged_in_account()
	{
		return $this->api->get_accounts_api()->get_logged_in_account();
	}
	
	/**
	 * Return the Account array for the given account path
	 *
	 * @return	Array
	 */
	public function get_account_by_name($account_path)
	{
		return $this->api->get_accounts_api()->get_account_by_name($account_path);
	}
	
	/**
	 * Return the Account array for the given email
	 *
	 * @return	Array
	 */
	public function get_account_by_email($email)
	{
		return $this->api->get_accounts_api()->get_account_by_email($email);
	}
	
	/**
	 * Search accounts
	 *
	 * @return	Array
	 */
	public function search($query)
	{
		return $this->api->get_accounts_api()->search($query);
	}
	
	/**
	* Activate a newly created account
	*
	* @return Array
	*/
	public function activate_account($email, $token) {
		$account = $this->get_account_by_email($email);
		$this->api->get_accounts_api()->update_account($account['id'], array('token' => $token));
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
			$river = Service_River::get_array($river, $account);
			$rivers[] = $river;
		}
		
		// Collaborating rivers
		foreach ($account['collaborating_rivers'] as $river)
		{
			$river = Service_River::get_array($river, $account);
			$rivers[] = $river;
		}
		
		// Following rivers
		foreach ($account['following_rivers'] as $river)
		{
			$river = Service_River::get_array($river, $account);
			$rivers[] = $river;
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
			$bucket['is_owner'] = TRUE;
			$bucket['collaborator'] = FALSE;
			$bucket['subscribed'] = FALSE;
			$bucket['url'] = Service_Bucket::get_base_url($bucket);
			$buckets[] = $bucket;
		}
		
		// Collaborating buckets
		foreach ($account['collaborating_buckets'] as $bucket)
		{
			$bucket['is_owner'] = TRUE;
			$bucket['collaborator'] = TRUE;
			$bucket['subscribed'] = FALSE;
			$bucket['url'] = Service_Bucket::get_base_url($bucket);
			$buckets[] = $bucket;
		}
		
		// Following buckets
		foreach ($account['following_buckets'] as $bucket)
		{
			$bucket['is_owner'] = FALSE;
			$bucket['collaborator'] = FALSE;
			$bucket['subscribed'] = TRUE;
			$bucket['url'] = Service_Bucket::get_base_url($bucket);
			$buckets[] = $bucket;
		}
		
		return $buckets;
	}
	
}