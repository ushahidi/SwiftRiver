<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Controller - Handles Individual Buckets
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
class Controller_Bucket extends Controller_Drop_Container {

	/**
	 * Bucket currently being viewed
	 * @var Model_Bucket
	 */
	protected $bucket;
	
	/**
	 * Whether photo drops only are selected.
	 */
	protected $photos = FALSE;
	
	/**
	 * Base URL of the bucket
	 * @var string
	 */
	protected $bucket_base_url;
	
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the bucket name from the url
		$this->bucket_base_url = sprintf("/%s/bucket/%s", $this->request->param('account'), $this->request->param('name'));
		
		// Get the buckets associated with the visited account
		$visited_account_buckets = $this->account_service->get_buckets($this->visited_account, $this->user);

		foreach ($visited_account_buckets as $k => $bucket)
		{
			if ($bucket['url'] === $this->bucket_base_url)
			{
				$this->bucket = $this->bucket_service->get_bucket_by_id($bucket['id'], $this->user);
				$this->owner = $this->bucket['is_owner'];
				break;
			}
		}

		// Check if the bucket was retrieved
		if (empty($this->bucket))
		{
			Kohana::$log->add(Log::INFO, __("Bucket :name not found", array(":name" => $this->bucket_base_url)));
			throw new HTTP_Exception_404();
		}
		
