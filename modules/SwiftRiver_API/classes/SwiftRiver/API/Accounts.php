<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SwiftRiver Rivers API
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
class SwiftRiver_API_Accounts extends SwiftRiver_API {
	
	/**
	* Get account object for the logged in account.
	*
	* @return Array
	*/
	public function get_logged_in_account()
	{
		return $this->get('/accounts/me');
	}
	
	/**
	* Get account object for the logged in account.
	*
	* @return Array
	*/
	public function get_account_by_name($account_path)
	{
		return $this->get('/accounts',  array('account_path' => $account_path));
	}
	
	/**
	 * Verifies whether the account specified in $query_account_id is following
	 * the account specified in $id
	 *
	 * @param  int id
	 * @param  int query_account_id
	 * @return bool
	 */
	public function is_account_follower($id, $query_account_id)
	{
		try
		{
			$this->get('/accounts/'.$id.'/followers', array('follower' => $query_account_id));
		}
		catch (SwiftRiver_API_Exception_NotFound $e)
		{
			Kohana::$log->add(Log::INFO, __("Account :query does not follow account :id",
				array(":query" => $query_account_id, ":id" => $id)));

			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Adds the account with the specified follower_account_id to the list of followers
	 * for the account specified by id
	 *
	 * @param  int id
	 * @param  int follower_account_id
	 * @return bool
	 */
	public function add_follower($id, $follower_account_id)
	{
		try
		{
			$this->put('/accounts/'.$id.'/followers/'.$follower_account_id);
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Removes the account specified in $follower_account_id from the list of followers
	 * for the account specified in $id
	 *
	 * @param  int  id
	 * @param  int follower_account_id
	 * @return bool
	 */
	public function remove_follower($id, $follower_account_id)
	{
		try
		{
			$this->delete('/accounts/'.$id.'/followers/'.$follower_account_id);
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Search accounts
	 * @return Array
	 */
	public function search($query)
	{
		return $this->get('/accounts',  array('q' => $query));
	}
	
	/**
	 * Update user's display profile
	 * @param  int    account_id
	 * @param  array  profile_data
	 */
	public function update_profile($account_id, $profile_data)
	{
		return $this->put('/accounts/'.$account_id, $profile_data);
	}
	
	/**
	 * Change user password
	 *
	 * @param  int    account_id
	 * @param  array  parameters
	 * @return array
	 */
	public function change_password($account_id, $parameters)
	{
		return $this->put('/accounts/'.$account_id, $parameters);
	}
}