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
		$droplets_array = Model_River::get_droplets($river->id, $page);

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
		
		// Generate the List HTML
		// $droplets_list = View::factory('pages/droplets/list')
		// 	->bind('droplets', $droplets)
		// 	->bind('view_more_url', $view_more_url)
		// 	->bind('buckets', $buckets);
		
		$droplet_js = View::factory('common/js/droplets')
		    ->bind('fetch_url', $fetch_url);
		
		$fetch_url = url::site().$this->account->account_path.'/river/droplets/'.$id;
		
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
		$filters_url = url::site().$this->account->account_path.'/river/filters/'.$id;
		$settings_url = url::site().$this->account->account_path.'/river/settings/'.$id;
		$more_url = url::site().$this->account->account_path.'/river/more/'.$id;
		$view_more_url = url::site().$this->account->account_path.'/river/index/'.$id.'?page='.($page+1);
		
	}
	
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$river_id = $this->request->param('id', 0);
		
		$droplets = Model_River::get_droplets($river_id);
		
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

		// Load the template and set the values
		$settings = View::factory('pages/river/settings_control')
			->bind('channels', $this->channels)
			->bind('river', $river)
			->bind('post', $post)
			->bind('base_url', $base_url);
		
		$base_url = URL::site().$this->account->account_path.'/river/';
		
		// Get the ID of the river
		$id = (int) $this->request->param('id', 0);
		
		// Get River (if set)
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();
		
		$post = array();
		
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
			$channel_filters = $river->channel_filters->find_all();
			foreach ($channel_filters as $filter)
			{
				$filter_options = Model_Channel_Filter::get_channel_filter_options($filter->channel, 
				    $river->id);
				
				$post[$filter->channel] = $filter_options;
			}
		}

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
			// Verify that the river identified by 'id' exists for this account
			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['river_id'])
				->where('account_id', '=', $this->account->id)
				->find();
			
			if ($river->loaded())
			{
				// Marshall the channel options - structure them into the standard
				// format for representing plugin config data
				$filter_options = $this->_marshall_channel_options($_POST['options']);
				
				/**
				 * Execute the 'pre_save' event -- execute before saving a river
				 * This event allows plugins to validate the channel options before
				 * they are saved
				 */
				Swiftriver_Event::run('swiftriver.river.pre_save', $filter_options);
				
				// Save channel filters
				$this->_save_filters($filter_options, $river);
			
				echo json_encode(array('success' => TRUE));
			}		
		}
		else
		{
			echo json_encode(array("success" => FALSE));
		}
	}

	/**
	 * Generates the data for channel filter options
	 *
	 * @param array $options The list of submitted channel options
	 * @return array An array of channel options per channel, FALSE otherwise
	 */
	private function _marshall_channel_options($options)
	{
		$channel_options = array();
		$group_count = 0;
		
		// Start with the singles
		foreach ($options['singles'] as $option)
		{
			$name = explode("_", $option['name']);
			$value = $option['value'];
			
			// Get the channel config - always the first element in the array
			$config = $this->channels[$name[0]];
			if ( ! isset($channel_options[$name[0]]))
			{
				$options[$name[0]] = array();
			}
			
			// Single option item
			$channel_options[$name[0]][] = array(
				$name[1] => array(
					'label' => $config['options'][$name[1]]['label'],
					'type' => $name[2],
					'value' => $value
				)
			);
		}
		
		// Tackle the groups
		if (isset($options['groups']))
		{
			foreach ($options['groups'] as $k => $group)
			{
				$group_items = array();
				$group_name = '';
				$channel = '';
				$config = NULL;
			
				// Process channel options for each group
				foreach ($group as $option)
				{
					$names = explode("_", $option['name']);
					$value = $option['value'];
				
					// Get the config for the plugin - only when the
					if ($channel != $names[0])
					{
						$channel = $names[0];
						$group_name = $names[1];
						$config = $this->channels[$channel];
					}
				
					// Store the items of the group
					$group_items[$names[2]] = array(
						'label' => $config['options'][$names[2]]['label'],
						'type' => $names[3],
						'value' => $value
					);
				}
			
				// Save the group items
				$channel_options[$channel][$k][$group_name] = $group_items;
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
	
	
	/**
	 * Renders the UI for a channel option
	 * @return void
	 */
	public function action_ajax_channel_option_ui()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// HTML for the UI
		$ui_html = '';
		
		// Get the channel config
		$channel = $_REQUEST['channel'];
		$config = $this->channels[$channel];
		
		// Get the item no. and lookup option
		$item_no = intval($_REQUEST['item_no']);
		$lookup_option = $_REQUEST['option'];
		
		// Check if the channel options are grouped
		if (isset($config['group']))
		{
			if ($config['group']['key'] == $lookup_option)
			{
				// Generate the CSS ID
				$css_id = $lookup_option."-".$item_no;
				
				$ui_html = "<div id=\"".$css_id."\" class=\"group-item\">"
				    ."<h2>"
				    .$config['group']['label']
					."<span>[<a href=\"javascript:channelOptionR('".$css_id."');\">&mdash;</a>]</span>"
				    ."</h2>";
				
				$i = 1;
				foreach ($config['options'] as $key => $option)
				{
					// Build the name of the option
					$option_name = sprintf("%s_%s_%s_%s_%d_%d", $channel, $lookup_option, $key, 
					    $option['type'], $item_no, $i);
					
					$ui_html .= "<div class=\"input\">"
					    ."<h3>".$option['label']."</h3>"
					    .Swiftriver_Plugins::get_channel_option_html($option, $option_name)
					    ."</div>";
					$i++;
				}
			
				$ui_html .= "</div>";
			}
		}
		else
		{
			// Get the configured option
			$option = $config['options'][$lookup_option];
			
			// CSS id for the parent <div>
			$css_id = "channel-option-".$item_no;
			
			// Build the option name
			$option_name = sprintf("%s_%s_%s_%d", $channel, $lookup_option, $option['type'], $item_no);
			
			$ui_html .= "<div class=\"input single\" id=\"".$css_id."\">"
			    ."<h3>"
			    .$option['label']
			    ."<span>[<a href=\"javascript:javascriptOptionR('".$css_id."')\">&mdash;</a>]</span>"
			    ."</h3>"
			    .Swiftriver_Plugins::get_channel_option_html($option, $option_name)
			    ."</div>";
		}
		
		// Render the UI
		echo json_encode(array("success" => TRUE, "html" => $ui_html));
	}

}