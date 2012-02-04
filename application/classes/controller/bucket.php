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
			->where('account_id', '=', $this->account->id)
			->find();
		
		if ($bucket_id != 0 AND ! $this->bucket->loaded())
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect('dashboard');
		}
	}

	public function action_index()
	{
		$this->template->content = View::factory('pages/bucket/main')
			->bind('bucket', $this->bucket)
			->bind('droplets_list', $droplets_list)
			->bind('settings_url', $settings_url)
			->bind('more', $more);
			
		
		//Get Droplets
		$droplets_array = Model_Bucket::get_droplets($this->bucket->id);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
				
		// Bootstrap the droplet list
		$droplet_list = json_encode($droplets);
		$bucket_list = json_encode($this->user->get_buckets());
		$droplet_js = View::factory('common/js/droplets')
				->bind('fetch_url', $fetch_url)
				->bind('droplet_list', $droplet_list)
    		    ->bind('bucket_list', $bucket_list);
		
		$fetch_url = $this->base_url.'/droplets/'.$this->bucket->id;
				
		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplet_js', $droplet_js);
		

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Links to ajax rendered menus
		$settings = $this->base_url.'/settings/'.$this->bucket->id;
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
			->where('account_id', '=', $this->account->id)
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
		if ( $since_id )
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
	 * Ajax rendered settings control box
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$settings_control = View::factory('pages/bucket/settings_control')
		    ->bind('bucket', $this->bucket)
		    ->bind('settings_js', $settings_js);
		
		// Javascript view
		$settings_js  = $this->_get_settings_js_view();
		
		echo $settings_control;	
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
	 * Ajax Delete Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_delete()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$response = array("success" => FALSE);
		
		// Check if the bucket exists
		if ($this->bucket->loaded())
		{
			$this->bucket->delete();
			$response["success"] = TRUE;
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
	
	/**
	 * Returns a JSON response with the list of users collaborating on
	 * the current bucket
	 */
	public function action_collaborators()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$collaborators = Model_Bucket::get_collaborators($this->bucket->id);
		
		echo json_encode($collaborators);
	}
	
	/**
	 * XHR endpoint for saving the bucket settings
	 */
	public function action_save_settings()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Default response
		$response = array("success" => FALSE, "redirect_url" => "");
		
		// Only HTTP POST requests are serviced 
		if ($_POST)
		{
			// New bucket
			if ( ! $this->bucket->loaded())
			{
				$bucket = ORM::factory('bucket');
				$post = $bucket->validate(Arr::extract($_POST, array('bucket_name', 'bucket_description')));
				
				if ($post->check())
				{
					$bucket->bucket_name = $post['bucket_name'];
					$bucket->bucket_description = $post['bucket_description'];
					$bucket->account_id = $this->account->id;
					$bucket->user_id = $this->user->id;
					
					// Save the bucket
					$this->bucket = $bucket->save();
					
					// Set the values for the JSON response
					$response["success"] = TRUE;
					$response["redirect_url"] = $this->base_url.'index/'.$bucket->id;
				}
			}
			
		}
		
		echo json_encode($response);
	}
}