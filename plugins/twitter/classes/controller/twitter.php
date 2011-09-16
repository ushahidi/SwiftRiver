<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter Controller
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
class Controller_Twitter extends Controller {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * Twitter Oauth Callback
	 * 
	 * @return	void
	 */
	public function action_auth()
	{
		include_once Kohana::find_file('vendor', 'epioauth/EpiCurl');
		include_once Kohana::find_file('vendor', 'epioauth/EpiOAuth');
		include_once Kohana::find_file('vendor', 'epioauth/EpiTwitter');

		if ( isset($_GET['oauth_token']) 
			AND ! empty($_GET['oauth_token']) )
		{
			// First lets make sure this token is valid
			// Get Twitter Settings
			$settings = array();
			$params = ORM::factory('twitter_setting')->find_all();
			foreach ($params as $param)
			{
				$settings[$param->key] = $param->value;
			}

			if (isset($settings['consumer_key']) AND isset($settings['consumer_secret']))
			{
				$twitter = new EpiTwitter($settings['consumer_key'], 
					$settings['consumer_secret']);
				$twitter->setToken($_GET['oauth_token']);
				$token = $twitter->getAccessToken();

				if ( isset($token->oauth_token) AND isset($token->oauth_token_secret) )
				{
					// Save Twitter Oauth Token
					$setting = ORM::factory('twitter_setting')
						->where('key', '=', 'oauth_token')
						->find();
					$setting->key = 'oauth_token';
					$setting->value = $token->oauth_token;
					$setting->save();

					// Save Twitter Oauth Token Secret
					$setting = ORM::factory('twitter_setting')
						->where('key', '=', 'oauth_token_secret')
						->find();
					$setting->key = 'oauth_token_secret';
					$setting->value = $token->oauth_token_secret;
					$setting->save();
				}
			}
		}
	}
	
}