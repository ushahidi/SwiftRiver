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
class SwiftRiver_API_Rivers extends SwiftRiver_API {
	
	/**
	 * Get river with the given id
	 *
	 * @return Array
	 */
	public function get_river_by_id($id)
	{
		return $this->get('/rivers/'.$id);
	}
	
	/**
	 * Get drops from a river
	 *
	 * @param   string  $path            the resource path
	 * @param   mixed   $parameters       POST parameters
	 * @return Array
	 */
	public function get_drops($id, $max_id = NULL, $since_id = NULL, $count = NULL)
	{
		return $this->get('/rivers/'.$id.'/drops', array(
			'max_id' => $max_id,
			'since_id' => $since_id,
			'count' => $count
		));
	}
	
	/**
	 * Create a river
	 *
	 * @param   string  $river_name
	 * @param   string  $river_description
	 * @param   string  $public
	 * @return Array
	 */
	public function create_river($river_name, $river_description = NULL, $public = FALSE) {
		
		$request_body = json_encode(array(
			"name" => $river_name,
			"description" => $river_description,
			"public" => (bool) $public
		));
		return $this->post('/rivers', $request_body);
	}
}