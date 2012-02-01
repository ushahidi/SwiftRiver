<?php defined('SYSPATH') or die('No direct script access'); 

/**
 * Swiftriver_Task_Runner test case
 *
 * @author     Ushahidi Team
 * @package    Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Tests
 * @copyright  (c) 2008-2011 Ushahidi Inc
 */
class Swiftriver_Task_Runner_Test extends Unittest_TestCase {
	
	/**
	 * @covers Swiftriver_Task_Runner::register_task
	 */
	public function test_register_task()
	{
		// URLs to crawl
		// $urls = array(
		// 	'http://feeds.reuters.com/reuters/artNews',
		// 	'http://feeds.reuters.com/reuters/businessNews',
		// 	'http://feeds.reuters.com/ReutersBusinessTravel',
		// 	'http://feeds.reuters.com/reuters/lifestyle',
		// 	'http://www.cbsnews.com/feeds/rss/world.rss',
		// 	'http://www.cbsnews.com/feeds/rss/politics.rss',
		// 	'http://www.cbsnews.com/feeds/rss/business.rss',
		// 	'http://www.cbsnews.com/feeds/rss/opinion.rss',
		// 	'http://www.cbsnews.com/feeds/rss/501370.rss',
		// 	'http://www.cbsnews.com/feeds/rss/health.rss',
		// 	'http://feeds.abcnews.com/abcnews/internationalheadlines',
		// 	'http://feeds.abcnews.com/abcnews/moneyheadlines',
		// 	'http://feeds.abcnews.com/abcnews/technologyheadlines',
		// 	'http://www.ft.com/rss/companies/africa',
		// 	'http://www.ft.com/rss/companies/financials',
		// 	'http://www.ft.com/rss/companies/health',
		// 	'http://www.walesonline.co.uk/business-in-wales/rss.xml',
		// 	'http://www.walesonline.co.uk/business-in-wales/business-news/rss.xml',
		// 	'http://feeds2.feedburner.com/time/world',
		// 	'http://feeds.feedburner.com/time/ideas',
		// 	'http://feeds.feedburner.com/time/newsfeed',
		// 	'http://feeds2.feedburner.com/time/scienceandhealth',
		// 	'http://feeds2.feedburner.com/time/business',
		// 	'http://feeds.feedburner.com/time/moneyland',
		// 	'http://feeds.feedburner.com/time/healthland',
		// 	'http://www.economist.com/rss/international_rss.xml',
		// 	'http://www.economist.com/rss/letters_rss.xml',
		// 	'http://www.economist.com/topics/economics/index.xml',
		// 	'http://www.economist.com/topics/environmental-problems-and-protection/index.xml',
		// 	'http://feeds.feedburner.com/economist/pJMW',
		// 	'http://www.economist.com/topics/corporate-governance/index.xml',
		// );
		
		// Swiftriver_Task_Runner::register_task('twitter', '1');
		// Swiftriver_Task_Runner::register_task('email', '1');
		
		$result = Swiftriver_Task_Runner::register_task('rss', '1');
		$this->assertTrue($result, 'Task could not be registered');
		
		$run_result = Swiftriver_Task_Runner::run_tasks();
		$this->assertTrue($run_result, 'The tasks could not be run');
		
		unset ($result, $run_result);
	}
}
?>