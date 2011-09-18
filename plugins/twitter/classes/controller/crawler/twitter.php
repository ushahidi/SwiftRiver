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

    /**
     * Use the Twitter Search API to retrieve hashtag tweets
     * @param array $options
     * @return void
     */
	private function hashtag($options = array())
	{
		include_once Kohana::find_file('vendor', 'twittersearch/twittersearch');
		//print_r($options);
	}

    /**
     * Use the Twitter Search API to retrieve location based
     * tweets
     * @param array $options
     * @return void
     */
	private function location($options = array())
	{
		
	}

    /**
     * Use the Twitter Search API to retrieve tweets from 
     * specified keywords
     * @param array $options
     * @return void
     */
	private function keywords($options = array())
	{
		include_once Kohana::find_file('vendor', 'twittersearch/twittersearch');
		if ( isset($options['keywords']) )
		{
			$keywords = array_map('trim', explode(',', $options['keywords']));
			foreach ($keywords as $keyword)
			{
				if ($keyword)
				{
					$search = new TwitterSearch($keyword);
					$results = $search->results();
					print_r($results);
					echo '<Br><Br><Br>{{{{-------------}}}<Br><Br><Br>';
				}
			}
		}
	}

	private function user($options = array())
	{
		
	}

	/**
     * Is this an old style retweet i.e. (RT @)
     * @param string $str
     * @return bool
     */
    private function _is_retweet($str = NULL)
	{
		// Case insensitive search on "RT @user"
		$regex1 = '(RT)(\\s+)(@)((?:[a-z][a-z]*[0-9]+[a-z0-9]*))';

		if ($c=preg_match_all ("/".$regex1."/is", $str, $matches))
		{
			$word1=$matches[1][0];
			$ws1=$matches[2][0];
			$c1=$matches[3][0];
			$word2=$matches[4][0];
			print "($word1) ($ws1) ($c1) ($word2) \n";
		}

		// Case insensitive search on "RT@user"
		$regex2 = '(RT)(@)((?:[a-z][a-z]+))';
	}
}