<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Channel worker
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Channel_Worker_Twitter extends Swiftriver_Channel_Worker {

	/**
	 * Initializes the Twitter channel worker by setting all the parameters
	 * used by the callback method (channel_worker)
	 */
	public function __construct()
	{
		$settings = array();
		
		// Fetch the Twitter settings
		$params = ORM::factory('twitter_setting')->find_all();
		foreach ($params as $param)
		{
			$settings[$param->key] = $param->value;
		}

		// Check that the Twitter OAuth params have been set
		if (isset($settings['consumer_key'])
			AND isset($settings['consumer_secret']) 
			AND isset($settings['oauth_token']) 
			AND isset($settings['oauth_token_secret']))
		{
			// The app OAuth credentials
			define("TWITTER_CONSUMER_KEY", $settings['consumer_key']);
			define("TWITTER_CONSUMER_SECRET", $settings['consumer_secret']);
			
			// The OAuth data for the twitter account
			define("OAUTH_TOKEN", $settings['oauth_token']);
			define("OAUTH_SECRET", $settings['oauth_token_secret']);
		}
	}

	/**
	 * @see Switriver_Worker_Channel::channel_worker
	 */
	public function channel_worker($job)
	{
		if (defined('OAUTH_TOKEN') 
			AND defined('OAUTH_SECRET') 
			AND defined('TWITTER_CONSUMER_KEY') 
			AND defined('TWITTER_CONSUMER_SECRET'))
		{ 
			// Consume the firehose!
			$sc = new Firehose_Filter(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
			$sc->consume();
		}
	}
	
}

?>
