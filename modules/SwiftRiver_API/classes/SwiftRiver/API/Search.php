<?php defined('SYSPATH') OR die('No direct script access');
/**
 * SwiftRiver Search API
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

class SwiftRiver_API_Search extends SwiftRiver_API {
	
	/**
	 * Finds and returns all drops that contain the phrase specified
	 * in $search_term
	 *
	 * @param  string  search_term
	 * @param  int     page
	 * @return array
	 */
	public function find_drops($search_term, $page = 1)
	{
		$parameters = array(
			'q' => $search_term,
			'page' => $page
		);
		return $this->get('/search/drops/',  $parameters);
	}

	/**
	 * Finds and returns all buckets that contain the phrase specified
	 * in $search_term
	 *
	 * @param  string  search_term
	 * @param  int     page
	 * @return array
	 */
	public function find_buckets($search_term, $page = 1)
	{
		$parameters = array(
			'q' => $search_term,
			'page' => $page
		);
		return $this->get('/search/buckets/',  $parameters);
	}

	/**
	 * Finds and returns all rivers that contain the phrase specified
	 * in $search_term
	 *
	 * @param  string  search_term
	 * @param  int     page
	 * @return array
	 */
	public function find_rivers($search_term, $page = 1)
	{
		$parameters = array(
			'q' => $search_term,
			'page' => $page
		);
		return $this->get('/search/rivers/',  $parameters);
	}
	
	/**
	 * Finds and returns all users that contain the phrase specified
	 * in $search_term
	 *
	 * @param  string  search_term
	 * @param  int     page
	 * @return array
	 */
	public function find_users($search_term, $page = 1)
	{
		$parameters = array(
			'q' => $search_term,
			'page' => $page
		);
		return $this->get('/search/accounts/',  $parameters);
	}
	
}