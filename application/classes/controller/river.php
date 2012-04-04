<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_River extends Controller_Swiftriver {

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
	 * Boolean indicating whether the logged in user owns the river
	 * @var bool
	 */
	protected $owner = FALSE; 

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
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the river name from the url
		$river_name_url = $this->request->param('name');
		
		// This check should be made when this controller is accessed
		// and the database id of the rive is non-zero
		$this->river = ORM::factory('river')
			->where('river_name_url', '=', $river_name_url)
			->where('account_id', '=', $this->visited_account->id)
			->find();
		
		// Action involves a specific river, check permissions
		if ($river_name_url)
		{
			if ( ! $this->river->loaded())
			{
				// Redirect to the dashboard
				$this->request->redirect($this->dashboard_url);
			}
					
			// Is the logged in user an owner
			if ($this->river->is_owner($this->user->id)) 
			{
				$this->owner = TRUE;
			}
			
			// If this river is not public and no ownership...
			if( ! $this->river->river_public AND ! $this->owner)
			{
				$this->request->redirect($this->dashboard_url);			
			}
			
			// Set the base url for this specific river
			$this->river_base_url = $this->_get_base_url($this->river);
		}
	}
	
	/**
	 * @return	string
	 */
	protected function _get_base_url($river)
	{
		return URL::site().$river->account->account_path.'/river/'.$river->river_name_url;
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		// Get the id of the current river
		$river_id = $this->river->id;
		
		if ($this->river->account->user->id == $this->user->id OR $this->river->account->user->username == 'public')
		{
			$this->template->header->title = $this->river->river_name;
		}
		else
		{
			$this->template->header->title = $this->river->account->account_path.' / '.$this->river->river_name;
		}
				
		$this->template->content = View::factory('pages/river/main')
			->bind('river', $this->river)
			->bind('droplets', $droplets)
			->bind('droplets_view', $droplets_view)
			->bind('settings_url', $settings_url)
			->bind('owner', $this->owner)
			->bind('user', $this->user)
			->bind('river_base_url', $this->river_base_url);
				
		// The maximum droplet id for pagination and polling
		$max_droplet_id = Model_River::get_max_droplet_id($river_id);
		
		// River filters
		$filters = $this->_get_river_filters();
		
		//Get Droplets
		$droplets_array = Model_River::get_droplets($this->user->id, $river_id, 0, 1, 
			$max_droplet_id, NULL, $filters);
		
		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
		
		// Total Droplets After Filtering
		$filtered_total = count($droplets);
				
		// Bootstrap the droplet list
		$droplet_list = json_encode($droplets);
		$droplet_js = View::factory('pages/drop/js/drops')
		    ->bind('fetch_base_url', $fetch_base_url)
		    ->bind('droplet_list', $droplet_list)
		    ->bind('max_droplet_id', $max_droplet_id)
		    ->bind('user', $this->user);
		    		
		$fetch_base_url = $this->river_base_url;
		
		// Check if any filters exist and modify the fetch urls
		$droplet_js->filters = NULL;
		if ( ! empty($filters))
		{
			$droplet_js->filters = $this->_stringify_filter_params($filters);
		}
		
		// Select droplet list view with drops view as the default if list not specified
		$droplets_view = View::factory('pages/drop/drops');
		$droplets_view->droplet_js = $droplet_js;
		$droplets_view->user = $this->user;
		$droplets_view->owner = $this->owner;
		$droplets_view->anonymous = $this->anonymous;
		
		$droplets_view->nothing_to_display = View::factory('pages/river/nothing_to_display')
		    ->bind('anonymous', $this->anonymous);
		$droplets_view->nothing_to_display->river_url = $this->request->url(TRUE);

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// Determine whether to show the droplet meter
		$show_meter =  ( ! empty($filters) AND $meter > 1);

		// URL's to pages that are ajax rendered on demand
		$filters_url = $this->river_base_url.'/filters';
		$settings_url = $this->river_base_url.'/settings';
		$more_url = $this->river_base_url.'/more';
	}
	
	/**
	* Below are aliases for the index.
	*/
	public function action_drops() {
		$this->action_index();
	}
	public function action_list() {
		$this->action_index();
	}
	public function action_drop() {
		$this->action_index();
	}

	
	/**
	 * XHR endpoint for fetching droplets
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
				if ($drop_id)
				{
					// Specific drop requested
					$droplets_array = Model_River::get_droplets($this->user->id, 
				    	$this->river->id, $drop_id);
					$droplets = array_pop($droplets_array['droplets']);
				}
				else
				{
					//Use page paramter or default to page 1
					$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
					$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
					$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
					$filters = $this->_get_river_filters();

					if ($since_id)
					{
					    $droplets_array = Model_River::get_droplets_since_id($this->user->id, 
					    	$this->river->id, $since_id, $filters);
					}
					else
					{
					    $droplets_array = Model_River::get_droplets($this->user->id, 
					    	$this->river->id, 0, $page, $max_id, NULL, $filters);
					}
					$droplets = $droplets_array['droplets'];
				}				
				

				//Throw a 404 if a non existent page/drop is requested
				if (($page > 1 OR $drop_id) AND empty($droplets))
				{
				    throw new HTTP_Exception_404('The requested page was not found on this server.');
				}
				

				echo json_encode($droplets);

			break;
			
			case "PUT":
				// No anonymous actions
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}
			
				$droplet_array = json_decode($this->request->body(), TRUE);
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('droplet', $droplet_id);
				$droplet_orm->update_from_array($droplet_array);
			break;
			
			case "DELETE":
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('droplet', $droplet_id);
				
				// Does the user exist
				if ( ! $droplet_orm->loaded())
				{
					throw new HTTP_Exception_404(
				        'The requested page :page was not found on this server.',
				        array(':page' => $page)
				        );
				}
				
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				ORM::factory('river', $this->river->id)->remove('droplets', $droplet_orm);
			break;
		}
	}
	
	
	/**
	 * Create a New River
	 *
	 * @return	void
	 */
	public function action_new()
	{
		$this->template->header->title = __('New River');
		
		// Only account owners are alllowed here
		if ( ! $this->account->is_owner($this->visited_account->user->id) OR $this->anonymous)
		{
			throw new HTTP_Exception_403();
		}
		
		// The main template
		$this->template->content = View::factory('pages/river/new')
			->bind('post', $post)
			->bind('template_type', $this->template_type)
			->bind('user', $this->user)
			->bind('active', $this->active)
			->bind('settings_control', $settings_control)
			->bind('errors', $errors)
			->bind('is_new_river', $is_new_river);
		
		$is_new_river = TRUE;
		
		// Check for form submission
		if ($_POST AND Swiftriver_CSRF::valid($_POST['form_auth_id']))
		{
			$post = Arr::extract($_POST, array('river_name', 'river_public'));
			try
			{
				$river = Model_River::create_new($post['river_name'], $post['river_public'], $this->user->account);
            	
				// Redirect to the river view.
				$this->request->redirect(URL::site().$river->account->account_path.'/river/'.$river->river_name_url);
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$errors = array(__("A river with the name ':name' already exists", 
				                                array(':name' => $post['river_name'])
				));
			}
		
		}
				
		$this->active = 'rivers';

		// Get the settings control		
		$settings_control = $this->_get_settings_view();
	}

	/**
	 * Ajax rendered filter control box
	 * 
	 * @return	void
	 */
	public function action_filters()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		$filters_control = View::factory('pages/river/filters_control')
		    ->bind('channel_filters', $channel_filters)
		    ->bind('filter_channel', $filter_channel)
		    ->bind('tags_filter', $tags_filter)
		    ->bind('places_filter', $places_filter);

		$channel_filters =  $this->river->get_channel_filters();

		$cached = array();
		if( $this->cache )
		{
			$cached = $this->cache->get('river.filters');
		}

		$filter_channel = (isset($cached['channel'])) ? $cached['channel'] : '';
		$tags_filter = '';
		$places_filter = '';

		if (isset($cached['tags']))
		{
			$tags_filter = "";
			if ( ! empty($cached['tags']['ids']))
			{
				$ids = $cached['tags']['ids'];
				$tags = DB::select('tag')
				    ->from('tags')
				    ->where('id', 'IN', $ids)
				    ->find_all()
				    ->as_array();

				$tags_filter = implode(",", $tags).", ";
			}

			$tags_filter .= implode(",", $cached['tags']['names']);
		}

		if (isset($cached['places']))
		{
			$places_filter = "";
			if ( ! empty($cached['places']['ids']))
			{
				$ids  =$cached['tags']['ids'];
				$places  = DB::select('place_name')
				    ->from('places')
				    ->where('id', 'IN', $ids)
				    ->find_all()
				    ->as_array();

				$places_filter = implode(",", $places).",";
			}
			$places_filter .= implode(", ", $cached['places']['names']);
		}

		echo $filters_control;
	}
	
	/**
	 * River collaborators restful api
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
			echo json_encode(Model_User::get_like($query, array($this->user->id, $this->river->account->user->id)));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
							
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('user', $user_id);
				
				if ( ! $user_orm->loaded()) 
					return;
					
				$collaborator_orm = $this->river->river_collaborators->where('user_id', '=', $user_orm->id)->find();
				if ($collaborator_orm->loaded())
				{
					$collaborator_orm->delete();
					Model_User_Action::delete_invite($this->user->id, 'river', $this->river->id, $user_orm->id);
				}
			break;
			
			case "PUT":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
			
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('user', $user_id);
				
				$collaborator_orm = ORM::factory("river_collaborator")
									->where('river_id', '=', $this->river->id)
									->where('user_id', '=', $user_orm->id)
									->find();
				
				if ( ! $collaborator_orm->loaded())
				{
					$collaborator_orm->river = $this->river;
					$collaborator_orm->user = $user_orm;
					$collaborator_orm->save();
					Model_User_Action::create_action($this->user->id, 'river', $this->river->id, $user_orm->id);
				}				
			break;
		}
	}
	
	 /**
	  * Tags restful api
	  */ 
	 public function action_tags()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$tag_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				$tag_array = json_decode($this->request->body(), true);
				$tag_name = $tag_array['tag'];
				$account_id = $this->visited_account->id;
				$tag_orm = Model_Account_Droplet_Tag::get_tag($tag_name, $droplet_id, $account_id);
				echo json_encode(array('id' => $tag_orm->tag->id, 'tag' => $tag_orm->tag->tag));
			break;

			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				Model_Droplet::delete_tag($droplet_id, $tag_id, $this->visited_account->id);
			break;
		}
	}
	
	/**
	  * Links restful api
	  */ 
	 public function action_links()
	{
		// Is the logged in user an owner?
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$link_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				$link_array = json_decode($this->request->body(), true);
				$url = $link_array['url'];
				if ( ! Valid::url($url))
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array(__("Invalid url"));
					echo json_encode(array('errors' => $errors));
					return;
				}
				$account_id = $this->visited_account->id;
				$link_orm = Model_Account_Droplet_Link::get_link($url, $droplet_id, $account_id);
				echo json_encode(array('id' => $link_orm->link->id, 'tag' => $link_orm->link->url));
			break;

			case "DELETE":
				Model_Droplet::delete_link($droplet_id, $link_id, $this->visited_account->id);
			break;
		}
	}
	
	/**
	  * Links restful api
	  */ 
	 public function action_places()
	{
		// Is the logged in user an owner?
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$place_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				$places_array = json_decode($this->request->body(), true);
				$place_name = $places_array['place_name'];
				if ( ! Valid::not_empty($place_name))
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array(__("Invalid location"));
					echo json_encode(array('errors' => $errors));
					return;
				}
				$account_id = $this->visited_account->id;
				$place_orm = Model_Account_Droplet_Place::get_place($place_name, $droplet_id, $account_id);
				echo json_encode(array('id' => $place_orm->place->id, 'place_name' => $place_orm->place->place_name));
			break;

			case "DELETE":
				Model_Droplet::delete_place($droplet_id, $place_id, $this->visited_account->id);
			break;
		}
	}
	
	 /**
	  * Replies restful api
	  */ 
	 public function action_reply()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				// Get the POST data
				$droplet = json_decode($this->request->body(), TRUE);
				
				// Set the remaining properties
				$droplet['parent_id'] = intval($this->request->param('id', 0));
				$droplet['droplet_type'] = 'reply';
				$droplet['channel'] = 'swiftriver';
				$droplet['droplet_title'] = $droplet['droplet_content'];
				$droplet['droplet_date_pub'] = gmdate('Y-m-d H:i:s', time());
				$droplet['droplet_orig_id'] = 0;
				$droplet['droplet_locale'] = 'en';
				$droplet['identity_orig_id'] = $this->user->id;
				$droplet['identity_username'] = $this->user->username;
				$droplet['identity_name'] = $this->user->name;
				$droplet['identity_avatar'] = Swiftriver_Users::gravatar($this->user->email, 80);
				// Set the river id
				$droplet['river_id'] = $this->river->id;
				// Add the droplet to the queue
				$droplet_orm = Swiftriver_Dropletqueue::add($droplet);
				
				if ($droplet_orm) 
				{
					echo json_encode(array(
						'id' => $droplet_orm->id,
						'channel' => $droplet['channel'],
						'identity_avatar' => $droplet['identity_avatar'],
						'identity_name' => $droplet['identity_name'],
						'droplet_date_pub' => gmdate('M d, Y H:i:s', time()).' UTC',
						'droplet_content' => $droplet['droplet_content']
					));
				}
				else
				{
					$this->response->status(400);
				}
			break;
		}
	}
	
		
	/**
	 * XHR endpoint for adding/updating channel filters
	 *
	 */
	 public function action_channels()
	 {
	 	$this->template = "";
	 	$this->auto_render = FALSE;

	 	$response = array("success" => FALSE);

	 	if ($this->river->loaded())
	 	{
	 		switch ($this->request->method())
	 		{
	 			// Create a new channel filter for the river
	 			case "POST":
					// Is the logged in user an owner?
					if ( ! $this->owner)
					{
						throw new HTTP_Exception_403();
					}
					
		 			$post = json_decode($this->request->body(), TRUE);

		 			// ORM instance for the filter
		 			$channel_filter_orm = ORM::factory('channel_filter');

		 			// Set properties
		 			$channel_filter_orm->channel = $post['channel'];
		 			$channel_filter_orm->river_id = $this->river->id;
		 			$channel_filter_orm->user_id = $this->user->id;
		 			$channel_filter_orm->filter_enabled = $post['enabled'];

		 			// Save
		 			$channel_filter_orm->save();

		 			$this->response->body(json_encode(array("id" => $channel_filter_orm->id)));
	 			break;
	 			case "PUT":
					// Is the logged in user an owner?
					if ( ! $this->owner)
					{
						throw new HTTP_Exception_403();
					}
					
	 				$channel_filter_id = $this->request->param('id', 0);
	 				$channel_filter_orm = ORM::factory('channel_filter', $channel_filter_id);
                	
	 				if ($channel_filter_orm->loaded())
	 				{
	 					$post = json_decode($this->request->body(), TRUE);
                	
	 					$channel_filter_orm->filter_enabled = $post['enabled'];
	 					$channel_filter_orm->save();
                	
	 				}
	 				else
	 				{
	 					$this->response->status(400);
	 				}
	 			break;
	 		}
	 	}

	 }


	/**
	 * XHR endpoint for adding/remove channel filter option
	 */
	public function action_channel_options()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if ($this->river->loaded())
		{
			switch ($this->request->method())
			{
				// Delete a channel filter option
				case "DELETE":
				
					// Is the logged in user an owner?
					if ( ! $this->owner)
					{
						throw new HTTP_Exception_403();
					}
				
					$channel_option_id = $this->request->param('id', 0);
					$option_orm = ORM::factory('channel_filter_option', $channel_option_id);

					// Verify that the option exists
					if ($option_orm->loaded())
					{
						Swiftriver_Event::run('swiftriver.channel.option.pre_delete', $option_orm);
						$option_orm->delete();
					}
					else
					{
						throw new HTTP_Exception_400();
					}

				break;

				// Create a new channel option
				case "POST":

					// Is the logged in user an owner?
					if ( ! $this->owner)
					{
						throw new HTTP_Exception_403();
					}

					// Check for file upload
					$is_file_upload = (empty($_FILES) == FALSE);

					// Fetch the POST data
					$post = ( ! empty($_FILES))
					    ? array_merge($_FILES, $_POST) 
					    : json_decode($this->request->body(), TRUE);
				

					// Run pre_save events
					Swiftriver_Event::run('swiftriver.channel.option.pre_save', $post);
					
					if ( ! empty($post))
					{
						if (isset($post['multiple']) AND $post['multiple'])
						{
							// Multiple entries specified
							unset ($post['multiple']);

							$entries = array();
							foreach ($post as $entry)
							{
								$filter_option = ORM::factory('channel_filter_option');
								$filter_option->channel_filter_id = $entry['channel_filter_id'];
								$filter_option->key = $entry['key'];
								$filter_option->value = json_encode($entry['data']);
								$filter_option->save();
								
								// Run post_save events
								Swiftriver_Event::run('swiftriver.channel.option.post_save', $filter_option);

								$entry["id"] = $filter_option->id;

								// Add the created entry
								$entries[] = $entry;
							}
							
							// Encode the entries to JSON
							$options_list = json_encode($entries);
							echo "<script type=\"text/javascript\">parent.window.ChannelView.addChannelOptions($options_list);</script>";

							// Halt
							return;
						}
						else
						{
							// Single entry
							$channel_filter_option = ORM::factory('channel_filter_option');
							$channel_filter_option->channel_filter_id = $post['channel_filter_id'];
							$channel_filter_option->key = $post['key'];
							$channel_filter_option->value = json_encode($post['data']);

							$channel_filter_option->save();
							
							// Run post_save events
							Swiftriver_Event::run('swiftriver.channel.option.post_save', $channel_filter_option);

							// Add the ID of the newly created option
							$post["id"] = $channel_filter_option->id;

							echo json_encode($post);
						}
					}
					else
					{
						// Bad request
						throw new HTTP_Exception_400();
					}

				break;
			}
		}
		
	}

	
	/**
	 * Saves the settings for the river. The data to be saved must be
	 * submitted via HTTP POST. The UI invokes this URL via an XHR
	 */
	public function action_save_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// Check for the submitted method
		switch ($this->request->method())
		{
			// Delete river
			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				if ($this->river->loaded())
				{
					$this->river->delete();

					// Encode the response to be returned to the client
					echo json_encode(array(
						"redirect_url" => URL::site($this->dashboard_url)
					));
				}
			break;

			// Update the river data
			case "PUT":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}

				if ($this->river->loaded())
				{
					$post = json_decode($this->request->body(), TRUE);

					// Update the river
					if (isset($post['name_only']) AND $post['name_only'])
					{
						$this->river->river_name = $post['river_name'];
						$this->river->river_name_url = URL::title($post['river_name']);
						$this->river->save();

						// Build out the new base URL for the river
						$this->river_base_url = $this->base_url.'/'.$this->river->river_name_url;

						// Response to be pushed back to the client
						echo json_encode(array('redirect_url' => $this->river_base_url));
					}
					elseif (isset($post['privacy_only']) AND $post['privacy_only'])
					{
						$this->river->river_public = $post['river_public'];
						$this->river->save();
					}
					else
					{
						// Bad request
						$this->response->status(400);
					}
				}
			break;
		}

	}

	/**
	 * Ajax rendered more control box
	 * 
	 * @return	void
	 */
	public function action_more()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/river/more_control')
			->bind('river', $this->river);
	}

	/**
	 * Grabs and packs the place and tag filters from a HTTP GET request
	 */
	private function _get_river_filters()
	{

		$filters = array();
		
		// Get filtering parameters
		$places = $this->request->query('places');
		$tags = $this->request->query('tags');
		$channel = strtolower($this->request->query('channel'));

		// Build the filters array
		if (is_string($places))
		{
			$filters['places'] = explode(",", $places);
		}
		elseif (is_array($places))
		{
			$filters['places'] = $places;
		}

		if (is_string($tags))
		{
			$filters['tags'] = explode(",", $tags);
		}
		elseif (is_array($tags))
		{
			$filters['tags'] = $tags;
		}

		// Sanitize the filters
		$filters = $this->_sanitize_filters($filters);

		// Only filter by a single channel
		$channels = explode(",", $channel);
		$channel = $channels[count($channels) - 1];

		// Add the channel filters
		if (Valid::not_empty($channel) AND (Swiftriver_Plugins::get_channel_config($channel) != FALSE))
		{
			$filters['channel'] = $channel;
		}

		// Cache the filters
		if ($this->cache)
		{
			$this->cache->set('river.filters', $filters);
		}

		return $filters;
	}

	/**
	 * Runs sanitization checks on a set of filter parameters. The
	 * filter parameters are split into two - ids and names - the
	 * former applying to digit values and the latter to strings
	 */
	private function _sanitize_filters($filters)
	{
		$modified = array();
		foreach ($filters as $param => $values)
		{
			// Split each parameter into ids and names
			$modified[$param]['ids'] = array();
			$modified[$param]['names'] = array();

			foreach ($values as $value)
			{
				if (intval($value) > 0)
				{
					$modified[$param]['ids'][] = $value;
				}
				elseif (is_string($value) AND Valid::not_empty($value))
				{
					$modified[$param]['names'][] = trim($value);
				}

			}
		}

		// Sanitization and duplication filtering
		foreach ($modified as $param => & $values)
		{
			$param = & $param;
			foreach ($values as $key => & $data)
			{
				$key = & $key;
				if (Valid::not_empty($data))
				{
					$data = array_unique($data);
				}
				else
				{
					unset ($key);
				}
			}

			if ( ! Valid::not_empty($param))
			{
				unset ($param);
			}
		}

		// Replace the filters with the sanitized set
		return $modified;
	}

	/**
	 * Converts the filter parameters into a url string representation
	 */
	private function _stringify_filter_params($filters)
	{
		// Convert arrays into comma separated strings
		foreach ($filters as $param => $data)
		{
			if (is_array($data))
			{
				$filters[$param] = implode(",", $data['names']);
			}
		}
		
		return http_build_query($filters);
	}

}