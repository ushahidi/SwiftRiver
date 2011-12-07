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

		// Build River Query
		$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
		                    'droplet_title', 'droplet_content', 
		                    'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
		    ->from('droplets')
		    ->join('channel_filter_droplets', 'INNER')
		    ->on('channel_filter_droplets.droplet_id', '=', 'droplets.id')
	        ->join('channel_filters', 'INNER')
	        ->on('channel_filters.id', '=', 'channel_filter_droplets.channel_filter_id')
	        ->join('identities')
	        ->on('droplets.identity_id', '=', 'identities.id')		    
		    ->where('channel_filters.river_id', '=', $river->id)
		    ->order_by('droplet_date_pub', 'DESC');

		// Clone query before any filters have been applied
		$pre_filter = clone $query;
		$total = (int) $pre_filter->execute()->count();

		// SwiftRiver Plugin Hook -- Hook into River Droplet Query
		//++ Allows for adding for more filters via Plugin
		Swiftriver_Event::run('swiftriver.river.filter', $query);

		// First Pass (Limit 20)
		$query->limit(20);

		// Get our droplets as an Array (not Object)
		$droplets = $query->execute()->as_array();
		$filtered_total = (int) count($droplets);

		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplets', $droplets);

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// URL's to pages that are ajax rendered on demand
		$filters_url = url::site().$this->account->account_path.'/river/filters/'.$id;
		$settings_url = url::site().$this->account->account_path.'/river/settings/'.$id;
		$more_url = url::site().$this->account->account_path.'/river/more/';
	}

	/**
	 * Create a New River
	 *
	 * @return	void
	 */
	public function action_new()
	{
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
		
		foreach ($this->channels as $key => $channel)
		{
			$this->channels[$key]['enabled'] = 0;
		}	

		// save the river
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
			$filters = ORM::factory('channel_filter')
					->select('channel_filter.channel', array('channel_filter_options.id', 'filter_option_id'), 
						'channel_filter_options.key', 'channel_filter_options.value')
					->join('channel_filter_options', 'INNER')
					->on('channel_filter_options.channel_filter_id', '=', 'channel_filter.id')
					->where('channel_filter.user_id', '=', $this->user->id)
					->where('channel_filter.river_id', '=', $river->id)
					->find_all();
			
			// Filter options for the channels
			$post = array();
			
			// Store the fetched filter options in a key->value array
			foreach ($filters as $filter_option)
			{
				if ( ! isset($filter_options[$filter_option->channel]))
				{
					$post[$filter_option->channel] = array();
				}
				
				// Add the filter options
				$post[$filter_option->channel][] = array(
					'key' => $filter_option->key,
					'value' => $filter_option->value
				);
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
		
		if ($_REQUEST AND isset($_REQUEST['river_id']))
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
	 * Adds/Removes channel options via ajax
	 */
	public function action_ajax_channel_options()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		$success = FALSE;
		
		// Check for input variables
		if (isset($_REQUEST['river_id']))
		{
			// Verify that the river identified by 'id' exists
			$river = ORM::factory('river', $_REQUEST['river_id']);
			if ($river->loaded() AND isset($_REQUEST['options']))
			{
				foreach ($_REQUEST['options'] as $key => $filter_option)
				{
					// Import the variables from the $filter_option array
					extract($filter_option);
					
					if ( ! empty($filter_option_id))
					{
						// TODO - Validation
						// Update channel filter option
						$orm = ORM::factory('channel_filter_option', $filter_option_id);
						$orm->key = $filter_option_key;
						$orm->value = $filter_option_value;
						$orm->save();
						$success = TRUE;
					}
					else
					{
						// Check if the specified channel filter exists for the current user
						// and river
						$channel_filter = ORM::factory('channel_filter')
							->where('channel', '=', $filter_channel)
							->where('river_id', '=', $river->id)
							->where('user_id', '=', $this->user->id)
							->find();
						
						if ($channel_filter->loaded())
						{
							// TODO - Apply validation rules
							$orm = new Model_Channel_Filter_Option();
							$orm->channel_filter_id = $channel_filter->id;
							$orm->key = $filter_option_key;
							$orm->value = $filter_option_value;
							$orm->save();
							
							$success = TRUE;
						}
					} // endforeach
				} // endif
			} // endif
		} // endif
		
		echo json_encode(array('success' => $success));
	}
	
	/**
	 * Deletes a channel filter option via Ajax
	 */
	public function action_ajax_delete_option()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$success = FALSE;
		if (isset($_REQUEST['filter_option_id']))
		{
			// Delete the filter option
			$option = ORM::factory('channel_filter_option', $_REQUEST['filter_option_id']);
			
			$success = $option->delete()? TRUE : FALSE;
		}
		
		echo json_encode(array('success' => $success));
	}
	
}