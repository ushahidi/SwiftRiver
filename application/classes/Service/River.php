<?php defined('SYSPATH') or die('No direct script access.');
/**
 * River Service
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

class Service_River extends Service_Base {
	
	/**
	 * Return a river array with subscription and collaboration
	 * status populated for $querying_account
	 *
	 * @param Model_User $user
	 * @param Model_User $querying_account
	 * @return array
	 *
	 */
	public static function get_array($river, $querying_account)
	{
		$river['url'] = self::get_base_url($river);
		$river['expired'] = FALSE;
		$river['is_owner'] = $river['account']['id'] == $querying_account['id'];
		
		// Is the querying account collaborating on the river?
		$river['is_collaborator'] = FALSE;
		foreach ($querying_account['collaborating_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				// $river['is_owner'] = TRUE;
				$river['is_collaborator'] = TRUE;
			}
		}
		
		// Is the querying account following the river?
		$river['following'] = FALSE;
		foreach($querying_account['following_rivers'] as $r)
		{
			if ($river['id'] == $r['id'])
			{
				$river['following'] = TRUE;
			}
		}
		
		// Get display name from channel plugins and disabled channels
		if (isset($river['channels']))
		{
			$channels = array();
			foreach ($river['channels'] as $channel)
			{
				if (! Swiftriver_Plugins::get_channel_config($channel['channel']))
					continue;
				
				$channel['display_name'] = '';
				$channel['parameters'] = json_decode($channel['parameters'], TRUE);
				Swiftriver_Event::run('swiftriver.channel.format', $channel);
				$channels[] = $channel;
			}
			$river['channels'] = $channels;
		}
		
		
		return $river;
	}
	
	/**
	 * Return the Account array for the given account path
	 *
	 * @return	Array
	 */
	public function get_river_by_id($id, $querying_account)
	{
		$river = $this->api->get_rivers_api()->get_river_by_id($id);

		return $this->get_array($river, $querying_account);
	}
	
	/**
	 * Return URL to the given River
	 *
	 * @return	Array
	 */
	public static function get_base_url($river)
	{
		return URL::site($river['account']['account_path'].'/river/'.URL::title($river['name']));
	}

	/**
	 * Create a river
	 *
	 * @param   string  $river_name
	 * @return Array
	 */
	public function create_river_from_array($river_array) 
	{
		$river_array = $this->api->get_rivers_api()->create_river(
			$river_array['name'], 
			$river_array['description'], 
			(bool) $river_array['public']
		);
		
		$river_array['url'] = self::get_base_url($river_array);
		$river_array['is_owner'] = TRUE;
		$river_array['collaborator'] = FALSE;
		$river_array['subscribed'] = FALSE;
		
		return $river_array;
	}
	
	/**
	 * Modify the given river
	 *
	 * @return Array
	 */
	public function update_river($river_id, $river_name, $river_description, $river_public)
	{
		return $this->api->get_rivers_api()->update_river($river_id, $river_name, $river_description, $river_public);
	}
	
	/**
	 * Delete channel
	 *
	 * @param   string  $river_name
	 * @return Array
	 */
	public function delete_channel($river_id, $channel_id)
	{
		$this->api->get_rivers_api()->delete_channel($river_id, $channel_id);
	}
	
	/**
	 * Add a channal to the given river
	 *
	 * @return Array
	 */
	public function create_channel_from_array($river_id, $channel_array)
	{
		Swiftriver_Event::run('swiftriver.channel.validate', $channel_array);
		
		$channel_array = $this->api->get_rivers_api()->create_channel(
					$river_id, 
					$channel_array["channel"], 
					json_encode($channel_array["parameters"])
				);
				
		$channel_array['parameters'] = json_decode($channel_array['parameters'], TRUE);
		$channel_array['display_name'] = '';
		Swiftriver_Event::run('swiftriver.channel.format', $channel_array);
		return $channel_array;
	}
	
	/**
	 * Modify the given channel in the river
	 *
	 * @return Array
	 */
	public function update_channel_from_array($river_id, $channel_id, $channel_array)
	{
		Swiftriver_Event::run('swiftriver.channel.validate', $channel_array);
		
		$channel_array = $this->api->get_rivers_api()->update_channel(
					$river_id, 
					$channel_id, 
					$channel_array["channel"], 
					json_encode($channel_array["parameters"])
				);
				
		$channel_array['parameters'] = json_decode($channel_array['parameters'], TRUE);
		$channel_array['display_name'] = '';
		Swiftriver_Event::run('swiftriver.channel.format', $channel_array);
		return $channel_array;
	}
	
