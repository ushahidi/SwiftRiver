<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Channel Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_River_Channels extends Controller_River_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->river->river_name.' ~ '.__('Channel Settings');
        $this->template->header->js .= HTML::script("themes/default/media/js/channels.js");
		
		$this->active = 'channels';
		$this->settings_content = View::factory('pages/river/settings/channels');		
		$this->settings_content->channels_config = json_encode(Swiftriver_Plugins::channels());
		$this->settings_content->channels = json_encode($this->river->get_channels(TRUE));
		$this->settings_content->base_url = $this->river->get_base_url().'/settings/channels';
		$this->settings_content->river = $this->river;
	}
	
	/**
	  * Channels restful api
	  */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case "POST":			
				$channel_array = json_decode($this->request->body(), TRUE);
				
				$channel_config = Swiftriver_Plugins::get_channel_config($channel_array['channel']);			
				if ( ! $channel_config)
					throw new HTTP_Exception_400();
					
				$channel_orm = $this->river->get_channel($channel_array['channel']);
				
				// Make sure the channel is enabled for the case where a disabled
				// channel is being re-added.
				if ( ! (bool) $channel_orm->filter_enabled)
				{
					$channel_orm->filter_enabled = TRUE;
					$channel_orm->save();
				}
				
				echo json_encode(array(
					'id' => $channel_orm->id,
					'channel' => $channel_orm->channel,
					'name' => $channel_config['name'],
					'enabled' => (bool) $channel_orm->filter_enabled,
					'options' => $this->river->get_channel_options($channel_orm)
				));
			break;
			case "PUT":
				$channel_array = json_decode($this->request->body(), TRUE);
				$channel_orm = $this->river->get_channel($channel_array['channel']);
				$channel_orm->filter_enabled = $channel_array['enabled'];
				$channel_orm->save();
			break;
			case "DELETE":
				$channel_id = intval($this->request->param('id', 0));
				$channel_orm = $this->river->get_channel_by_id($channel_id);
				
				if ($channel_orm)
				{
					$channel_orm->delete();
				}
			break;
		}
	 }
	
	/**
	  * Channel options restful api
	  */
	public function action_options()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if ($this->river->is_expired())
		{
			$this->response->status(400);
			$this->response->headers('Content-Type', 'application/json');
			echo json_encode(array("error" => __("Oops! Your river has already expired.")));

			return;
		}
	
		$channel_id = intval($this->request->param('id', 0));
		$option_id = intval($this->request->param('id2', 0));
				
		$channel = $this->river->get_channel_by_id($channel_id);
		
		if ( ! $channel)
		{
			throw new HTTP_Exception_404();
		}

		switch ($this->request->method())
		{
			case "POST":
			case "PUT":
				try
				{
					$channel_option_array = json_decode($this->request->body(), TRUE);
					
					// Validate option data first
					$channel_option_array['channel'] = $channel->channel;
					Swiftriver_Event::run('swiftriver.channel.option.pre_save', $channel_option_array);
					unset($channel_option_array['channel']);
					
					// Validation passed, save the option
					$channel_option = $channel->update_option($channel_option_array, $option_id);
					
					// Return the new id + updated values
					$option_array = $this->river->get_channel_options($channel, $channel_option->id);
					echo(json_encode($option_array[0]));
				}
				catch (Swiftriver_Exception_Channel_Option $e)
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					echo json_encode(array('error' => $e->getMessage()));
				}
			break;
			case "DELETE":
				$channel->delete_option($option_id);
			break;
		}
	}
	
	/**
	  * File upload end point.
	  */
	public function action_file()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$channel_id = intval($this->request->param('id', 0));
		$channel = $this->river->get_channel_by_id($channel_id);
		
		if ( ! $channel)
		{
			throw new HTTP_Exception_404();
		}
		
		$file = $_FILES['file'];
		
		if ( ! Upload::not_empty($file))
		{
			$this->response->status(400);
			$this->response->headers('Content-Type', 'application/json');
			echo json_encode(array('error' => __("Invalid file")));
			return;
		}
		sleep(5);
		// Pass on the files to plugings
		try 
		{
			$file['key'] = $this->request->post('key');
			Swiftriver_Event::run('swiftriver.channel.option.file', $file);
		}
		catch (SwiftRiver_Exception_Channel_Option $e)
		{
			$this->response->status(400);
			$this->response->headers('Content-Type', 'application/json');
			echo json_encode(array('error' => $e->getMessage()));
			return;
		}
		
		// Create the filter options
		$options_array = array();
		foreach ($file['option_data'] as $option_data)
		{
			// Validate option data first, skip failures
			try
			{
				$channel_option_array['channel'] = $channel->channel;
				Swiftriver_Event::run('swiftriver.channel.option.pre_save', $option_data);

				// Validation passed, save the option
				$channel_option = $channel->update_option($option_data);			

				// Return the new id + updated values
				$option_array = $this->river->get_channel_options($channel, $channel_option->id);
				$options_array[] = $option_array[0];
			}
			catch (SwiftRiver_Exception_Channel_Option $e) 
			{
				// Do nothing
			}
		}
						
		echo json_encode($options_array);
	}
}