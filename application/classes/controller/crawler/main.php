<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Crawler_Main extends Controller {
	
	/**
	 * Gearman worker
	 * @var GearmanWorker
	 */
	private $worker;
	
	/**
	 * Constructor
	 */
	public function action_index()
	{
		// Initialize gearman worker
		$this->worker = new GearmanWorker();
		
		// TODO Configuration option for specifying Gearman servers
		$this->worker->addServer();
		
		// Get all the available services
		$services = Swiftriver_Plugins::channels();
		
		// Check if any services have been found
		if (count($services) == 0)
		{
			Kohana::$log->add(Log::ERROR, 'No channel services found');
			exit;
		}
		
		// Create a worker for each channel
		foreach ($services as $key => $value)
		{
			// Create instance for the channel worker. If not found, the
			// framework will thrown an exception
			$instance = Swiftriver_Worker_Channel::factory($key);
			
			// Log
			Kohana::$log->add(Log::INFO, 'Registering channel worker for :worker', 
				array(':worker' => $key));
			
			// Register the crawler's callback function
			$this->worker->addFunction($key, array($instance, 'channel_worker'));
		}
		
		// Add and register the queue processor
		$this->worker->addFunction('process_queue', array('Swiftriver_Dropletqueue', 'process'));

		// Wait for and perform jobs
		while ($this->worker->work())
		{
			// Listen for failure
			if ($this->worker->returnCode() != GEARMAN_SUCCESS)
			{
				// Log the error!
				Kohana::$log->add(Log::ERROR, 'Gearman worker error :code - :error', 
					array(':code' => $this->worker->returnCode(), ':error' => $this->worker->error()));
			}
		}
	}
	
}
?>