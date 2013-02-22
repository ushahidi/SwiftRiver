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
	 * Get drops from a river with id less than the given max_id
	 *
	 * @param   long  $id          The id of the river
	 * @param   long  $max_id      The maximum of drops to be returned
	 * @param   int   $page        Page relative from max_id and count
	 * @param   int   $count       Number of drops to return per page
	 * @return Array
	 */
	public function get_drops($id, $max_id, $page, $count)
	{
		return $this->get('/rivers/'.$id.'/drops', array(
			'max_id' => $max_id,
			'page' => $page,
			'count' => $count
		));
	}
	
	/**
	 * Get drops from a river with id greater than the given since_id
	 *
	 * @param   long  $id          The id of the river
	 * @param   long  $since_id      The maximum of drops to be returned
	 * @param   int   $count       Number of drops to return per page
	 * @return Array
	 */
	public function get_drops_since($id, $since_id, $count)
	{
		return $this->get('/rivers/'.$id.'/drops', array(
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
		
		$request_body = array(
			"name" => $river_name,
			"description" => $river_description,
			"public" => (bool) $public
		);
		return $this->post('/rivers', $request_body);
	}
	
	/**
	 * Modify a river
	 *
	 * @param   string  $river_name
	 * @param   string  $river_description
	 * @param   string  $river_public
	 * @return Array
	 */
	public function update_river($river_id, $river_name, $river_description, $river_public)
	{
		$request_body = array(
			"name" => $river_name,
			"description" => $river_description,
			"public" => (bool) $river_public
		);

		return $this->put('/rivers/'.$river_id, $request_body);
	}
	
	/**
	 * Create a channel
	 *
	 * @param   int  $river_id
	 * @param   string  $channel
	 * @param   string  $parameters
	 * @return Array
	 */
	public function create_channel($river_id, $channel, $parameters = NULL)
	{
		$request_body = array(
			"channel" => $channel,
			"parameters" => $parameters
		);

		return $this->post('/rivers/'.$river_id.'/channels', $request_body);
	}
	
	/**
	 * Delete channel
	 *
	 * @param   long  $river_name
	 * @param   long  $channel_id
	 * @return Array
	 */
	public function delete_channel($river_id, $channel_id)
	{
		return $this->delete('/rivers/'.$river_id.'/channels/'.$channel_id);
	}
	
	/**
	 * Modify a channel
	 *
	 * @param   int  $river_id
	 * @param   string  $channel
	 * @param   string  $parameters
	 * @return Array
	 */
	public function update_channel($river_id, $channel_id, $channel, $parameters = NULL)
	{
		$request_body = array(
			"channel" => $channel,
			"parameters" => $parameters
		);

		return $this->put('/rivers/'.$river_id.'/channels/'.$channel_id, $request_body);
	}
}