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
	 * No. of completed tasks
	 * @var int
	 */
	private static $_completed = array();
	
	/**
	 * List of registered tasks
	 * @var array
	 */
	private static $_tasks = array();
		
	/**
	 * Registers a task to be executed in the background
	 *
	 * @param string $function_name Name of the worker to execute
	 * @param string $data Raw data string to be passed to the callback
	 */
	public static function register_task($function_name, $data = NULL)
	{
		if (empty(self::$client))
		{
			 self::_init_gearman_client();
		}
		
		// Add background task
		if ( ! array_key_exists($function_name, self::$_tasks))
		{
			// Register the event and its data
			$data = (empty($data) OR is_string($data))? $data : serialize($data);
			self::$_tasks[$function_name] = $data;
		}
		
	}
	
	/**
	 * Runs the registered background tasks
	 *
	 * @return bool TRUE on succeed, FALSE otherwise
	 */
	public static function run_tasks()
	{
		// Add the tasks for background execution
		foreach (self::$_tasks as $task => $data)
		{
			if ( ! in_array($task, self::$_completed))
			{
				self::$client->addTaskBackground($task, $data);
			}
		}
		
		// Register the callback function to be called when execution completes
		self::$client->setCompleteCallback(array('Swiftriver_Task_Manager', 'init_queue_processor'));
	
		// Callback function to be called when task does not complete successfully
		self::$client->setFailCallback(array('Swiftriver_Task_Manager', 'log_failures'));
	
		// Run all the background tasks
		if ( ! self::$client->runTasks())
		{
			// Log the error
			Kohana::$log->add(Log::ERROR, 'Gearman Error: :error', 
				array(':error' => self::$client->error()));
		
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Callback to be invoked when a task is completed 
	 */
	public static function init_queue_processor(GearmanTask $task)
	{
		// Increment the no. of completed
		self::$_completed[] = $task->functionName();
		
		if (count(self::$_completed) == count(self::$_tasks))
		{
			// Reset the list of completed tasks
			self::$_completed = array();
		}
		
		// Process the queue as each task completes
		// Log message
		Kohana::$log->add(Log::INFO, 'Processing the droplet queue');
		
		// Process the queue
		self::$client->doBackground('process_queue', NULL);
	}
	
	/**
	 * Callback to be invoked when a task does not complete successfully
	 *
	 * @param GearmanTask $task
	 */
	public static function log_failures(GearmanTask $task)
	{
		if ($task->returnCode() != GEARMAN_SUCCESS)
		{
			// Increment the counter for no. of completed jobs
			self::$_completed[] = $task->functionName();
			
			// Log the error
			Kohana::$log->add(Log::ERROR, 'Gearman task not completed successfully: :error', 
				array(':error' => self::$client->error()));
		}
	}
	
	/**
	 * Initialzies the Gearman client that will register the tasks
	 */
	private static function _init_gearman_client()
	{
		self::$client = new GearmanClient();
		
		// TODO Fetch the list of servers from a config file
		self::$client->addServer();
	}
}

?>