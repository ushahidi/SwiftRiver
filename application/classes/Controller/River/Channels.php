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
	  * Channels restful api
	  */
	public function action_index()
	{
		$this->template = "";
		$this->auto_render = FALSE;

	 }
	
	/**
	  * Channel options restful api
	  */
	public function action_options()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		sleep(5);

		switch ($this->request->method())
		{
			case "DELETE":			
				$channel_id = intval($this->request->param('id', 0));				
				$this->riverService->delete_channel($this->river['id'], $channel_id);
			break;
			case "POST":			
				$channel_array = json_decode($this->request->body(), TRUE);
				
				$channel_config = Swiftriver_Plugins::get_channel_config($channel_array['channel']);			
				if ( ! $channel_config)
					throw new HTTP_Exception_400();
				
				try 
				{
					$channel_array = $this->riverService->create_channel_from_array($this->river['id'], $channel_array);
					echo json_encode($channel_array);
				} 
				catch (Swiftriver_Exception_Channel_Option $e)
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					echo json_encode(array('error' => $e->getMessage()));
				}
			break;
			case "PUT":
				$channel_id = intval($this->request->param('id', 0));				
				$channel_array = json_decode($this->request->body(), TRUE);
				
				$channel_config = Swiftriver_Plugins::get_channel_config($channel_array['channel']);			
				if ( ! $channel_config)
					throw new HTTP_Exception_400();
				
				try 
				{
					$channel_array = $this->riverService->update_channel_from_array($this->river['id'], $channel_id, $channel_array);
					echo json_encode($channel_array);
				} 
				catch (Swiftriver_Exception_Channel_Option $e)
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					echo json_encode(array('error' => $e->getMessage()));
				}
			break;
			default:
				throw new HTTP_Exception_405();
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