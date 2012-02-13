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
	
	protected static $process_mutex = 'SwiftRiver_PostProcessor';

	/**
	 * Forks of the post processing drops from the DB into a separate process.
	 * The parent process exits immediately completing the HTTP request.
	 * The child process will continue in the process preventing other
	 * processes from doing processing on the same DB via a mysql lock
	 *
	 */		   
	public function action_process()
	{
		$pid = 0;
		
		// Fork process to do the crawl if pcntl is installed
		if (function_exists('pcntl_fork'))
		{
			Kohana::$log->add(Log::INFO, "Forking process!");
			$pid = pcntl_fork();
		}

		if ($pid == -1) 
		{
			 Kohana::$log->add(Log::ERROR, "Processor controller forking failed.");
		} 
		else if ($pid == 0) 
		{
			Database::instance()->disconnect(); // Force child reconnection
			$this->__do_process();
		}		 
	}
	
	private function __do_process()
	{
		// Only one instance of the crawler is allowed
		// to run at any time system wide
		if ( ! Swiftriver_Mutex::obtain($this->process_mutex)) 
		{
			Kohana::$log->add(Log::ERROR, "Processor unable to obtain lock");
			exit;
		}
		
		Kohana::$log->add(Log::INFO, "Post processing started");
		
		// Since we are not adding new drops in this
		// process, the below call will query the DB
		Swiftriver_Dropletqueue::process();
		
		Swiftriver_Mutex::release($this->process_mutex);
		
		Kohana::$log->add(Log::INFO, "Post processing completed");
	}
	
	/**
	 * Forks of the crawling into a separate process.
	 * The parent process exits immediately completing the HTTP request.
	 * The child process will continue in the process preventing other
	 * processes from doing crawls on the same DB via a mysql lock
	 *
	 */	   
	public function action_index()
	{
		$pid = 0;
		
		// Control whether to do this request asynchronously or not
		$sync = $this->request->query('sync') ? 1 : 0;
		
		// Fork process to do the crawl if pcntl is installed
		if (function_exists('pcntl_fork') & ! $sync)
		{			
			Kohana::$log->add(Log::INFO, "Forking crawl!");
			$pid = pcntl_fork();
		}
		
		if ($pid == -1)
		{
			 Kohana::$log->add(Log::ERROR, "Crawler controller forking failed.");
		}
		elseif ($pid == 0)
		{
			// Child process
			
			if ( ! $sync )
			{
				// Force child reconnection
				Database::instance()->disconnect();
			}
			$river_id = intval($this->request->param('id', 0));
			$channel = $this->request->param('channel');
			Swiftriver_Crawlers::do_crawl($river_id, $channel);
			exit;
		}
		
		// If rss parameter is provided, return an RSS feed
		$rss = $this->request->query('rss') ? TRUE : FALSE;
		if ($rss)
		{
			$feed = View::factory('pages/crawler/rss')
						 ->bind('site_url', $site_url)			  
						 ->bind('request_date', $request_date);

			$request_date = date('jS F Y h:i:s A e', time());
			$site_url = url::site(NULL, TRUE, TRUE);
			echo $feed;
		}
		
	}

}
?>
