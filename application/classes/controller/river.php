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
	private $river;
	
	/**
	 * Boolean indicating whether the logged in user owns the river
	 * @var bool
	 */
	private $owner = FALSE; 

	/**
	 * Whether the river is newly created
	 * @var bool
	 */
	private $is_newly_created = FALSE;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the id (if set)
		$river_id = intval($this->request->param('id', 0));
		
		// This check should be made when this controller is accessed
		// and the database id of the rive is non-zero
		$this->river = ORM::factory('river')
			->where('id', '=', $river_id)
			->find();
		
		// Action involves a specific river, check permissions
		if ($river_id)
		{
			if (! $this->river->loaded())
			{
				// Redirect to the dashboard
				$this->request->redirect('dashboard');
			}
					
			// Is the logged in user an owner
			if ( $this->river->is_owner($this->user->id)) 
			{
				$this->owner = TRUE;
			}
			
			// If this river is not public and no ownership...
			if( ! $this->river->river_public AND ! $this->owner)
			{
				$this->request->redirect('dashboard');			
			}
		}

		// Get all available channels from plugins
		$this->channels = Swiftriver_Plugins::channels();
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
	    
		// Get the id of the current river
		$river_id = $this->river->id;
		
		$this->template->content = View::factory('pages/river/main')
			->bind('river', $this->river)
			->bind('droplets', $droplets)
			->bind('droplet_list_view', $droplet_list_view)
			->bind('filtered_total', $filtered_total)
			->bind('meter', $meter)
			->bind('filters_url', $filters_url)
			->bind('settings_url', $settings_url)
			->bind('more_url', $more_url)
			->bind('owner', $this->owner);
				
		// The maximum droplet id for pagination and polling
		$max_droplet_id = Model_River::get_max_droplet_id($river_id);
				
		//Get Droplets
	    $droplets_array = Model_River::get_droplets($river_id, 1, $max_droplet_id);
		
		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
		
		// Total Droplets After Filtering
		$filtered_total = count($droplets);
				
		// Bootstrap the droplet list
		$droplet_list = json_encode($droplets);
		$bucket_list = json_encode($this->user->get_buckets_array());
		$droplet_js = View::factory('pages/droplets/js/droplets')
		    ->bind('fetch_url', $fetch_url)
		    ->bind('tag_base_url', $tag_base_url)
		    ->bind('droplet_list', $droplet_list)
		    ->bind('bucket_list', $bucket_list)
		    ->bind('max_droplet_id', $max_droplet_id)
		    ->bind('user', $this->user);
		    		
		// Turn on Ajax polling
		$polling_enabled = "true";
		$fetch_url = $this->base_url.'/droplets/'.$river_id;
		$tag_base_url = $this->base_url.'/droplets/'.$river_id;
		$droplet_action_url = $this->base_url.'/ajax_droplet/'.$river_id;
		
		$droplet_list_view = View::factory('pages/droplets/list')
		    ->bind('droplet_js', $droplet_js)
		    ->bind('user', $this->user)
		    ->bind('owner', $this->owner);

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// URL's to pages that are ajax rendered on demand
		$filters_url = $this->base_url.'/filters/'.$river_id;		
		$settings_url = $this->base_url.'/settings/'.$river_id;
		$more_url = $this->base_url.'/more/'.$river_id;
	}
	
	/**
	 * XHR endpoint for fetching droplets
	 */
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		//Use page paramter or default to page 1
		$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
		$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
		$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
		
		$droplets_array = array();
		if ( $since_id )
		{
		    $droplets_array = Model_River::get_droplets_since_id($this->river->id, $since_id);
		}
		else
		{
		    $droplets_array = Model_River::get_droplets($this->river->id, $page, $max_id);
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
	 * Create a New River
	 *
	 * @return	void
	 */
	public function action_new()
	{
		// The main template
		$this->template->content = View::factory('pages/river/new')
			->bind('post', $post)
			->bind('template_type', $this->template_type)
			->bind('user', $this->user)
			->bind('active', $this->active)
			->bind('settings_control', $settings_control)
			->bind('errors', $errors)
			->bind('is_new_river', $is_new_river);
		
		// Check for form submission
		if ($_POST)
		{
			$river = ORM::factory('river');
			$post  = $river->validate(Arr::extract($_POST, array('river_name', 'river_public')));

			if ($post->check())
			{
				$river->river_name = $post['river_name'];
				$river->river_public = $post['river_public'];
				$river->account_id = $this->user->account->id;

				$this->river = $river->save();

				// Mark the river as newly created
				$this->is_newly_created = TRUE;
			}
			else
			{
				$errors = $post->errors();
			}
		}
		
		$is_new_river = ($this->river->loaded() == FALSE);
		$this->template_type = 'dashboard';
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
		echo View::factory('pages/river/filters_control');
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

		echo $this->_get_settings_view();
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
		
		$query = $this->request->query('q') ? $this->request->query('q') : NULL;
		
		if ($query) {
			echo json_encode(Model_User::get_like($query, array($this->user->id, $this->river->account->user->id)));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				$user_id = intval($this->request->param('user_id', 0));
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
				$user_id = intval($this->request->param('user_id', 0));
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
	 * Generates the view for the settings JavaScript
	 * @return View
	 */
	private function _get_settings_view($new = TRUE)
	{
		// Load the view for the settings UI
		$settings = View::factory('pages/river/settings_control')
		    ->bind('settings_js', $settings_js)
		    ->bind('is_newly_created', $this->is_newly_created)
		    ->bind('river', $this->river)
		    ->bind('collaborators_control', $collaborators_control);
		
		$collaborators_control = NULL;
		
		if ( ! $this->is_newly_created)
		{
			$collaborators_control = View::factory('template/collaborators')
					                             ->bind('collaborator_list', $collaborator_list)
					                             ->bind('fetch_url', $fetch_url)
					                             ->bind('logged_in_user_id', $logged_in_user_id);
			$collaborator_list = json_encode($this->river->get_collaborators());
			$fetch_url = $this->base_url.'/'.$this->river->id.'/collaborators';
			$logged_in_user_id = $this->user->id;			
		}
		    
		// JavaScript for settings UI
		$settings_js = View::factory('pages/river/js/settings')
		    ->bind('channels_url', $channels_url)
		    ->bind('channel_options_url', $channel_options_url)
		    ->bind('channels_list', $channels_list)
		    ->bind('river_url_root', $river_url_root)
		    ->bind('river_data', $river_data);
		
		// River data
		$river_data = json_encode(array(
			'id' => $this->river->id, 
			'river_name' => $this->river->river_name, 
			'river_public' => $this->river->river_public
		));

		// URLs for XHR endpoints
		$river_url_root = $this->base_url.'/save_settings';
		$channels_url = $this->base_url.'/channels/'.$this->river->id;
		$channel_options_url = $this->base_url.'/channel_options/'.$this->river->id;

		$channels_list = json_encode($this->river->get_channel_filter_data());
		
		return $settings;
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

		 			$response["success"] = TRUE;
		 			$response["id"] = $channel_filter_orm->id;
	 			break;

	 			case "PUT":
	 			$channel_filter_id = $this->request->param('channel_filter_id', 0);
	 			$channel_filter_orm = ORM::factory('channel_filter', $channel_filter_id);

	 			if ($channel_filter_orm->loaded())
	 			{
	 				$post = json_decode($this->request->body(), TRUE);

	 				$channel_filter_orm->filter_enabled = $post['enabled'];
	 				$channel_filter_orm->save();

	 				$response["success"] = TRUE;

	 			}
	 			break;
	 		}
	 	}

	 	echo json_encode($response);
	 }


	/**
	 * XHR endpoint for adding/remove channel filter option
	 */
	public function action_channel_options()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$response = array("success" => FALSE);

		if ($this->river->loaded())
		{
			switch ($this->request->method())
			{
				// Delete a channel filter option
				case "DELETE":
					$channel_option_id = $this->request->param('channel_option_id', 0);
					$option_orm = ORM::factory('channel_filter_option', $channel_option_id);

					// Verify that the option exists
					if ($option_orm->loaded())
					{
						$option_orm->delete();
						$response["success"] = TRUE;
					}

				break;

				// Create a new channel option
				case "POST":
					$post = json_decode($this->request->body(), TRUE);

					$channel_filter_option = ORM::factory('channel_filter_option');
					$channel_filter_option->channel_filter_id = $post['channel_filter_id'];
					$channel_filter_option->key = $post['key'];
					$channel_filter_option->value = json_encode($post['data']);

					$channel_filter_option->save();

					// Add the ID of the newly created option
					$post["id"] = $channel_filter_option->id;

					$response["success"] = TRUE;
					$response["data"] = $post;

				break;
			}
		}
		
		echo json_encode($response);
		
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
		$river_id = $this->request->param('id', 0);
		echo View::factory('pages/river/more_control')
			->bind('river_id', $river_id);
	}	

	/**
	 * Ajax Title Editing Inline
	 *
	 * Edit River Name
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

			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['edit_id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($river->loaded())
			{
				$river->river_name = $_REQUEST['edit_value'];
				$river->save();
			}
		}
	}

	/**
	 * Ajax Delete River
	 * 
	 * @return string - json
	 */
	public function action_delete()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		if ($this->river->loaded())
		{
			$this->river->delete();
			echo json_encode(array("success"=>TRUE));
		}
		else
		{
			echo json_encode(array("success"=>FALSE));
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
		
		$response = array(
			"success" => FALSE,
			"redirect_url" => ""
		);
		
		// Check for the submitted method
		switch ($this->request->method())
		{
			// Delete river
			case "DELETE":
				if ($this->river->loaded())
				{
					$this->river->delete();

					$response["success"] = TRUE;
					$response["redirect_url"] = URL::site("/dashboard");
				}
			break;

			// Update the river data
			case "PUT":

				if ($this->river->loaded())
				{
					$data = json_decode($this->request->body(), TRUE);
					$post = $this->river->validate($data);

					// Validate
					if ($post->check())
					{
						// Update the river
						if (isset($post['name_only']) AND $post['name_only'])
						{
							$this->river->river_name = $post['river_name'];
							$this->river->save();
						}
						elseif (isset($post['privacy_only']) AND $post['privacy_only'])
						{
							$this->river->river_public = $post['river_public'];
							$this->river->save();
						}

						$response["success"] = TRUE;
						$response["redirect_url"] = $this->base_url.'/index/'.$this->river->id;

					}
				}
			break;
		}

		echo json_encode($response);
	}

	
	public function action_ajax_droplet()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$response = array("success" => FALSE);
		
		if ($_POST)
		{
			$droplet_id = isset($_POST['droplet_id']) ? intval($_POST['droplet_id']) : 0;
			$action = isset($_POST['action']) ? $_POST['action'] : "";
			
			// Load the droplet
			$droplet = ORM::factory('droplet', $droplet_id);
			
			if ($this->river->loaded() AND $droplet->loaded())
			{
				switch ($action)
				{
					// Remove droplet from the river
					case 'remove':
						$this->river->remove('droplets', $droplet);
						$response["success"] = TRUE;
					break;
				}
			}
		}
		
		echo json_encode($response);
	}

}