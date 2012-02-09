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
	private $_river;
	
	/*
	* Boolean indicating whether the logged in user owns the river
	*/
	private $owner = FALSE; 

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
		$this->_river = ORM::factory('river')
			->where('id', '=', $river_id)
			->find();
		
		// Action involves a specific river, check permissions
		if ($river_id)
		{
			if (! $this->_river->loaded())
			{
				// Redirect to the dashboard
				$this->request->redirect('dashboard');
			}
					
			// Is the logged in user an owner
			if ( $this->_river->is_owner($this->user->id)) 
			{
				$this->owner = TRUE;
			}
			
			// If this river is not public and no ownership...
			if( ! $this->_river->river_public AND ! $this->owner)
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
		$river_id = $this->_river->id;
		
		$this->template->content = View::factory('pages/river/main')
			->bind('river', $this->_river)
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
		$bucket_list = json_encode($this->user->get_buckets());
		$droplet_js = View::factory('pages/droplets/js/droplets')
		    ->bind('fetch_url', $fetch_url)
		    ->bind('tag_base_url', $tag_base_url)
		    ->bind('droplet_list', $droplet_list)
		    ->bind('bucket_list', $bucket_list)
		    ->bind('max_droplet_id', $max_droplet_id);
		    		
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
		    $droplets_array = Model_River::get_droplets_since_id($this->_river->id, $since_id);
		}
		else
		{
		    $droplets_array = Model_River::get_droplets($this->_river->id, $page, $max_id);
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
			->bind('errors', $errors);

		$this->template_type = 'dashboard';
		$this->active = 'rivers';

		// Get the settings control		
		$settings_control = View::factory('pages/river/settings_control')
		    ->bind('settings_js', $settings_js);
		
		$settings_js = $this->_get_settings_js_view();
		
		// Disable available channels by default
		foreach ($this->channels as $key => $channel)
		{
			$this->channels[$key]['enabled'] = 0;
		}

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
		
		// Load the view for the settings UI
		$settings = View::factory('pages/river/settings_control')
		                 ->bind('collaborators_control', $collaborators_control)
		                 ->bind('settings_js', $settings_js);		
		$settings_js = $this->_get_settings_js_view();
		
		$collaborators_control = View::factory('template/collaborators')
		                             ->bind('collaborator_list', $collaborator_list)
		                             ->bind('fetch_url', $fetch_url);
		$collaborator_list = json_encode($this->_river->get_collaborators());
		$fetch_url = $this->base_url.'/'.$this->_river->id.'/collaborators';
		
		echo $settings;
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
			echo json_encode(Model_User::get_like($query));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				$collaborator_id = intval($this->request->param('user_id', 0));
				$collaborator_orm = ORM::factory('user', $collaborator_id);
				
				if ( ! $collaborator_orm->loaded()) 
					return;
					
				if ($this->_river->has('collaborators', $collaborator_orm))
				{
					$this->_river->remove('collaborators', $collaborator_orm);
				}
			break;
			
			case "PUT":
				$collaborator_id = intval($this->request->param('user_id', 0));
				$collaborator_orm = ORM::factory('user', $collaborator_id);
				$this->_river->add('collaborators', $collaborator_orm);
			break;
		}
	}	
	
	
	/**
	 * Generates the view for the settings JavaScript
	 * @return View
	 */
	private function _get_settings_js_view()
	{
		// JavaScript for settings UI
		$settings_js = View::factory('pages/river/js/settings')
		    ->bind('channels_url', $channels_url)
		    ->bind('save_settings_url', $save_settings_url)
		    ->bind('delete_river_url', $delete_river_url);

		// URLs for XHR endpoints
		$channels_url = $this->base_url.'/channels/'.$this->_river->id;
		$save_settings_url = $this->base_url.'/save_settings/'.$this->_river->id;
		$delete_river_url = $this->base_url.'/delete/'.$this->_river->id;
		
		return $settings_js;
	}
	
	/**
	 * Gets the list of available channels for the specified river
	 */
	public function action_channels()
	{
		// Check for HTTP POST data
		// Dirty!
		if (isset($_POST['command']) AND $_POST['command'] == 'update_status')
		{
			$this->action_update_channel_status();
			return;
		}
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Store for the channel config data
		$channels_config = array();
		
		$exists = $this->_river->loaded();
		
		// Get the list of channels for the current river
		$river_channels = $this->_river->get_channel_filters();
		
		foreach (array_keys($this->channels) as $channel)
		{
			$filter_options = array();
			$switch_class = "switch-off";
			
			// Check if the channel has been added to the current river
			if (array_key_exists($channel, $river_channels))
			{
				$filter_options = Model_Channel_Filter::get_channel_filter_options($channel, 
				    $this->_river->id);
				
				// on/off state for the channel on the UI
				$switch_class = ($river_channels[$channel] == 0)
				    ? 'switch-off' 
				    : 'switch-on';
			}
			
			// Update the configuration for the river's channels
			$channels_config[] = array(
				'channel' => $channel,
				'channel_name' => $this->channels[$channel]['name'],
				'grouped' => isset($this->channels[$channel]['group']),
				
				'group_key' => isset($this->channels[$channel]['group']) 
				    ? $this->channels[$channel]['group']['key'] 
				    : "",
				
				'group_label' => isset($this->channels[$channel]['group']) 
				    ? $this->channels[$channel]['group']['label'] 
				    : "",
				
				'switch_class' => $switch_class,
				'channel_data' => $filter_options,
				'config_options' => $this->channels[$channel]['options']
			);
		}
		echo json_encode($channels_config);
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
		echo View::factory('pages/river/more_control');
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

		if ($this->_river->loaded())
		{
			$this->_river->delete();
			echo json_encode(array("success"=>TRUE));
		}
		else
		{
			echo json_encode(array("success"=>FALSE));
		}
	}
	
	/**
	 * Enables/disables channel filters for a river
	 */
	public function action_update_channel_status()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$succeed = FALSE;
		
		if ($this->_river->loaded())
		{
			// Get enable/disable flag
			$enabled = $_REQUEST['enabled'];
			$channel  = $_REQUEST['channel'];
			
			// Modify the status of the channel
			$succeed = $this->_river->modify_channel_status($channel, $this->user->id, $enabled);
		
		}
		
		echo json_encode(array('success' => $succeed));
	}
	
	/**
	 * Saves the settings for the river. The data to be saved must be
	 * submitted via HTTP POST. The UI invokes this URL via an XHR
	 */
	public function action_save_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$status_data = array(
			"success" => FALSE,
			"redirect_url" => ""
		);
		
		// Check for new river
		if ($_POST)
		{
			$river = ORM::factory('river');
			$post = $river->validate(Arr::extract($_POST, array('river_name')));

			// Swiftriver Plugin Hook -- execute before saving a river
			// Allows plugins to perform further validation checks
			// ** Plugins can then use 'swiftriver.river.save' after the river
			// has been saved
			Swiftriver_Event::run('swiftriver.river.pre_save', $post);

			if ($post->check())
			{
				$river->river_name = $post['river_name'];
				$river->account_id = $this->account->id;
				$this->_river = $river->save();
				
				if (isset($_POST["selected_channels"]))
				{
					foreach ($_POST["selected_channels"] as $channel => $status)
					{
						$this->_river->modify_channel_status($channel, $this->user->id, $status);
					}
				}
				
				// Modify the status data
				$status_data["success"] = TRUE;
				$status_data["redirect_url"] = URL::site().
				    $this->account->account_path."/river/index/".$river->id;
			}
		}
		
		// Proceed to save the channels
		if (isset($_POST["channels"]) AND $this->_river->loaded())
		{
			// Marshall the channel options - structure them into the standard
			// format for representing plugin config data
			$filter_options = $this->_marshall_channel_options($_POST['channels']);
			
			/**
			 * Execute the 'pre_save' event -- execute before saving a river
			 * This event allows plugins to validate the channel options before
			 * they are saved
			 */
			Swiftriver_Event::run('swiftriver.channel.pre_save', $filter_options);
			
			// Save channel filters
 			$this->_save_filters($filter_options, $this->_river);
			
			$status_data["success"] = TRUE;
		
		}
		
		echo json_encode($status_data);
	}

	/**
	 * Packs the channel configuration params into an array that can ba be 
	 * passed on to _save_filters for subsequent saving to the database
	 *
	 * @param array $options The list of submitted channel options
	 * @return array An array of channel options per channel, FALSE otherwise
	 */
	private function _marshall_channel_options($options)
	{
		$channel_options = array();
		
		foreach ($options as $channel => $channel_data)
		{
			$channel_options[$channel] = array();
			
			foreach ($channel_data as $k => $v)
			{
				// Check for grouped options
				$has_group  = isset($v['group']) AND $v['group'] == TRUE;
				
				// Store each individual config params in a key->value format
				// where key is the name of the config param and value is an
				// array of the param's label, form field type 
				// and field value
				if ($has_group)
				{
					$grouped_options = array();
					foreach ($v['data'] as $group_key => $group_item)
					{
						$grouped_options[$group_item["key"]] = array(
							"label" => $group_item["label"],
							"type" => $group_item["type"],
							"value" => $group_item["value"]
						);
					}
					
					$channel_options[$channel][$k][$v['groupKey']] = $grouped_options;
					
				}
				else
				{
					$channel_options[$channel][] = array(
						$v["key"] => array(
							"label" => $v["label"],
							"type" => $v["type"],
							"value" => $v["value"]
						)
					);
				}
			}
		}
		
		return $channel_options;
	}
	
	/**
	 * Save the rivers channel filters
	 *
	 * @param	array $filter_options List of validated channel options
	 * @param	Model_River $river ORM instance of a river
	 * @return	void
	 */
	private function _save_filters($filter_options, $river)
	{
		if ($filter_options AND $river)
		{
			$channel_name = '';
			$channel_filter = NULL;
			
			foreach ($filter_options as $channel => $data)
			{
				// Check if the channel name has been set
				if ($channel_name != $channel)
				{
					$channel_name = $channel;
					
					// Find the channel filter
					$channel_filter = ORM::factory('channel_filter')
					    ->where('river_id', '=', $river->id)
					    ->where('channel', '=', $channel)
						->find();
						
					// 2. Save Channel Filter Options
					// Better to reset all the filter options before a new save
					
					DB::delete('channel_filter_options')
						->where('channel_filter_id', '=', $channel_filter->id)
						->execute();
				}
				
				// 	Save the channel filter
				if ( ! $channel_filter->loaded())
				{
					$channel_filter->channel = $channel;
					$channel_filter->river_id = $river->id;
					$channel_filter->user_id = $this->user->id;
					$channel_filter->save();
				}
				
				// Save the channel options
				foreach ($data as $k => $item)
				{
					foreach ($item as $option => $values)
					{
						$channel_filter_option = new Model_Channel_Filter_Option();
						$channel_filter_option->channel_filter_id = $channel_filter->id;
						$channel_filter_option->key = $option;
						$channel_filter_option->value = json_encode($values);
						$channel_filter_option->save();
					}
				}
			} // endforeach
		} // endif;
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
			
			if ($this->_river->loaded() AND $droplet->loaded())
			{
				switch ($action)
				{
					// Remove droplet from the river
					case 'remove':
						$this->_river->remove('droplets', $droplet);
						$response["success"] = TRUE;
					break;
				}
			}
		}
		
		echo json_encode($response);
	}

}