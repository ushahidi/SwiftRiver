<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Crawler_Twitter extends Controller_Crawler_Main {

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Get Twitter Settings
		$this->settings = array();
		$params = ORM::factory('twitter_setting')->find_all();
		foreach ($params as $param)
		{
			$this->settings[$param->key] = $param->value;
		}
	}

	public function action_index()
	{
		$feeds = ORM::factory('feed')
			->where('service', '=', 'twitter')
			->where('feed_enabled', '=', '1')
			->find_all();
		
		foreach ($feeds as $feed)
		{
			// Which Twitter Option are we going to use?
			$service_option = $feed->service_option;

			$options = array();

			// Get the Search parameters
			$params = $feed->feed_options->find_all();
			foreach ($params as $param)
			{
				$options[$param->key] = $param->value;
			}

			$this->$service_option($options);
		}
	}


	private function hashtag($options = array())
	{
		echo "hashtag <BR>";
		print_r($options);
		echo "<BR /><br />";
	}

	private function location($options = array())
	{
		echo "location <BR>";
		print_r($options);
		echo "<BR /><br />";
	}

	private function keywords($options = array())
	{
		echo "keywords <BR>";
		print_r($options);
		echo "<BR /><br />";
	}

	private function user($options = array())
	{
		echo "user <BR>";
		print_r($options);
		echo "<BR /><br />";
	}
}