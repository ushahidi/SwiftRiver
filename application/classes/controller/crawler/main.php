<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Controller_Crawler_Main extends Controller {
	
	protected static $process_mutex = 'SwiftRiver_PostProcessor';
	protected static $crawl_mutex = 'SwiftRiver_Crawler';

	// Processing of drops from the db	   
	public function action_process()
	{
		$this->do_fork(function() {
			Swiftriver_Dropletqueue::process();
			exit;
		});
	}

	// Run crawling scheduler
	public function action_index()
	{
		$this->do_fork(function() {
			Swiftriver_Crawlers::do_crawl();
			exit;
		});
	}

	/**
	 * Forks of the callback into a separate process.
	 * The parent process exits immediately completing the HTTP request.
	 * and maintain a mutex preventing other instances of this class
	 * from running before the callback completes.
	 * Double fork is done to allow the callback to obtain another
	 * mutex if need be.
	 */	   
	private function do_fork($callback)
	{
		// The signals used below require cli mode
		if (php_sapi_name() != 'cli')
		{
		    Kohana::$log->add(Log::ERROR, "CLI mode is required");
			return;
		}
		
		// Fork process to do the crawl if pcntl is installed
		if ( ! function_exists('pcntl_fork'))
		{
			Kohana::$log->add(Log::ERROR, "PCNTL is required");
			return;
		}

		$pid = pcntl_fork();		
		if ($pid == -1)
		{
			 Kohana::$log->add(Log::ERROR, "Forking failed.");
		}
		elseif ($pid == 0)
		{
			// Fork again
			// This second parent will hold the crawl mutex
			// so that child processes can other locks

			// Install signal handlers
			declare(ticks = 1); // How often to check for signals
			// Run callable where OK received from parent
			pcntl_signal(SIGUSR1, $callback);
			// Exit when NACK received from parent.
			pcntl_signal(SIGUSR2, function($signo) { exit; } );
									
			$pid = pcntl_fork();
						
			// Force reconnection. Both parent and child
			// processes will open their own conneciton
			// once they start.
			Database::instance()->disconnect();
			
			if ($pid == -1)
			{
				 Kohana::$log->add(Log::ERROR, "Second fork failed.");
			}
			elseif ($pid == 0)
			{
				// Second child
				
				// Wait for signal from parent to proceed
				while (TRUE)
					sleep(60);
			}
			else
			{
				// Second parent
				try
				{
					Swiftriver_Mutex::obtain(get_class());
					// Signal child to proceed
					Kohana::$log->write();
					posix_kill($pid, SIGUSR1);
				}
				catch (SwiftRiver_Exception_Mutex $e)
				{
					// Signal child to exit
					Kohana::$log->add(Log::ERROR, "Unable to obtain mutex");
					posix_kill($pid, SIGUSR2);
					exit;
				}
				pcntl_wait($status);
				Swiftriver_Mutex::release(get_class());
			}
		}
	}

}
?>