/**
	 * Get drops from a river
	 *
	 * @param   long    river_id
	 * @param   int     page
	 * @param   int     count
	 * @param   long    since_id
	 * @param   long    max_id
	 * @param   bool    photos
	 * @param   array   filters
	 * @return  array
	 */
	public function get_drops($river_id, $page = 1, $count = 20, $since_id = NULL, $max_id = NULL, $photos = FALSE, $filters = array())
	{
		// Parameters to send to the API
		$parameters = array(
			'page' => $page,
			'count' => $count
		);

		// since_id parameter
		if ( ! empty($since_id) AND $since_id > 0)
		{
			$parameters['since_id'] = $since_id;
		}

		// max_id parameter
		if ( ! empty($max_id) AND $max_id > 0)
		{
			$parameters['max_id'] = $max_id;
		}

		// photos parameter
		if ($photos === TRUE)
		{
			$parameters['photos'] = TRUE;
		}

		// Filters
		if ( ! empty($filters))
		{
			$filter_keys = array(
				'keywords' => 'list',
				'channels' => 'list',
				'channel_ids' => 'list',
				'date_from' => 'string', 
				'date_to' => 'string', 
				'state' => 'string',
				'locations'=> 'string',
			);

			foreach ($filter_keys as $key => $type) 
			{
				if (isset($filters[$key])) 
				{
					$value = $filters[$key];
					if ($type === 'list')
					{
						$value = implode(',', $value);
					}

					$parameters[$key] = $value;

				}
			}
		}
		return $this->api->get_rivers_api()->get_drops($river_id, $parameters);
	}
	
	/**
	 * Checks whether the account specified in $account_id is following the river
	 * specified in $river_id
	 *
	 * @param  int river_id
	 * @param  int account_id
	 * @return bool
	 */
	public function is_follower($river_id, $account_id)
	{
		return $this->api->get_rivers_api()->is_follower($river_id, $account_id);
	}
	
	/**
	 * Adds an account to the list of river followers
	 */
	public function add_follower($river_id, $account_id)
	{
		$this->api->get_rivers_api()->add_follower($river_id, $account_id);
	}
	
	/**
	 * Deletes an account from the list of river followers
	 */
	public function delete_follower($river_id, $account_id)
	{
		$this->api->get_rivers_api()->delete_follower($river_id, $account_id);
	}

	/**
	 * Get the river's collaborators
	 *
	 * @param   logn  $river_id
	 * @return Array
	 */
	public function get_collaborators($river_id)
	{
		return $this->api->get_rivers_api()->get_collaborators($river_id);
	}
	
	/**
	 * Remove collaborator
	 *
	 * @param   long  $river_id
	 * @param   long  $account_id
	 * @return  array
	 */
	public function delete_collaborator($river_id, $account_id)
	{
		return $this->api->get_rivers_api()->delete_collaborator($river_id, $account_id);
	}
	
	/**
	 * Add a collaborator
	 *
	 * @param   long  $river_id
	 * @param   long  $collaborator_array
	 * @return Array
	 */
	public function add_collaborator($river_id, $collaborator_array)
	{
		return $this->api->get_rivers_api()->add_collaborator($river_id, $collaborator_array);
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
		// Validation
		$validation = Validation::factory($tag_data)
			->rule('tag', 'not_empty')
			->rule('tag_type', 'not_empty');

		if ($validation->check())
		{
			return $this->api->get_rivers_api()->add_drop_tag($river_id, $drop_id, $tag_data);
		}
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
		$this->api->get_rivers_api()->delete_drop_tag($river_id, $drop_id, $tag_id);
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
		// Validation
		$validation = Validation::factory($link_data)
			->rule('url', 'url');

		if ($validation->check())
		{
			return $this->api->get_rivers_api()->add_drop_link($river_id, $drop_id, $link_data);
		}
	}

	/**
	 * Removes a tag from a river drop
	 *
	 * @param int  river_id
	 * @param int  drop_id
	 * @param int  link_id
	 */
	public function delete_drop_link($river_id, $drop_id, $link_id)
	{
		$this->api->get_rivers_api()->delete_drop_link($river_id, $drop_id, $link_id);
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
		// Validation
		$validation = Validation::factory($place_data)
			->rule('name', 'not_empty')
			->rule('longitude', 'range', -90, 90)
			->rule('latitude', 'range', -180, 180);

		if ($validation->check())
		{
			return $this->api->get_rivers_api()->add_drop_place($river_id, $drop_id, $place_data);
		}
	}

	/**
	 * Removes a place from a river drop
	 *
	 * @param int  river_id
	 * @param int  drop_id
	 * @param int  place_id
	 */
	public function delete_drop_place($river_id, $drop_id, $place_id)
	{
		$this->api->get_rivers_api()->delete_drop_place($river_id, $drop_id, $place_id);
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
		return $this->api->get_rivers_api()->add_drop_comment($river_id, $drop_id, $comment_text);
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
		return $this->api->get_rivers_api()->get_drop_comments($river_id, $drop_id);
	}
	
	/**
	 * Deletes the comment specified in $comment_id from the river drop
	 * specified in $drop_id
	 */
	public function delete_drop_comment($river_id, $drop_id, $comment_id)
	{
		$this->api->get_rivers_api()->delete_drop_comment($river_id, $drop_id, $comment_id);
	}
	
	/**
	 * Deletes a drop from a river
	 * @param int river_id
	 * @param int drop_id
	 */
	public function delete_drop($river_id, $drop_id)
	{
		$this->api->get_rivers_api()->delete_drop($river_id, $drop_id);
	}
	
	/**
	 * Deletes the river specified in $river_id
	 *
	 * @param int river_id
	 */
	public function delete_river($river_id)
	{
		$this->api->get_rivers_api()->delete_river($river_id);
	}

	/**
	 * Adds a form to a drop
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  string id of the form being added
	 * @param  mixed form field values
	 */
	public function add_drop_form($river_id, $drop_id, $form_id, $values)
	{
		return $this->api->get_rivers_api()->add_drop_form($river_id, $drop_id, $form_id, $values);
	}
	
	/**
	 * Modify existing form fields a form to a drop
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  string id of the form being modified
	 * @param  mixed form field values
	 */
	public function modify_drop_form($river_id, $drop_id, $form_id, $values)
	{
		return $this->api->get_rivers_api()->modify_drop_form($river_id, $drop_id, $form_id, $values);
	}
	
	/**
	 * Delete custom drop fields
	 *
	 * @param  int river_id
	 * @param  int drop_id
	 * @param  string id of the form being modified
	 */
	public function delete_drop_form($river_id, $drop_id, $form_id)
	{
		return $this->api->get_rivers_api()->delete_drop_form($river_id, $drop_id, $form_id);
	}

	/**
	 * Gets and returns the rules for the specified river
	 *
	 * @param  int river_id ID of the river with the rules
	 * @return array
	 */
	public function get_rules($river_id)
	{
		return $this->api->get_rivers_api()->get_rules($river_id);
	}
	
	/**
	 * Adds a rule to the river with the specified river_id
	 *
	 * @param  int   river_id
	 * @param  array rule_data
	 * @return array
	 */
	public function add_rule($river_id, $rule_data)
	{
		if ($this->validate_rule($rule_data))
		{
			return $this->api->get_rivers_api()->add_rule($river_id, $rule_data);
		}
	}
	
	/**
	 * Modify a rule
	 *
	 * @param int river_id
	 * @param int rule_id
	 * @param array rule_data
	 */
	public function modify_rule($river_id, $rule_id, $rule_data)
	{
		if ($this->validate_rule($rule_data))
		{
			return $this->api->get_rivers_api()->modify_rule($river_id, $rule_id, $rule_data);
		}
	}
	
	/**
	 * Deletes a rule
	 *
	 * @param int river_id
	 * @param int rule_id
	 */
	public function delete_rule($river_id, $rule_id)
	{
		$this->api->get_rivers_api()->delete_rule($river_id, $rule_id);
	}
	
	/**
	 * Internal helper method for validating rules
	 */
	private function validate_rule(array & $rule_data)
	{
		$rule_data['all_conditions'] = (bool) intval($rule_data['all_conditions']);

		$validation = Validation::factory($rule_data)
			->rule('name', 'not_empty')
			->rule('conditions', 'not_empty')
			->rule('actions', 'not_empty');
		
		if ( ! $validation->check())
		{
			throw new Validation_Exception($validation);
		}
		
		return TRUE;
	}
	
	/**
	 * Issues an API request to mark the drop with the specified $droplet_id
	 * as read
	 *
	 * @param  int river_id
	 * @param  int droplet_id
	 */
	public function mark_drop_as_read($river_id, $droplet_id)
	{
		$this->api->get_rivers_api()->mark_drop_as_read($river_id, $droplet_id);
	}

}