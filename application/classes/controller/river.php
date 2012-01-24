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
			->where('account_id', '=', $this->account->id)
			->find();
		
		if ($river_id != 0 AND ! $this->_river->loaded())
		{
			// Redirect to the dashboard
			$this->request->redirect('dashboard');
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
			->bind('droplets_list', $droplets_list)
			->bind('filtered_total', $filtered_total)
			->bind('meter', $meter)
			->bind('filters_url', $filters_url)
			->bind('settings_url', $settings_url)
			->bind('more_url', $more_url);

		//Use page paramter or default to page 1
		$page = $this->request->query('page') ? $this->request->query('page') : 1;
				
		//Get Droplets
		$droplets_array = Model_River::get_droplets($river_id, $page);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
		
		// Total Droplets After Filtering
		$filtered_total = count($droplets);
		
		//Throw a 404 if a non existent page is requested
		if ($page > 1 AND empty($droplets))
		{
		    throw new HTTP_Exception_404(
		        'The requested page :page was not found on this server.',
		        array(':page' => $page)
		        );
		}
		
		$droplet_js = View::factory('common/js/droplets')
		    ->bind('fetch_url', $fetch_url);
		
		$fetch_url = url::site().$this->account->account_path.'/river/droplets/'.$river_id;
		
		$droplets_list = View::factory('pages/droplets/list')
		    ->bind('droplet_js', $droplet_js);

		// $buckets = ORM::factory('bucket')
		// 	->where('account_id', '=', $this->account->id)
		// 	->find_all();

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// URL's to pages that are ajax rendered on demand
		$filters_url = url::site().$this->account->account_path.'/river/filters/'.$river_id;
		$settings_url = url::site().$this->account->account_path.'/river/settings/'.$river_id;
		$more_url = url::site().$this->account->account_path.'/river/more/'.$river_id;
		$view_more_url = url::site().$this->account->account_path.'/river/index/'.$river_id.'?page='.($page+1);
		
	}
	
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$droplets = Model_River::get_droplets($this->_river->id, $page);
		
		echo json_encode($droplets['droplets']);
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
		
		$settings_js = View::factory('pages/river/js/settings')
		    ->bind('channels_url', $channels_url)
		    ->bind('save_settings_url', $save_settings_url);

		$channels_url = URL::site().$this->account->account_path.'/river/channels';
		$save_settings_url = URL::site().$this->account->account_path.'/river/save_settings';
		
		// Disable available channels by default
		foreach ($this->channels as $key => $channel)
		{
			$this->channels[$key]['enabled'] = 0;
		}

		// Save the river
		if ($_POST)
		{
			$river = ORM::factory('river');
			$post = $river->validate($_POST);

			// Swiftriver Plugin Hook -- execute before saving a river
			// Allows plugins to perform further validation checks
			// ** Plugins can then use 'swiftriver.river.save' after the river
			// has been saved
			// Swiftriver_Event::run('swiftriver.river.pre_save', $post);

			if ($post->check())
			{
				$river->river_name = $post['river_name'];
				$river->account_id = $this->account->id;
				$river->save();

				// Save channel filters
				// $this->_save_filters($post, $river);

				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('river/index/'.$river->id);
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('river');
			}

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
			->bind('settings_js', $settings_js);
		
		// JavaScript for settings UI
		$settings_js = View::factory('pages/river/js/settings')
		    ->bind('channels_url', $channels_url)
		    ->bind('save_settings_url', $save_settings_url);
		
		// URL for fetching the channels for the river
		$channels_url = URL::site().$this->account->account_path.'/river/channels/'.$this->_river->id;
		$save_settings_url = URL::site().$this->account->account_path.'/river/save_settings/'.$this->_river->id;
		echo $settings;
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
		
		// Get the list of channel filters and their options
		$filters = ($exists) 
		    ? $this->_river->channel_filters->find_all()
		    : array_keys($this->channels);
		
		if (count($filters) == 0)
		{
			$exists = FALSE;
			$filters = array_keys($this->channels);
		}
		
		foreach ($filters as $filter)
		{
			// Get the channel name
			$channel = ($exists) ? $filter->channel : $filter;
			
			$filter_options = array();
			$switch_class = "switch-off";
			
			// Check if the channel's plugin is enabled in the system config
			if ($exists)
			{
				$filter_options = Model_Channel_Filter::get_channel_filter_options($filter->channel, 
				    $this->_river->id);
				
				// on/off state for the channel on the UI
				$switch_class = ($filter->filter_enabled == 0)
				    ? 'switch-off' 
				    : 'switch-on';
			}
			
			if (isset($this->channels[$channel]))
			{
				$channels_config[] = array(
					'channel' => $channel,
					'channel_name' => $this->channels[$channel]['name'],
					'switch_class' => $switch_class,
					'channel_data' => $filter_options,
					'config_options' => $this->channels[$channel]['options']
				);
			}
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

		if ($river->_river->loaded())
		{
			$river->delete();
			echo json_encode(array("status"=>"success"));
		}
		else
		{
			echo json_encode(array("status"=>"error"));
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
		
			// Check if the channel exists
			$filter = ORM::factory('channel_filter')
				->where('channel', '=', $channel)
				->where('user_id', '=', $this->user->id)
				->where('river_id', '=', $this->_river->id)
				->find();
		
			if ($filter->loaded())
			{
				// Modify existing channel fitler
				$filter->filter_enabled = $enabled;
				$filter->filter_date_modified = date('Y-m-d H:i:s');
				$filter->save();
			
				$succeed = TRUE;
			}
			else
			{
				try {
					// Create a new channel fitler
					$filter = new Model_Channel_Filter();
					$filter->channel = $channel;
					$filter->river_id = $this->_river->id;
					$filter->user_id = $this->user->id;
					$filter->filter_enabled = $enabled;
					$filter->filter_date_add = date('Y-m-d H:i:s');
					$filter->save();
					$succeed = TRUE;
				}
				catch (Kohana_Exception $e)
				{
					// Catch and log exception
					Kohana::$log->add(Log::ERROR, 
					    "An error occurred while enabling/disabling the channel: :error",
					    array(":error" => $e->getMessage())
					);
				
					$succeed = FALSE;
				}
			}
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

		if ($_POST AND $this->_river->loaded())
		{
			// Marshall the channel options - structure them into the standard
			// format for representing plugin config data
			$filter_options = $this->_marshall_channel_options($_POST['channels']);
			
			/**
			 * Execute the 'pre_save' event -- execute before saving a river
			 * This event allows plugins to validate the channel options before
			 * they are saved
			 */
			Swiftriver_Event::run('swiftriver.river.pre_save', $filter_options);
			
			// Save channel filters
			$this->_save_filters($filter_options, $this->_river);
		
			echo json_encode(array('success' => TRUE));
		}
		else
		{
			echo json_encode(array("success" => FALSE));
		}
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
				// Store each individual config params in a key->value format
				// where key is the name of the config param and value is an
				// array of the param's label, form field type 
				// and field value
				$channel_options[$channel][] = array(
					$v["key"] => array(
						"label" => $v["label"],
						"type" => $v["type"],
						"value" => $v["value"]
					)
				);
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

}