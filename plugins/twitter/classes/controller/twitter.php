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

	private $session;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Get the session instance
		$this->session = Session::instance();
	}
	
	/**
	 * Twitter Oauth Callback
	 * 
	 * @return	void
	 */
	public function action_auth()
	{
		include_once Kohana::find_file('vendor', 'twitteroauth/twitteroauth');

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
				try
				{
					$connection = new TwitterOAuth(
							$settings['consumer_key'],
							$settings['consumer_secret'],
							$this->session->get('oauth_token'),
							$this->session->get('oauth_token_secret')
						);

					$token = $connection->getAccessToken();
					
					// Get the details of user making this request
					$user = $connection->get('account/verify_credentials');

					if ( isset($token['oauth_token']) AND isset($token['oauth_token_secret']) )
					{
						// Save Twitter Oauth Token
						$setting = ORM::factory('twitter_setting')
							->where('key', '=', 'oauth_token')
							->find();
						$setting->key = 'oauth_token';
						$setting->value = $token['oauth_token'];
						$setting->save();

						// Save Twitter Oauth Token Secret
						$setting = ORM::factory('twitter_setting')
							->where('key', '=', 'oauth_token_secret')
							->find();
						$setting->key = 'oauth_token_secret';
						$setting->value = $token['oauth_token_secret'];
						$setting->save();

						// Save Twitter Screen Name
						$setting = ORM::factory('twitter_setting')
							->where('key', '=', 'screen_name')
							->find();
						$setting->key = 'screen_name';
						$setting->value = $user->screen_name;
						$setting->save();
					}			
				}
				catch (Exception $e)
				{
					Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
				}
			}
		}

		Request::current()->redirect('settings/twitter');
	}
	
}