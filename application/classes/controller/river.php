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
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Get all available channels from plugins
		$this->channels = Swiftriver_Plugins::channels();
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/river/main')
			->bind('river', $river)
			->bind('droplets', $droplets)
			->bind('droplets_list', $droplets_list)
			->bind('filtered_total', $filtered_total)
			->bind('meter', $meter)
			->bind('filters_url', $filters_url)
			->bind('settings_url', $settings_url)
			->bind('more_url', $more_url);

		// First we need to make sure this river
		// actually exists
		$id = (int) $this->request->param('id', 0);
		
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();
		if ( ! $river->loaded())
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect('dashboard');
		}

		//Use page paramter or default to page 1
		$page = $this->request->query('page') ? $this->request->query('page') : 1;

		//Get Droplets
		$droplets_array = Model_Droplet::get_river($river->id, $page);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		// The Droplets
		$droplets = $droplets_array['droplets'];
		// Total Droplets After Filtering
		$filtered_total = (int) count($droplets);
		
		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplets', $droplets)
			->bind('view_more_url', $view_more_url)
			->bind('buckets', $buckets);

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// URL's to pages that are ajax rendered on demand
		$filters_url = url::site().$this->account->account_path.'/river/filters/'.$id;
		$settings_url = url::site().$this->account->account_path.'/river/settings/'.$id;
		$more_url = url::site().$this->account->account_path.'/river/more/'.$id;
		$view_more_url = url::site().$this->account->account_path.'/river/index/'.$id.'?page='.($page+1);;
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
			->bind('settings_control', $settings_control)
			->bind('errors', $errors);

		// Get the settings control
		$settings_control = View::factory('pages/river/settings_control')
			->bind('channels', $this->channels)
			->bind('post', $post)
			->bind('base_url', $base_url);

		$base_url = URL::site().$this->account->account_path.'/river/';
		
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
			Swiftriver_Event::run('swiftriver.river.pre_save', $post);

			if ($post->check())
			{
				$river->river_name = $post['river_name'];
				$river->account_id = $this->account->id;
				$river->save();

				// Save channel filters
				$this->_save_filters($post, $river);

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

		// Load the template and set the values
		$settings = View::factory('pages/river/settings_control')
			->bind('channels', $this->channels)
			->bind('river', $river)
			->bind('post', $post)
			->bind('base_url', $base_url);
		
		// Get the ID of the river
		$id = (int) $this->request->param('id', 0);
		
		// Get River (if set)
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();
		
		// Verify that the river exists
		if ($river->loaded())
		{
			// Add the enabled property
			$filters = $river->get_channel_filters();
			foreach ($this->channels as $key => $channel)
			{
				if (isset($filters[$key]))
				{
					$this->channels[$key]['enabled'] = $filters[$key];
				}
				else
				{
					$this->channels[$key]['enabled'] = 0;
				}
			}
			
			// Get the list of channel filter options from the DB
			$post = array();
			//$post['filter']
			$channel_filters = $river->channel_filters->find_all();
			foreach ($channel_filters as $filter)
			{
				//$post['filter'][] = $filter->channel;
				// Get Channel Options
				$channel_filter_options = $filter->channel_filter_options->find_all();
				foreach ($channel_filter_options as $option)
				{
					$post['filter'][$filter->channel][$option->key][] = array(
							'value' => $option->value,
							'type' => $option->type
						);
				}
			}
		}

		$base_url = URL::site().$this->account->account_path.'/river/';

		echo $settings;
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
	public function action_ajax_delete()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		if ( $_REQUEST AND isset($_REQUEST['id']) AND
			! empty($_REQUEST['id']) )
		{
			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($river->loaded())
			{
				$river->delete();
				echo json_encode(array("status"=>"success"));
			}
			else
			{
				echo json_encode(array("status"=>"error"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error"));
		}
	}
	
	/**
	 * Enables/disables channel filters for a river
	 */
	public function action_ajax_channels()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$succeed = FALSE;
		
		if (isset($_REQUEST['river_id']))
		{
			// TODO - Validation checks for the river id
			
			// Get enable/disable flag
			$enabled = $_REQUEST['enabled'];
			$channel  = $_REQUEST['channel'];
			
			// Check if the channel exists
			$filter = ORM::factory('channel_filter')
				->where('channel', '=', $channel)
				->where('user_id', '=', $this->user->id)
				->where('river_id', '=', $_REQUEST['river_id'])
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
				// Create a new channel fitler
				$filter = new Model_Channel_Filter();
				$filter->channel = $channel;
				$filter->river_id = $_REQUEST['river_id'];
				$filter->user_id = $this->user->id;
				$filter->filter_enabled = $enabled;
				$filter->filter_date_add = date('Y-m-d H:i:s');
				$filter->save();
				$succeed = TRUE;
			}
		}
		
		echo json_encode(array('success' => $succeed));
	}
	
	/**
	 * Adds/Removes channel filter settings via ajax
	 */
	public function action_ajax_channel_filters()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		if ($_POST AND isset($_REQUEST['river_id']))
		{
			// Verify that the river identified by 'id' exists
			// for this account
			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['river_id'])
				->where('account_id', '=', $this->account->id)
				->find();
			if ($river->loaded())
			{
				$post = Validation::factory($_POST);

				// Swiftriver Plugin Hook -- execute before saving a river
				// Allows plugins to perform further validation checks
				// ** Plugins can then use 'swiftriver.river.save' after the river
				// has been saved
				Swiftriver_Event::run('swiftriver.river.pre_save', $post);

				if ($post->check())
				{
					// Save channel filters
					$this->_save_filters($post, $river);

					echo json_encode(array('status' => "success"));

				}
				else
				{
					//validation failed, get errors
					$errors = $post->errors('river');

					echo json_encode(array("status"=>"error", "errors" => $errors));
				}
			}		
		}
	}

	/**
	 * Save the rivers channel filters
	 *
	 * @param	POST $post object
	 * @param	RIVER $river object
	 * @return	void
	 */
	private function _save_filters($post = NULL, $river = NULL)
	{
		if ($post AND $river)
		{
			if (isset($post['filter']) AND is_array($post['filter']))
			{
				foreach ($post['filter'] AS $channel => $options)
				{
					// 1. Save Channel Filter
					$channel_filter = ORM::factory('channel_filter')
						->where('river_id', '=', $river->id)
						->where('channel', '=', $channel)
						->find();
					if ( ! $channel_filter->loaded() )
					{
						$channel_filter->channel = $channel;
						$channel_filter->river_id = $river->id;
						$channel_filter->user_id = $this->user->id;
						$channel_filter->save();
					}

					// 2. Save Channel Filter Options
					// Better to reset all the filter options before a new save
					DB::delete('channel_filter_options')
						->where('channel_filter_id', '=', $channel_filter->id)
						->execute();

					// Loop through each option
					foreach ($options as $key => $option)
					{
						// Loop through each of the return values
						foreach ($option as $input)
						{
							$channel_filter_option = ORM::factory('channel_filter_option');
							$channel_filter_option->channel_filter_id = $channel_filter->id;
							$channel_filter_option->key = $key;
							$channel_filter_option->value = $input['value'];
							$channel_filter_option->type = $input['type'];
							$channel_filter_option->save();
						}
					}
				}
			}
		}
	}
	
	/**
	 * Return GeoJSON representation of the river
	 *
	 */
	public function action_geojson() {
	    $id = (int) $this->request->param('id', 0);
	    
	    $droplets_array = Model_Droplet::get_geo_river($id);
	    
	    //Prepare the GeoJSON object
	    $ret{'type'} = 'FeatureCollection';
	    $ret{'features'} = array();
	    
	    //Add each droplet as a feature with point geometry and the droplet details
	    //as the feature attributes
	    foreach ($droplets_array['droplets'] as $droplet) 
	    {
	        $geo_droplet['type'] = 'Feature';
	        $geo_droplet['geometry'] = array(
	            'type' => 'Point',
	            'coordinates' => array($droplet['longitude'], $droplet['latitude'])
	        );
	        $geo_droplet['properties'] = array(
	            'droplet_id' => $droplet['id'],
	            'droplet_title' => $droplet['droplet_title'],
	            'droplet_content' => $droplet['droplet_content']
	        );
	        $ret{'features'}[] = $geo_droplet;
	    }
        
        $this->auto_render = false;
        echo json_encode($ret);
    }
	
}