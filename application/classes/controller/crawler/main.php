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
    
    private $crawl_mutex = 'SwiftRiver_Crawler';
    private $process_mutex = 'SwiftRiver_PostProcessor';

	/**
	 * Forks of the post processing drops from the DB into a separate process.
	 * The parent process exits immediately completing the HTTP request.
	 * The child process will continue in the process preventing other
	 * processes from doing processing on the same DB via a mysql lock
	 *
	 */        
    public function action_process()
    {
        $pid = pcntl_fork();
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
	        return;
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
        $pid = pcntl_fork();
        if ($pid == -1) 
        {
             Kohana::$log->add(Log::ERROR, "Crawler controller forking failed.");
        } 
        else if ($pid == 0) 
        {
            Database::instance()->disconnect(); // Force child reconnection
            $this->__do_crawl();
        }
        
        // If rss parameter is provided, return an RSS feed
        $rss = $this->request->query('rss') ? true : false;
        if ($rss)
        {
            $feed = View::factory('pages/crawler/rss')
                         ->bind('site_url', $site_url)            
                         ->bind('request_date', $request_date);
                         
            $request_date = date('jS F Y h:i:s A e', time());
            $site_url = url::site(null, true, true);
            echo $feed;
        }
        
    }
		
	
	/**
	 * Run the crawlers
	 */
	private function __do_crawl()
	{	
	    
	    // Only one instance of the crawler is allowed
	    // to run at any time system wide
	    if ( ! Swiftriver_Mutex::obtain($this->crawl_mutex)) 
	    {
	        Kohana::$log->add(Log::ERROR, "Crawler unable to obtain lock");
	        return;
        }
        
        Kohana::$log->add(Log::INFO, "Crawler started");
	    
	    $river_id = intval($this->request->param('id', 0));
	    $channel = $this->request->param('channel');
	    	    
	    	    
	    // If a river_id or plugin is provided then only do a run for that
	    // selection
		if ($river_id or $channel) 
		{
		    Swiftriver_Crawlers::run($river_id, $channel);		    
		}
		else
		{
		    // We create and run a schedule
		    $this->__do_schedule();
		}
				
		Swiftriver_Mutex::release($this->crawl_mutex);
		
		// If we got some drops, process them
		if ( ! Swiftriver_Dropletqueue::isempty()) {
		    Kohana::$log->add(Log::INFO, "Crawler post processing");	
		    Swiftriver_Dropletqueue::process();   
		}		
		
		Kohana::$log->add(Log::INFO, "Crawler completed");	
	}
	
	/**
	 * Run the crawlers in order of their run dates
	 *
	 */	
	private function __do_schedule()
	{	    
	    $jobs = Model_Channel_Filter::get_channel_filters_by_run_date();	    
	    foreach ($jobs as $job)
	    {
		    Swiftriver_Crawlers::run($job['river_id'], $job['channel']);
	    }
	}
	
	
}
?>
