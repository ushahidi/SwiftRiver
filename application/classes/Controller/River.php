<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * River Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Controller_River extends Controller_Drop_Base {

	/**
	 * Channels
	 */
	protected $channels;

	/**
	 * ORM reference for the currently selected river
	 * @var Model_River
	 */
	protected $river;

	/**
	 * Whether the river is newly created
	 * @var bool
	 */
	protected $is_newly_created = FALSE;
	
	/**
	 * Base URL for this river.
	 */
	protected $river_base_url = NULL;
	
	/**
	 * Whether photo drops only are selected.
	 */
	protected $photos = FALSE;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the river name from the url
		$river_name_url = $this->request->param('name');
		$action = $this->request->action();
		
		// Find the matching river from the visited account's rivers.
		if (($river = $this->account_service->has_river($this->visited_account, $river_name_url)) !== FALSE)
		{
			$this->river = $this->river_service->get_river_by_id($river['id'], $this->user);
		}
		
		if ($river_name_url AND ! $this->river AND $action != 'manage')
		{
			$this->redirect($this->dashboard_url, 302);
		}
		
		// Action involves a specific river, check permissions
		if ($this->river)
		{
			$this->owner = $this->river['is_owner'];
			$this->is_collaborator = $this->river['is_collaborator'];
			$this->public = (bool) $this->river['public'];
			
			// If this river is not public and no ownership...
			if ( ! $this->public AND  ! $this->owner AND  ! $this->is_collaborator)
			{
				$this->redirect($this->dashboard_url, 302);
			}

			$this->river_base_url = $this->river_service->get_base_url($this->river);
			$this->settings_url = $this->river_base_url.'/settings';

			// Navigation Items
			$this->nav = Swiftriver_Navs::river($this->river);
			
			if ($this->owner)
			{
				$this->page_title = $this->river['name'];
			}
			else
			{
				$this->page_title = $this->river['account']['account_path'].' / '.$this->river['name'];
			}
			$this->template->header->title = $this->page_title;

			$this->template->content = View::factory('pages/river/layout')
				->bind('droplets_view', $this->droplets_view)
				->bind('river_base_url', $this->river_base_url)
				->bind('settings_url', $this->settings_url)
				->bind('owner', $this->owner)
				->bind('anonymous', $this->anonymous)
				->bind('user', $this->user)
				->bind('nav', $this->nav)
				->bind('active', $this->active)
				->bind('page_title', $this->page_title)
				->bind('follow_button', $follow_button);

			$view_data = array(
				'channels_config' => json_encode(Swiftriver_Plugins::channels()),
				'channels' => json_encode($this->river['channels']),
				'channels_base_url' => $this->river_base_url.'/settings/channels/options',
				'river' => $this->river
			);

			$this->template->content->set($view_data);

			$this->template->header->js .= HTML::script("themes/default/media/js/channels.js");
			
			// Show the follow button?
			if ( ! $this->owner AND ! $this->is_collaborator)
			{
				$is_following = $this->river_service->is_follower($this->river['id'], $this->user['id']);

				$river_data = json_encode(array(
					'id' => $this->river['id'],
					'name' => $this->river['name'],
					'type' => 'river',
					'following' => $is_following
				));

				$follow_button = View::factory('template/follow')
					->bind('data', $river_data)
					->bind('action_url', $action_url);
					
				$action_url = URL::site($this->river['url'].'/manage');
			}
			
		}
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		// Get the id of the current river
		$river_id = $this->river['id'];

		// The maximum droplet id for pagination and polling
		$max_droplet_id = $this->river['max_drop_id'];

		// River filters
		$filters = $this->get_filters();

		//Get Droplets
		$droplets_array = $this->river_service->get_drops($river_id, 1, 20, NULL, $max_droplet_id, (bool) $this->photos, $filters);

		// Bootstrap the droplet list
		$this->template->header->js .= HTML::script("themes/default/media/js/drops.js");

		$droplet_js = View::factory('pages/drop/js/drops')
			->set('fetch_base_url',  $this->river_base_url)
			->set('default_view', 'drops')
			->set('photos', $this->photos ? 1 : 0)
			->set('polling_enabled', TRUE);

		// Check if any filters exist and modify the fetch urls
		$droplet_js->filters = NULL;
		if ( ! empty($filters))
		{
			$encoded_filters = array();
			parse_str(http_build_query($filters), $encoded_filters);
			$droplet_js->filters = json_encode($encoded_filters);
		}

		$droplet_js->droplet_list = json_encode($droplets_array);
		$droplet_js->max_droplet_id = $max_droplet_id;

		// No content view
		$no_content_view = empty($this->river['channels'])
			? View::factory('pages/river/no-channels')
			: View::factory('pages/river/no-drops')->set('has_drops', ($max_droplet_id > 0));
		
			
		// Select droplet list view with drops view as the default if list not specified
		$this->droplets_view = View::factory('pages/drop/drops')
			->set('no_content_view', $no_content_view)
			->set('asset_templates', View::factory('template/assets'))
		    ->bind('droplet_js', $droplet_js)
		    ->bind('user', $this->user)
		    ->bind('owner', $this->owner)
		    ->bind('anonymous', $this->anonymous);

		// Show expiry notice to owners only
		if ($this->owner AND $this->river['expired'])
		{
			$this->droplets_view->nothing_to_display = "";

			$expiry_notice = View::factory('pages/river/expiry_notice');
			$expiry_notice->river_base_url = $this->river_base_url."/extend";
			$expiry_notice->extension_period = Swiftriver::get_setting('default_river_lifetime');
			$this->droplets_view->river_notice = $expiry_notice;

		}
		elseif ($this->owner AND $this->river['full'])
		{
			$this->droplets_view->nothing_to_display = "";
			$this->droplets_view->river_notice = View::factory('pages/river/full_notice');

		}
		else
		{
			$this->droplets_view->river_notice = '';
		}
		
		
		// Extend rivers accessed by an owner during notice perio
		if ($this->owner AND ! $this->river['expired'] AND FALSE)
		{
		 	$days_remaining = $this->river->get_days_to_expiry();
			$notice_period = Swiftriver::get_setting('default_river_lifetime');
			
			if (($days_remaining <= $notice_period) AND $this->river->is_notified())
			{
				Kohana::$log->add(Log::DEBUG, __("Extending lifetime of river with id :id", array(':id' => $this->river->id)));
				$this->river->extend_lifetime();
			}
		}
	}
	
	/**
	* Below are aliases for the index.
	*/
	public function action_drops()
	{
		$this->action_index();
	}
	
	public function action_list()
	{
		$this->action_index();
	}

	public function action_photos()
	{
		$this->photos = TRUE;
		$this->action_index();
	}	

	public function action_drop()
	{
		$this->action_index();
	}

	
	/**
	 * XHR endpoint for fetching droplets
	 */
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$this->response->headers('Content-Type', 'application/json;charset=UTF-8');

		switch ($this->request->method())
		{
			case "GET":
				$drop_id = $this->request->param('id');
				$page = 1;
				//Use page parameter or default to page 1
				$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
				$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
				$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
				$photos = $this->request->query('photos') ? intval($this->request->query('photos')) : 0;
				$filters = $this->get_filters();

				$droplets = $this->river_service->get_drops($this->river['id'], $page, 20, $since_id, $max_id, (bool) $photos, $filters);
				if ( ! $since_id)
				{
					$droplets = array_reverse($droplets);
				}

				if (empty($droplets))
				{
					Kohana::$log->add(Log::INFO, __("No drops returned"));
				}
				echo json_encode($droplets);

			break;
			
			case "PATCH":
				// No anonymous actions
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}

				$payload = json_decode($this->request->body(), TRUE);
				$droplet_id = intval($this->request->param('id', 0));

				// Check for change in bucket membership
				if (isset($payload['bucket_id']))
				{
					// Add/remove drop from bucket

					$bucket_id = intval($payload['bucket_id']);
					if ($payload['command'] === 'add')
					{
						$this->bucket_service->add_drop($bucket_id, $droplet_id);
					}
					elseif ($payload['command'] === 'remove')
					{
						$this->bucket_service->delete_drop($bucket_id, $droplet_id);
					}
				}

				// Has the drop been marked as read
				if (isset($payload['read']) AND $payload['read'] === TRUE)
				{
					// Mark drop as read
					$this->river_service->mark_drop_as_read($this->river['id'], $droplet_id);
				}
			
			break;
			
			case "DELETE":
				$droplet_id = intval($this->request->param('id', 0));
				$this->river_service->delete_drop($this->river['id'], $droplet_id);
			break;
		}
	}


	
	/**
	 * XHR endpoint for adding/removing collaborators
	 * 
	 * @return	void
	 */
	public function action_collaborators()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// No anonymous here
		if ($this->anonymous)
		{
			throw new HTTP_Exception_403();
		}
		
		$query = $this->request->query('q') ? $this->request->query('q') : NULL;
		
		if ($query)
		{
			echo json_encode($this->account_service->search($query));
			return;
		}
		
		switch ($this->request->method())
		{
			case "POST":
				$collaborator_array = json_decode($this->request->body(), TRUE);
				try
				{
					$collaborator = $this->river_service->add_collaborator($this->river['id'], $collaborator_array);
				}
				catch (SwiftRiver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}
				echo json_encode($collaborator);
			break;
			
			case "DELETE":
				$account_id = intval($this->request->param('id', 0));
				$this->river_service->delete_collaborator($this->river['id'], $account_id);
			break;
		}
	}
	
	
	/**
	 * River management
	 */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
			case "PUT":
				// No anonymous
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}
				
				$river_id = intval($this->request->param('id', 0));
				$request_body = json_decode($this->request->body(), TRUE);
				
				if ($request_body['following'])
				{
					$this->river_service->add_follower($river_id, $this->user['id']);
				}
				elseif ( ! $request_body['following'])
				{
					$this->river_service->delete_follower($river_id, $this->user['id']);
				}

			break;

			case "DELETE":
			break;
		}
		
		// Force refresh of cached rivers
		if (in_array($this->request->method(), array('DELETE', 'PUT', 'POST')))
		{
			// Cache::instance()->delete('user_rivers_'.$this->user->id);
		}
	}
	
	public function action_tags()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$drop_id = intval($this->request->param('id', 0));
				$tag_data = json_decode($this->request->body(), TRUE);
				$tag = $this->river_service->add_drop_tag($this->river['id'], $drop_id, $tag_data);
			
				echo json_encode($tag);
			break;
			
			case "DELETE":
				$drop_id = intval($this->request->param('id', 0));
				$tag_id = intval($this->request->param('id2', 0));

				$this->river_service->delete_drop_tag($this->river['id'], $drop_id, $tag_id);
			break;
		}
		
	}
	
	public function action_places()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
			break;
			
			case "DELETE":
				$drop_id = intval($this->request->param('id', 0));
				$place_id = intval($this->request->param('id2', 0));

				$this->river_service->delete_drop_place($this->river['id'], $drop_id, $place_id);
			break;
		}
		
	}
	
	public function action_links()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$drop_id = intval($this->request->param('id', 0));
				$link_data = json_decode($this->request->body(), TRUE);
				$link = $this->river_service->add_drop_link($this->river['id'], $drop_id, $link_data);
			
				echo json_encode($link);
			break;
			
			case "DELETE":
				$drop_id = intval($this->request->param('id', 0));
				$link_id = intval($this->request->param('id2', 0));

				$this->river_service->delete_drop_link($this->river['id'], $drop_id, $link_id);
			break;
		}
	}
	
	public function action_comments()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "GET":
				$drop_id = $this->request->param('id', 0);
				$drop_comments = $this->river_service->get_drop_comments($this->river['id'], $drop_id);
				echo json_encode($drop_comments);
			break;

			case "POST":
				$drop_id = $this->request->param('id', 0);
				$request_body = json_decode($this->request->body(), TRUE);
				$comment = $this->river_service->add_drop_comment($this->river['id'], $drop_id, $request_body['comment_text']);
				echo json_encode($comment);
			break;
			
			case "DELETE":
				$drop_id = $this->requst->param('id', 0);
				$comment_id = $this->request->param('id2', 0);
				$this->river_service->delete_drop_comment($this->river['id'], $drop_id, $comment_id);
			break;
		}
	}
	
	public function action_forms()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		switch ($this->request->method())
		{
			case "POST":
				$drop_id = intval($this->request->param('id', 0));
				$form_array = json_decode($this->request->body(), TRUE);
				
				$form_id = $form_array['id'];
				$values = $form_array['values'];
				
				
				try
				{
					$response = $this->river_service->add_drop_form($this->river['id'], $drop_id, $form_id, $values);
					echo json_encode($response);
				}
				catch (SwiftRiver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}			
			break;
			
			case "PUT":
				$drop_id = intval($this->request->param('id', 0));
				$form_id = $this->request->param('id2', 0);
				$form_array = json_decode($this->request->body(), TRUE);
				$values = $form_array['values'];
				
				try
				{
					$response = $this->river_service->modify_drop_form($this->river['id'], $drop_id, $form_id, $values);
					echo json_encode($response);
				}
				catch (SwiftRiver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}			
			break;
			
			case "DELETE":
				$drop_id = $this->request->param('id', 0);
				$form_id = $this->request->param('id2', 0);
				$this->river_service->delete_drop_form($this->river['id'], $drop_id, $form_id);
			break;
		}
	}
}