		// Set the page title
		$this->page_title = $this->bucket['name'];
		
	}

	public function action_index()
	{
		$this->template->header->title = $this->page_title;
		
		$this->template->content = View::factory('pages/bucket/main')
			->bind('droplets_view', $droplets_view)
			->bind('settings_url', $settings_url)
			->bind('discussion_url', $discussion_url)
			->bind('owner', $this->owner)
			->bind('drop_count', $drop_count)
			->bind('photos_drop_count', $photos_drop_count)
			->bind('follow_button', $follow_button);

		$this->template->content->is_collaborator = FALSE;
		$this->template->content->anonymous = $this->anonymous;
		$this->template->content->bucket = $this->bucket;
		$this->template->content->user = $this->user;
			
		// The maximum droplet id for pagination and polling
		$droplet_list = $this->bucket_service->get_drops($this->bucket['id'], NULL, $this->photos);

		$drop_count = count($droplet_list);
		$photos_drop_count = ($this->photos) ? $drop_count : 0;

		// Bootstrap the droplet list
		$this->template->header->js .= HTML::script("themes/default/media/js/drops.js");

		$droplet_js = View::factory('pages/drop/js/drops')
			->set('fetch_base_url', $this->bucket_base_url)
			->set('default_view', $this->bucket['default_layout'])
			->set("photos", ($this->photos ? 1 : 0))
			->set('droplet_list', json_encode($droplet_list))
			->set('filters', NULL)
			->set('max_droplet_id', 0)
			->set('channels', json_encode(array()));

		if (count($droplet_list) > 0)
		{
			$droplet_js->set('max_droplet_id', $droplet_list[0]['id']);
		}

		// Generate the List HTML
		$droplets_view = View::factory('pages/drop/drops')
			->bind('droplet_js', $droplet_js)
			->bind('user', $this->user)
			->bind('owner', $this->owner)
		    ->bind('anonymous', $this->anonymous);

		// Links to bucket menu items
		$settings_url = $this->bucket_base_url.'/settings';
		$discussion_url = $this->bucket_base_url.'/discussion';
		
		// Follow button
		if ( ! $this->owner)
		{
			// Is the current user following the visited bucket?
			$is_following = $this->bucket_service->is_bucket_follower($this->bucket['id'], 
				$this->user['id']);

			// Bucket data
			$bucket_data = json_encode(array(
				'id' => $this->bucket['id'],
				'name' => $this->bucket['name'],
				'type' => 'bucket',
				'following' => $is_following
			));
			
			// xHR endpoint for follow/unfollow actions
			$action_url = URL::site($this->bucket['url'].'/manage');

			$follow_button = View::factory('template/follow')
				->bind('data', $bucket_data)
				->bind('action_url', $action_url);
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
	 * Gets the droplets for the specified bucket and page no. contained
	 * in the URL variable "page"
	 * The result is packed into JSON and returned to the requesting client
	 */
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "GET":
				$drop_id = $this->request->param('id');
				$page = 1;

				$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
				$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
				$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
				$photos = $this->request->query('photos') ? intval($this->request->query('photos')) : 0;

				// Get the drops
				$droplets_array = $this->bucket_service->get_drops($this->bucket['id'], $since_id, (bool) $photos, $page);
				
				echo json_encode($droplets_array);
			break;
			
			case "PATCH":
				// No anonymous actions
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}
			
				$payload = json_decode($this->request->body(), TRUE);
				if ( ! isset($payload['command']) OR ! isset($payload['bucket_id']))
				{
					throw new HTTP_Exception_400();
				}

				$bucket_id = intval($payload['bucket_id']);
				$droplet_id = intval($this->request->param('id', 0));
				if ($payload['command'] === 'add')
				{
					$this->bucket_service->add_drop($bucket_id, $droplet_id, "bucket");
				}
				elseif ($payload['command'] === 'remove')
				{
					$this->bucket_service->delete_drop($bucket_id, $droplet_id);
				}
			break;
			
			case "DELETE":
				
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}

				$droplet_id = intval($this->request->param('id', 0));
				// $this->bucket_service->delete_drop($bucket_id, $droplet_id);
		}
	}
	

	/**
	 * Bucket collaborators restful api
	 * 
	 * @return	void
	 */
	public function action_collaborators()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
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
					$collaborator = $this->bucket_service->add_collaborator($this->bucket['id'], $collaborator_array);
				}
				catch (SwiftRiver_API_Exception_BadRequest $e)
				{
					throw new HTTP_Exception_400();
				}
				
				echo json_encode($collaborator);
				break;
			break;
			
			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
							
				$collaborator_id = intval($this->request->param('id', 0));
				
				try
				{
					$this->bucket_service->delete_collaborator($this->bucket['id'], $collaborator_id);
				} catch (SwiftRiver_API_Exception_NotFound $e)
				{
					throw new HTTP_Exception_403();
				}
			break;
		}
	}
	
	/**
	 * Endpoint for bucket follow/unfollow actions
	 */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case "PUT";
				$request_body = json_decode($this->request->body(), TRUE);
			
				$bucket_id = intval($this->request->param('id', 0));

				// Follow/unfollow
				if ($request_body['following'])
				{
					// Follow bucket
					$this->bucket_service->add_follower($bucket_id, $this->user['id']);
				}
				elseif ( ! $request_body['following'])
				{
					// Unfollow bucket
					$this->bucket_service->delete_follower($bucket_id, $this->user['id']);
				}

			break;
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
				$tag = $this->bucket_service->add_drop_tag($this->bucket['id'], $drop_id, $tag_data);
			
				echo json_encode($tag);
			break;
			
			case "DELETE":
				$drop_id = intval($this->request->param('id', 0));
				$tag_id = intval($this->request->param('id2', 0));

				$this->bucket_service->delete_drop_tag($this->bucket['id'], $drop_id, $tag_id);
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

				$this->bucket_service->delete_drop_place($this->bucket['id'], $drop_id, $place_id);
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
				$link = $this->bucket_service->add_drop_link($this->bucket['id'], $drop_id, $link_data);
			
				echo json_encode($link);
			break;
			
			case "DELETE":
				$drop_id = intval($this->request->param('id', 0));
				$link_id = intval($this->request->param('id2', 0));

				$this->bucket_service->delete_drop_link($this->bucket['id'], $drop_id, $link_id);
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
				$drop_comments = $this->bucket_service->get_drop_comments($this->bucket['id'], $drop_id);
				echo json_encode($drop_comments);
			break;

			case "POST":
				$drop_id = $this->request->param('id', 0);
				$request_body = json_decode($this->request->body(), TRUE);
				$comment = $this->bucket_service->add_drop_comment($this->bucket['id'], $drop_id, $request_body['comment_text']);
				echo json_encode($comment);
			break;
			
			case "DELETE":
				$drop_id = $this->requst->param('id', 0);
				$comment_id = $this->request->param('id2', 0);
				$this->bucket_service->delete_drop_comment($this->bucket['id'], $drop_id, $comment_id);
			break;
		}
	}

}
