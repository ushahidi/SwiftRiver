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

// Check if the PCNTL functions exist - Sorry Windows peeps!
if ( ! function_exists('pcntl_fork'))
{
	Kohana::$log->add(Log::ERROR, 'PCNTL functions are not available in this PHP installation');
	exit;
}

declare(ticks = 1);

class Controller_Crawler_Main extends Controller {
	
	/**
	 * Gearman worker
	 * @var GearmanWorker
	 */
	private $worker;
	
	/**
	 * Process ID of the current process
	 * @var int
	 */
	private $current_pid;
	
	/**
	 * List of currently forked processes
	 * @var array
	 */
	private $current_procs = array();
	
	/**
	 * Processes that have exited before the parent
	 * @var array
	 */
	private $signal_queue = array();
	
	/**
	 * @return void
	 */
	public function before()
	{	
		parent::before();
		
		// Get the current process id
		$this->current_pid = getmypid();
		
		// Register the signal handler
		pcntl_signal(SIGCHLD, array($this, 'handle_child_signal'));
		
		// Initialize gearman worker
		$this->worker = new GearmanWorker();
		
		// TODO - Configuration option for specifying Gearman servers
		$this->worker->addServer();
		
		// Prevent child processes from forking stuff
		if ( ! isset($this->current_procs[$this->current_pid]))
		{
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
				// Fork process for each channel
				$process_id = pcntl_fork();
				
				if ($process_id == -1)
				{
					// Error
					Kohana::$log->add(Log::ERROR, 'Could not fork worker process for the :channel channel', 
						array(':channel' => $key));
					
					exit(1);
				}
				elseif ($process_id)
				{
					// Add the child process to the job queue
					$this->current_procs[] = $process_id;
				
					// Check if the signal for the current child process has been caught
					if (isset($this->signal_queue[$process_id]))
					{
						// Handle the signal
						$this->handle_child_signal(SIGCHLD, $process_id, $this->signal_queue[$process_id]);
						
						// Remove process from the signal queue
						unset ($this->signal_queue[$process_id]);
					}
				}
				else
				{
					// Create instance for the channel worker. If not found, the
					// framework will thrown an exception
					$instance = Swiftriver_Worker_Channel::factory($key);
			
					// Log
					Kohana::$log->add(Log::DEBUG, 'Forked process :pid for :channel channel', 
						array(':pid' => getmypid(), ':channel' => strtoupper($key)));
					
					// Register the crawler's callback function
					$this->worker->addFunction($key, array($instance, 'channel_worker'));
					$this->action_index();
				}
			}
		
			// Add and register the queue processor
			$this->worker->addFunction('process_queue', array('Swiftriver_Dropletqueue', 'process'));
			
			// Wait for the child processes to finish
			while (count($this->current_procs))
			{
				// Prevents PHP from munching the CPU
				sleep(5);
			}
		}
	}
	
	/**
	 * Run the channel worker
	 */
	public function action_index()
	{
		// Wait for and perform jobs
		while ($this->worker->work())
		{
			
			// Listen for failure
			if ($this->worker->returnCode() != GEARMAN_SUCCESS)
			{
				// Log the error!
				Kohana::$log->add(Log::ERROR, 'Gearman worker error :code --- :error', 
					array(':code' => $this->worker->returnCode(), ':error' => $this->worker->error()));
			}
		}
	}
	
	/**
	 * Signal handler for the child process
	 *
	 * @param int $signo Signal number
	 * @param int $pid Process ID of the child process
	 * @param int $status Status of the process
	 */
	public function handle_child_signal($signo, $pid = NULL, $status = NULL)
	{
		// If no pid is provided, we're getting the signal from the system. Let's find out which
		// process ended
		if ( ! $pid)
		{
			$pid = pcntl_waitpid(-1, $status, WNOHANG);
		}
		
		// Get all the children that have exited
		while ($pid > 0)
		{
			if ($pid AND isset($this->current_procs[$pid]))
			{
				// Get the exit status of the terminated child process
				$exit_code = pcntl_wexitstatus($status);
				
				// Check for clean exit
				if ($exit_code != 0)
				{
					// Log the error
					Kohana::$log->add(Log::ERROR, 'Process :pid exited with status :code', 
						array(':pid' => $pid, ':code' => $exit_code));
				}
				
				// Remove the process from the list of current processes
				unset ($this->current_procs[$pid]);
			}
			elseif ($pid)
			{
				// The child process has finished before parent process. Add it to the signal queue
				// so that the parent can deal with it
				$this->signal_queue[$pid] = $status;
			}
			
			// Wait for the child to exit and return immediately if no child has exited
			$pid = pcntl_waitpid(-1, $status, WNOHANG);
		}
		
		return TRUE;
	}
	
}
?>