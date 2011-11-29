<?php defined('SYSPATH') or die('No direct script access allowed'); 

/**
 * Base class for all channel worker classes
 *
 * @author    Ushahidi Team
 * @package   SwiftRiver - https://github.com/ushahidi/Swiftriver_v2
 * @category  Helpers
 * @copyright (c) 2008-2011 Ushahidi Inc - http://www.ushahidi.com
 */

abstract class Swiftriver_Channel_Worker {
	
	/**
	 * Creates an returns a new task channel
	 * @param string $channel Name of the channel
	 * @return Swifriver_Task_Channel
	 */
	public static function factory($channel)
	{
		// Set class name
		$channel = 'Swiftriver_Channel_Worker_'.ucfirst($channel);
		return new $channel;
	}
	
	/**
	 * Performs the actual work of crawling/fetching content specific to 
	 * a channel. All direct sub-classes of this class must implement this method
	 *
	 * @param GearmanJob $job GearmanJob with the workload to be acted on
	 */
	public abstract function channel_worker($job);
	
	/**
	 * Callback function for processing the droplet queues of the 
	 * various channels.
	 *
	 * @param GearmanJob $job
	 */
	public static function on_complete_task($job)
	{
		$tasks = unserialize($job->workload());
		
		$client = new GearmanClient();
		$client->addServer();
		
		while (count($tasks))
		{
			foreach ($tasks as $channel => $handle)
			{
				// Get the status of the background job 
				$status = $client->jobStatus($handle);
				
				// Check if the job exists
				if ( ! $status[0])
				{
					// Delete the channel from the list of tasks
					unset ($tasks[$channel]);
					
					Swiftriver_Dropletqueue::process($channel);
					
					continue;
				}
			}
			
			// Take a chill pill...
			sleep(15);
		}
	}

}
?>