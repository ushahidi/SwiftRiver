<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Controller - Handles Individual Buckets
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
class Controller_Bucket extends Controller_Swiftriver {

	/**
	 * Bucket currently being viewed
	 * @var Model_Bucket
	 */
	protected $bucket;
	
	/**
	 * Boolean indicating whether the logged in user owns the bucket
	 * or is a collaborator
	 * @var bool
	 */
	private $owner = FALSE; 
	
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the river name from the url
		$bucket_name_url = $this->request->param('name');
		$action = $this->request->action();
		
		$this->bucket = ORM::factory('bucket')
			->where('bucket_name_url', '=', $bucket_name_url)
			->where('account_id', '=', $this->visited_account->id)
			->find();

		if ($bucket_name_url AND ! $this->bucket->loaded() AND $action != 'manage')
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect($this->dashboard_url);
		}
		
		if ($this->bucket->loaded())
		{
			// Is the logged in user owner / collaborator?
			if ($this->bucket->is_owner($this->user->id))
			{
				$this->owner = TRUE;
			}
			
			// Bucket isn't published and logged in user isn't owner
			if ( ! $this->bucket->bucket_publish AND ! $this->owner)
			{
				$this->request->redirect($this->dashboard_url);
			}
			
			// Set the base url for this specific bucket
			$this->bucket_base_url = $this->base_url.'/'.$this->bucket->bucket_name_url;
		}
	}

	public function action_index()
	{
		if ($this->bucket->account->user->id == $this->user->id)
		{
			$this->template->header->title = $this->bucket->bucket_name;
		}
		else
		{
			$this->template->header->title = $this->bucket->account->account_path.' / '.$this->bucket->bucket_name;
		}
		
		$this->template->content = View::factory('pages/bucket/main')
			->bind('bucket', $this->bucket)
			->bind('droplets_list', $droplets_list)
			->bind('discussion_url', $discussion_url)
			->bind('settings_url', $settings_url)
			->bind('more', $more)
			->bind('owner', $this->owner)
			->bind('user', $this->user);
			
        // The maximum droplet id for pagination and polling
		$max_droplet_id = Model_Bucket::get_max_droplet_id($this->bucket->id);
				
		//Get Droplets
		$droplets_array = Model_Bucket::get_droplets($this->user->id, $this->bucket->id, 1, $max_droplet_id);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
				
		// Bootstrap the droplet list
		$droplet_list = @json_encode($droplets);
		$bucket_list = json_encode($this->user->get_buckets_array());
		$droplet_js = View::factory('pages/droplets/js/droplets')
		        ->bind('fetch_base_url', $fetch_base_url)
		        ->bind('droplet_list', $droplet_list)
		        ->bind('bucket_list', $bucket_list)
		        ->bind('max_droplet_id', $max_droplet_id)
		        ->bind('user', $this->user);
		
		$fetch_base_url = $this->bucket_base_url;
		$droplet_js->filters = NULL;
				
		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplet_js', $droplet_js)
			->bind('user', $this->user)
			->bind('owner', $this->owner)
		    ->bind('anonymous', $this->anonymous);
		
		// Nothing to display message
		$droplets_list->nothing_to_display = View::factory('pages/bucket/nothing_to_display')
		    ->bind('message', $nothing_to_display_message);
		$nothing_to_display_message = __('There are no drops in this bucket yet.');
		if ($this->owner)
		{
			$nothing_to_display_message .= __(' Add some from your :rivers', 
			                                      array(':rivers' => HTML::anchor($this->dashboard_url.'/rivers', __('rivers'))));
		}		

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Links to ajax rendered menus
		$settings_url = $this->bucket_base_url.'/settings';
		$discussion_url = $this->bucket_base_url.'/discussion';
		$more = $this->bucket_base_url.'/more';
	}
	
	/**
	 * Create new bucket page
	 */
	public function action_new()
	{
		$this->template->header->title = __('New Bucket');
		
		// Only account owners are alllowed here
		if ( ! $this->account->is_owner($this->visited_account->user->id) OR $this->anonymous)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template->content = View::factory('pages/bucket/new')
		    ->bind('template_type', $this->template_type)
		    ->bind('user', $this->user)
		    ->bind('active', $this->active)
		    ->bind('post', $post)
		    ->bind('errors', $errors);
		
		$this->template_type = 'dashboard';
		$this->active = 'buckets';

		// Check for form submission
		if ($_POST)
		{
			// Extract the posted data
			$data = Arr::extract($_POST, array('bucket_name', 'bucket_description'));
			
			try
			{
				// Save the bucket
				$bucket = ORM::factory('bucket');
				$bucket->bucket_name = $data['bucket_name'];
				$bucket->bucket_description = $data['bucket_description'];
				$bucket->account_id = $this->account->id;
				$bucket->user_id = $this->user->id;            
				$bucket->save();
				Request::current()->redirect(URL::site().$bucket->account->account_path.'/bucket/'.$bucket->bucket_name_url);
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$errors = array(__("A bucket with the name ':name' already exists", 
				                                array(':name' => $bucket->bucket_name)
				));
			}
		}
		
	}
	
	/**
	 * Generates the view for the settings JS
	 *
	 * @return View
	 */
	private function _get_settings_js_view()
	{
		// Javascript view
		$settings_js  = View::factory('pages/bucket/js/settings')
		   ->bind('bucket_url_root', $bucket_url_root)
		   ->bind('bucket_data', $bucket_data);

		$bucket_url_root = $this->bucket_base_url.'/save_settings';

		$bucket_data = json_encode(array(
			'id' => $this->bucket->id,
			'bucket_name' => $this->bucket->bucket_name,
			'bucket_publish' => $this->bucket->bucket_publish
		));

		return $settings_js;
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
				$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
				$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
				$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
				
				
				$droplets_array = array();
				if ($since_id)
				{
				    $droplets_array = Model_Bucket::get_droplets_since_id($this->user->id, $this->bucket->id, $since_id);
				}
				else
				{
				    $droplets_array = Model_Bucket::get_droplets($this->user->id, $this->bucket->id, $page, $max_id);
				}
        		
				$droplets = $droplets_array['droplets'];
				//Throw a 404 if a non existent page is requested
				if ($page > 1 AND empty($droplets))
				{
				    throw new HTTP_Exception_404(
				        'The requested page :page was not found on this server.',
				        array(':page' => $page)
				        );
				}
				
				
				echo json_encode($droplets);
			break;
			
			case "PUT":
				// No anonymous actions
				if ( ! $this->anonymous)
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
				
				ORM::factory('bucket', $this->bucket->id)->remove('droplets', $droplet_orm);
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
				$droplet['droplet_date_pub'] = date('Y-m-d H:i:s', time());
				$droplet['droplet_orig_id'] = 0;
				$droplet['droplet_locale'] = 'en';
				$droplet['identity_orig_id'] = $this->user->id;
				$droplet['identity_username'] = $this->user->username;
				$droplet['identity_name'] = $this->user->name;
				$droplet['identity_avatar'] = Swiftriver_Users::gravatar($this->user->email, 80);
				// Set the river id
				$droplet['bucket_id'] = $this->bucket->id;
				// Add the droplet to the queue
				$droplet_orm = Swiftriver_Dropletqueue::add($droplet);
				echo json_encode(array(
					'id' => $droplet_orm->id,
					'channel' => $droplet['channel'],
					'identity_avatar' => $droplet['identity_avatar'],
					'identity_name' => $droplet['identity_name'],
					'droplet_date_pub' => $droplet['droplet_date_pub'],
					'droplet_content' => $droplet['droplet_content']
				));
			break;
		}
	}
	
	/**
	 * Ajax rendered discussion control box
	 * 
	 * @return	void
	 */
	public function action_discussion()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/bucket/discussion_control');
	}
	

	/**
	 * Ajax rendered settings control box
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$settings_control = View::factory('pages/bucket/settings_control')
		                        ->bind('collaborators_control', $collaborators_control)
		                        ->bind('bucket', $this->bucket)
		                        ->bind('settings_js', $settings_js);
		
		// Javascript view
		$settings_js  = $this->_get_settings_js_view();
		
		$collaborators_control = View::factory('template/collaborators')
		                             ->bind('collaborator_list', $collaborator_list)
		                             ->bind('fetch_url', $fetch_url)
		                             ->bind('logged_in_user_id', $logged_in_user_id);

		$collaborator_list = json_encode($this->bucket->get_collaborators());
		$fetch_url = $this->bucket_base_url.'/collaborators';
		$logged_in_user_id = $this->user->id;
		
		
		echo $settings_control;	
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
			echo json_encode(Model_User::get_like($query, array($this->user->id, $this->bucket->account->user->id)));
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
					
				$collaborator_orm = $this->bucket->bucket_collaborators->where('user_id', '=', $user_orm->id)->find();
				if ($collaborator_orm->loaded())
				{
					$collaborator_orm->delete();
					Model_User_Action::delete_invite($this->user->id, 'bucket', $this->bucket->id, $user_orm->id);
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
				
				$collaborator_orm = ORM::factory("bucket_collaborator")
									->where('bucket_id', '=', $this->bucket->id)
									->where('user_id', '=', $user_orm->id)
									->find();
				
				if ( ! $collaborator_orm->loaded())
				{
					$collaborator_orm->bucket = $this->bucket;
					$collaborator_orm->user = $user_orm;
					$collaborator_orm->save();
					Model_User_Action::create_action($this->user->id, 'bucket', $this->bucket->id, $user_orm->id);
				}
			break;
		}
	}

	/**
	 * Ajax Title Editing Inline
	 *
	 * Edit Bucket Name
	 * 
	 * @return	void
	 */
	public function action_ajax_title()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		if (
			$_REQUEST AND
			isset($_REQUEST['edit_id'], $_REQUEST['edit_value']) AND
			! empty($_REQUEST['edit_id']) AND ! empty($_REQUEST['edit_value'])
			)
		{

			$bucket = ORM::factory('bucket')
				->where('id', '=', $_REQUEST['edit_id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($bucket->loaded())
			{
				$bucket->bucket_name = $_REQUEST['edit_value'];
				$bucket->save();
			}
		}
	}	

	/**
	 * XHR endpoint for bucket operations. Returns a JSON object containing 
	 * the status of the operation and any redirect URLs, to the client
	 * 
	 * @return void
	 */
	public function action_save_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Check for permissions
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		// Check for the request method
		switch ($this->request->method())
		{
			// Update an existing bucket
			case "PUT":
				if ($this->bucket->loaded())
				{
					$post = json_decode($this->request->body(), TRUE);

					if (isset($post['name_only']) AND $post['name_only'])
					{
						$this->bucket->bucket_name = $post['bucket_name'];
						$this->bucket->save();

						// Modify the bucket base URL
						$this->bucket_base_url = $this->base_url.'/'.$this->bucket->bucket_name_url;

						// Response to be pushed back to the client
						$this->response->body(json_encode(
							array(
								'redirect_url' => $this->bucket_base_url
							)
						));
					}
					elseif (isset($post['privacy_only']) AND $post['privacy_only'])
					{
						$this->bucket->bucket_publish = $post['bucket_publish'];
						$this->bucket->save();
					}
					else
					{
						// Bad request
						$this->response->status(400);
					}
				}
				else
				{
					// Bad request
					$this->response->status(400);
				}

			break;

			// Delets a bucket from the database
			case "DELETE":
				if ($this->bucket->loaded())
				{
					$this->bucket->delete();
					$this->response->body(json_encode(
						array(
							"redirect_url" => URL::site($this->bucket_base_url)
						)
					));
				}
				else
				{
					// Bad request
					$this->response->status(400);
				}
			break;
		}
		
	}
	
	/**
	 * Bucket management restful API
	 * 
	 */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
			case "GET":
				echo json_encode($this->user->get_buckets_array());
			break;

			case "POST":
				// No anonymous buckets
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				$bucket_array = json_decode($this->request->body(), TRUE);
				try
				{
					$bucket_array['user_id'] = $this->user->id;
					$bucket_array['account_id'] = $this->account->id;
					$bucket_orm = Model_Bucket::create_from_array($bucket_array);
					echo json_encode($bucket_orm->as_array());
				}
				catch (ORM_Validation_Exception $e)
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array();
					foreach ($e->errors('validation') as $message) {
						$errors[] = $message;
					}
					echo json_encode(array('errors' => $errors));
				}
				catch (Database_Exception $e)
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array(__("A bucket with the name ':name' already exists", 
					                                array(':name' => $bucket_array['bucket_name']
					)));
					echo json_encode(array('errors' => $errors));
				}
			break;
		}
	}
	
}