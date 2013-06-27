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
	 * Get drops from the river with the specified $id using the parameters
	 * in $parameters
	 *
	 * @param   long   $id          The id of the river
	 * @param   array  $parameters  The query parameters for fetching the drops
	 * @return  array
	 */
	public function get_drops($id, $parameters = array())
	{
		return $this->get('/rivers/'.$id.'/drops', $parameters);
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
			"public" => $public
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
	
	/**
	 * Check whether an account is following a river
	 *
	 * @param  int
	 * @param  int
	 * @return bool
	 */
	public function is_follower($river_id, $account_id)
	{
		try
		{
			$this->get('/rivers/'.$river_id.'/followers', array('follower' => $account_id));
		}
		catch (SwiftRiver_API_Exception_NotFound $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Adds the account specified in $account_id to the list of followers
	 * for the river specified in $river_id
	 *
	 * @param  int river_id
	 * @param  int account_id
	 */
	public function add_follower($river_id, $account_id)
	{
		$this->put('/rivers/'.$river_id.'/followers/'.$account_id);
	}
	
	/**
	 * Removes the account specified in $account_id from the list of followers
	 * for the river specified in $river_id
	 *
	 * @param  int river_id
	 * @param  int account_id
	 */
	public function delete_follower($river_id, $account_id)
	{
		$this->delete('/rivers/'.$river_id.'/followers/'.$account_id);
	}

	/**
	 * Get a river's collaborators
	 *
	 * @param   string  $river_id
	 * @return Array
	 */
	public function get_collaborators($river_id)
	{
		return $this->get('/rivers/'.$river_id.'/collaborators');
	}
	
	/**
	 * Delete collaborator
	 *
	 * @param   long  $river_name
	 * @param   long  $account_id
	 * @return  array
	 */
	public function delete_collaborator($river_id, $account_id)
	{
		return $this->delete('/rivers/'.$river_id.'/collaborators/'.$account_id);
	}
	
	/**
	 * Add a collaborator
	 *
	 * @param   long  $river_name
	 * @param   long  $collaborator_array
	 * @return Array
	 */
	public function add_collaborator($river_id, $collaborator_array)
	{
		return $this->post('/rivers/'.$river_id.'/collaborators', $collaborator_array);
	}

	/**
	 * Adds a tag to a river drop
	 *
	 * @param  int   river_id
	 * @param  int   drop_id
	 * @param  array tag_data
	 * @return array
	 */
	public function add_drop_tag($river_id, $drop_id, $tag_data)
	{
		return $this->post('/rivers/'.$river_id.'/drops/'.$drop_id.'/tags', $tag_data);
	}

	/**
	 * Removes a tag from a river drop
	 *
	 * @param int  river_id
	 * @param int  drop_id
	 * @param int  tag_id
	 */
	public function delete_drop_tag($river_id, $drop_id, $tag_id)
	{
		$this->delete('/rivers/'.$river_id.'/drops/'.$drop_id.'/tags/'.$tag_id);
	}

	/**
	 * Adds a link to a river drop
	 *
	 * @param  int   river_id
	 * @param  int   drop_id
	 * @param  array link_data
	 * @return array
	 */
	public function add_drop_link($river_id, $drop_id, $link_data)
	{
		return $this->post('/rivers/'.$river_id.'/drops/'.$drop_id.'/links', $link_data);
	}

	/**
	 * Removes a link from a river drop
	 *
	 * @param int  river_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_link($river_id, $drop_id, $link_id)
	{
		$this->delete('/rivers/'.$river_id.'/drops/'.$drop_id.'/links/'.$link_id);
	}

	/**
	 * Adds a place to a river drop
	 *
	 * @param  int   river_id
	 * @param  int   drop_id
	 * @param  array place_data
	 * @return array
	 */
	public function add_drop_place($river_id, $drop_id, $place_data)
	{
		return $this->post('/rivers/'.$river_id.'/drops/'.$drop_id.'/places', $place_data);
	}

	/**
	 * Removes a place from a river drop
	 *
	 * @param int  river_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_place($river_id, $drop_id, $place_id)
	{
		$this->delete('/rivers/'.$river_id.'/drops/'.$drop_id.'/places/'.$place_id);
	}

	/**
	 * Adds a comment to a river drop
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  string comment_text
	 */
	public function add_drop_comment($river_id, $drop_id, $comment_text)
	{
		$parameters = array('comment_text' => $comment_text);

		return $this->post('/rivers/'.$river_id.'/drops/'.$drop_id.'/comments', $parameters);
	}
	
	/**
	 * Get the comments for the river drop specified in $drop_id
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @return array
	 */
	public function get_drop_comments($river_id, $drop_id)
	{
		return $this->get('/rivers/'.$river_id.'/drops/'.$drop_id.'/comments');
	}
	
	/**
	 * Deletes the comment specified in $comment_id from the river drop
	 * specified in $drop_id
	 */
	public function delete_drop_comment($river_id, $drop_id, $comment_id)
	{
		$this->delete('/rivers/'.$river_id.'/drops/'.$drop_id.'/comments/'.$commenti_id);
	}

	/**
	 * Deletes the drop with the specified $drop_id from the river in $river_id
	 *
	 * @param int river_d
	 * @param int drop_id
	 */
	public function delete_drop($river_id, $drop_id)
	{
		$this->delete('/rivers/'.$river_id.'/drops/'.$drop_id);
	}
	
	/**
	 * Deletes the river specified in $river_id
	 *
	 * @param int river_id
	 */
	public function delete_river($river_id)
	{
		$this->delete('/rivers/'.$river_id);
	}
	
	/**
	 * Adds a form to a drop
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function add_drop_form($river_id, $drop_id, $form_id, $values)
	{
		$parameters = array(
			'id' => $form_id,
			'values' => $values
		);

		return $this->post('/rivers/'.$river_id.'/drops/'.$drop_id.'/forms', $parameters);
	}
	
	/**
	 * Modify custom drop fields
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function modify_drop_form($river_id, $drop_id, $form_id, $values)
	{
		$parameters = array(
			'values' => $values
		);
		
		return $this->put('/rivers/'.$river_id.'/drops/'.$drop_id.'/forms/'.$form_id, $parameters);
	}
	
	/**
	 * Delete custom drop fields
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  array form values
	 */
	public function delete_drop_form($river_id, $drop_id, $form_id)
	{
		return $this->delete('/rivers/'.$river_id.'/drops/'.$drop_id.'/forms/'.$form_id);
	}
	
	/**
	 * Gets and returns the rules for the specified river
	 *
	 * @param  int  river_id
	 * @return array
	 */
	public function get_rules($river_id)
	{
		return $this->get('/rivers/'.$river_id.'/rules');
	}
	
	/**
	 * Adds a rule to the river with the specified river_id
	 *
	 * @param int    river_id
	 * @param array  rule_data	
	 * @return array
	 */
	public function add_rule($river_id, $rule_data)
	{
		return $this->post('/rivers/'.$river_id.'/rules', $rule_data);
	}
	
	/**
	 * Modifies the rule with the specified $rule_id in the river specified
	 * by $river_id
	 *
	 * @param   int    river_id
	 * @param   int    rule_id
	 * @param   array  rule_data
	 * @return  array
	 */
	public function modify_rule($river_id, $rule_id, $rule_data)
	{
		return $this->put('/rivers/'.$river_id.'/rules/'.$rule_id, $rule_data);
	}
	
	/**
	 * Deletes the rule specified in $rule_id from the river with the
	 * specified $river_id
	 *
	 * @param  int river_id
	 * @param  int rule_id
	 */
	public function delete_rule($river_id, $rule_id)
	{
		$this->delete('/rivers/'.$river_id.'/rules/'.$rule_id);
	}
	
	/**
	 * Marks the drop with the id specified in $droplet_id as having been
	 * read
	 *
	 * @param int river_id
	 * @param int droplet_id
	 */
	public function mark_drop_as_read($river_id, $droplet_id)
	{
		$this->put('/rivers/'.$river_id.'/drops/read/'.$droplet_id);
	}
}