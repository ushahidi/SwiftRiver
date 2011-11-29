<?php defined('SYSPATH') or die('No direct script access'); 
/**
 * Helper class for registering and running Gearman tasks
 *
 * @author     Ushahidi Team
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Helpers
 * @copyright  (c) 2008-2011 Ushahidi Inc - http://www.ushahidi.com
 */

class Swiftriver_Task_Manager {
	
	/**
	 * @var GearmanClient
	 */
	private static $client;
	
	/**
	 * List of registered tasks
	 * @var array
	 */
	private static $_tasks = array();
	
	/**
	 * Registers a task to be executed in the background
	 *
	 * @param string $function_name Name of the worker to execute
	 * @param array $data Data to be passed to the worker function
	 */
	public static function register_task($function_name, $data = NULL)
	{
		if (empty(self::$client))
		{
			 self::_init_gearman_clients();
		}
		
		// Add background task
		if ( ! array_key_exists($function_name, self::$_tasks))
		{
			if (empty($data))
			{
				// Pass an empty array to the worker function
				$data = serialize(array());
			}
			elseif ( ! empty($data) AND ! is_string($data))
			{
				$data  = serialize($data);
			}
			
			// Add the task to the internal register
			self::$_tasks[$function_name] = $data;
			
			return TRUE;
		}
		
		return FALSE;
		
	}
	
	/**
	 * Runs the registered background tasks
	 *
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public static function run_tasks()
	{
		// Add the tasks for background execution
		$submitted = array();
		foreach (self::$_tasks as $task => $data)
		{
			$handle = self::$client->doBackground($task, $data);
			
			$submitted[$task] = $handle;
		}
		
		// Ensure that the droplet queues are processed
		self::$client->doBackground('on_complete_task', serialize($submitted));
		
		return TRUE;
	}
	
	
	/**
	 * Initialzies the Gearman clients that will register the tasks
	 */
	private static function _init_gearman_clients()
	{
		self::$client = new GearmanClient();
		
		// TODO Fetch the list of servers from a config file
		self::$client->addServer();
		
	}
}

?>