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
		
		$settings = array();
		$params = ORM::factory('twitter_setting')->find_all();
		foreach($params as $param) {
			$settings[$param->key] = $param->value;
		}

		
		if (isset($settings['consumer_key']) 
			and isset($settings['consumer_secret']) 
			and isset($settings['oauth_token']) 
			and isset($settings['oauth_token_secret'])) {

		    // The app OAuth credentials
                    define("TWITTER_CONSUMER_KEY", $settings['consumer_key']);
                    define("TWITTER_CONSUMER_SECRET", $settings['consumer_secret']);


                    // The OAuth data for the twitter account
                    define("OAUTH_TOKEN", $settings['oauth_token']);
                    define("OAUTH_SECRET", $settings['oauth_token_secret']);
		}
	}

	public function action_index()
	{
		if(defined('OAUTH_TOKEN') 
			and defined('OAUTH_SECRET') 
			and defined('TWITTER_CONSUMER_KEY') 
			and defined('TWITTER_CONSUMER_SECRET')){ 
		$sc = new Firehose_Filter(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
		$sc->consume();
	    }
	}
}
