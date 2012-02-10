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
		
		// First we need to make sure this bucket exists
		$bucket_id = intval($this->request->param('id', 0));

		$this->bucket = ORM::factory('bucket')
			->where('id', '=', $bucket_id)
			->find();
		
		if ($bucket_id != 0 AND ! $this->bucket->loaded())
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect('dashboard');
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
				$this->request->redirect('dashboard');
			}
		}
	}

	public function action_index()
	{
		$this->template->content = View::factory('pages/bucket/main')
			->bind('bucket', $this->bucket)
			->bind('droplets_list', $droplets_list)
			->bind('discussion_url', $discussion_url)
			->bind('settings_url', $settings_url)
			->bind('more', $more)
			->bind('owner', $this->owner);
			
        // The maximum droplet id for pagination and polling
		$max_droplet_id = Model_Bucket::get_max_droplet_id($this->bucket->id);
				
		//Get Droplets
		$droplets_array = Model_Bucket::get_droplets($this->bucket->id, 1, $max_droplet_id);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
				
		// Bootstrap the droplet list
		$droplet_list = json_encode($droplets);
		$bucket_list = json_encode($this->user->get_buckets());
		$droplet_js = View::factory('pages/droplets/js/droplets')
		        ->bind('fetch_url', $fetch_url)
		        ->bind('tag_base_url', $tag_base_url)
		        ->bind('droplet_list', $droplet_list)
		        ->bind('bucket_list', $bucket_list)
		        ->bind('max_droplet_id', $max_droplet_id);
		
		$fetch_url = $this->base_url.'/droplets/'.$this->bucket->id;
		$tag_base_url = $this->base_url.'/droplets/'.$this->bucket->id;
				
		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplet_js', $droplet_js)
			->bind('user', $this->user)
			->bind('owner', $this->owner);
		

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Links to ajax rendered menus
		$settings_url = $this->base_url.'/settings/'.$this->bucket->id;
		$discussion_url = $this->base_url.'/discussion/'.$this->bucket->id;
		$more = $this->base_url.'/more/';
	}
	
	/**
	 * Create new bucket page
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/bucket/new')
		    ->bind('template_type', $this->template_type)
		    ->bind('user', $this->user)
		    ->bind('active', $this->active)
		    ->bind('post', $post)
		    ->bind('errors', $errors)
		    ->bind('settings_control', $settings_control);
		
		$this->template_type = 'dashboard';
		$this->active = 'buckets';
		
		$settings_control = View::factory('pages/bucket/settings_control')
		                        ->bind('collaborators_control', $collaborators_control)
		                        ->bind('bucket', $this->bucket)
		                        ->bind('settings_js', $settings_js);
							
		// Javascript view
		$settings_js  = $this->_get_settings_js_view();	
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
		    ->bind('collaborator_fetch_url', $collaborator_fetch_url)
		    ->bind('delete_bucket_url', $delete_bucket_url)
		    ->bind('save_settings_url', $save_settings_url);


		// URLs endpoints for XHR actions
		$collaborator_fetch_url = $this->base_url.'/collaborators/'.$this->bucket->id;
		$save_settings_url = $this->base_url.'/save_settings/'.$this->bucket->id;
		$delete_bucket_url = $this->base_url.'/ajax_delete/'.$this->bucket->id;

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
		
		// First we need to make sure this bucket exists
		$id = (int) $this->request->param('id', 0);
		
		$bucket = ORM::factory('bucket')
			          ->where('id', '=', $id)
			          ->find();
		
		if ( ! $bucket->loaded())
		{
			echo json_encode(array());
			return;
		}
		
		$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
		$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
		$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
		
		
		$droplets_array = array();
		if ($since_id)
		{
		    $droplets_array = Model_Bucket::get_droplets_since_id($bucket->id, $since_id);
		}
		else
		{
		    $droplets_array = Model_Bucket::get_droplets($bucket->id, $page, $max_id);
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
	}
	
	/**
	 * Ajax Create New Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_new()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		// save the bucket
		if ($_POST)
		{
			$bucket = ORM::factory('bucket');
			$post = $bucket->validate($_POST);

			if ($post->check())
			{
				$bucket->bucket_name = $post['bucket_name'];
				$bucket->user_id = $this->user->id;
				$bucket->account_id = $this->account->id;
				$bucket->save();
				
				echo json_encode(array(
					"success" => TRUE,
					"bucket" => array(
						'id' => $bucket->id,
						'bucket_name' => $bucket->bucket_name
						)));
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('bucket');
				echo json_encode(array("success" => FALSE, "errors" => $errors));
			}
		}
	}

	/**
	 * Ajax Add Droplet to Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_droplet()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$response = array("success" => FALSE);
		
		// Check for HTTP POST
		if ($_POST)
		{
			// 	Fetch the HTTP POST data
			$bucket_id = isset($_POST['bucket_id']) ? $_POST['bucket_id'] : 0;
			$droplet_id = isset($_POST['droplet_id']) ? $_POST['droplet_id'] : 0;
			$action = isset($_POST['action'])? $_POST['action'] : "";
			
			// If no bucket is loaded or bucket_id is non-zero
			if ($bucket_id != 0 OR ! $this->bucket->loaded())
			{
				$this->bucket = ORM::factory('bucket')
					->where('id', '=', $bucket_id)
					->where('account_id', '=', $this->account->id)
					->find();
			}

			// Get Droplet
			$droplet = ORM::factory('droplet', $droplet_id);

			// Verify that both the bucket and droplet exist
			if ($this->bucket->loaded() AND $droplet->loaded())
			{
				switch ($action)
				{
					// Add droplet to bucket
					case 'add':
						if ( ! $this->bucket->has('droplets', $droplet))
						{
							$this->bucket->add('droplets', $droplet);
						}
						
						$response["success"] = TRUE;
						
						break;
						
					case 'remove':
						$this->bucket->remove('droplets', $droplet);
						$response["success"] = TRUE;
						break;
				}
			}
		}
		
		// Output response
		echo json_encode($response);
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
		$fetch_url = $this->base_url.'/'.$this->bucket->id.'/collaborators';
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
		
		if ($query) {
			echo json_encode(Model_User::get_like($query, array($this->user->id, $this->bucket->account->user->id)));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				$user_id = intval($this->request->param('user_id', 0));
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
				$user_id = intval($this->request->param('user_id', 0));
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
		if ($_REQUEST AND
			isset($_REQUEST['edit_id'], $_REQUEST['edit_value']) AND
			! empty($_REQUEST['edit_id']) AND 
			! empty($_REQUEST['edit_value']) )
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
	public function action_api()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$response = array("success" => FALSE);


		switch ($this->request->method())
		{
			// Update an existing bucket
			case "PUT":
			break;

			// Add a new collaborator to the bucket
			case "POST":
				// Get the POST data
				$post = json_decode($this->request->body(), TRUE);

				if ($this->bucket->loaded())
				{
					// Add user to the list of collaborators
					if ($this->bucket->add_collaborator($post['collaborator']['id']))
					{
						$response["success"] = TRUE;
					}
				}
				else
				{
					// 
					// Step 1 - Creating a new bucket
					// 
					if (array_key_exists('bucket_name', $post))
					{
						// Model_Bucket instance to be used for save
						$bucket = ORM::factory('bucket');

						// Set up validation
						$validate = $bucket->validate(Arr::extract($post, 
							array('bucket_name', 'bucket_description')));
						
						// Validate!
						if ($validate->check())
						{
							$bucket->bucket_name = $post['bucket_name'];

							$bucket->bucket_description = isset($post['bucket_description']) 
							    ? $post['bucket_description'] 
							    : '';
							
							$bucket->account_id = $this->account->id;
							$bucket->user_id = $this->user->id;
							
							// Save the bucket
							$this->bucket = $bucket->save();
							
							// Set the values for the JSON response
							$response["success"] = TRUE;
							$response["redirect_url"] = $this->base_url.'/index/'.$bucket->id;
						}
					}

					// 
					// Step 2 - Check for collaborators
					// 
					if ($this->bucket->loaded() AND array_key_exists('collaboratrs', $post))
					{
						// Add each of the specified collaborators and trap for
						// exception
						try 
						{
							foreach ($post['collaborators'] as $collaborator)
							{
								$this->bucket->add_collaborator($collaborator['id']);
							}
						}
						catch (Database_Exception $e)
						{
							// Fail!
							$response["success"] = FALSE;

							// Log the error
							Kohana::$log->add(Log::ERROR, $e->getMessage());
						}
					}

				}
				
			break;


			// Remove a bucket or collaborator from bucket
			case "DELETE":
				
				if (array_key_exists('collaborator_id', $this->request->param()))
				{
					// Get the collaboration id
					$collaborator_id = intval($this->request->param('collaborator_id', 0));

					// Remove the collaboration
					if ($this->bucket->remove_collaborator($collaborator_id))
					{
						$response["success"] = TRUE;
					}
				}
				else
				{
					$this->bucket->delete();
					$response["success"] = TRUE;
					$response["redirect_url"] = url::site('dashboard/buckets');
				}

			break;
		}
		
		echo json_encode($response);

	}
	
	/**
	 * Returns a JSON response of the list of buckets accessible to the 
	 * currently logged in user
	 */
	public function action_list_buckets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
		    case "GET":
		       echo json_encode($this->user->get_buckets());
		    break;
		    case "POST":
		        $bucket_array = json_decode($this->request->body(), true);
		        $bucket_array['user_id'] = $this->user->id;
		        $bucket_array['account_id'] = $this->account->id;
		        $bucket_orm = Model_Bucket::create_from_array($bucket_array);
		        echo json_encode($bucket_orm->as_array());
		    break;
		}
	}
	
}