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
	}

	public function action_index()
	{
		// Start streaming +++ TESTING +++
		$sc = new Firehose_Filter('username', 'password', Phirehose::METHOD_FILTER);
		$sc->setTrack(array('ushahidi'));
		$sc->consume();

		//echo "test";
	}

	/**
	 * Build Twitter Search Query
	 * @param array $options
	 * @return void
	 */
	private function _search($options = array())
	{
		include_once Kohana::find_file('vendor', 'twittersearch/twittersearch');
		
		$search = new TwitterSearch();
		foreach ($options as $key => $value)
		{
			// Keywords
			if ($key == 'keywords')
			{
				$search->contains($value);
			}

			// Hashtag
			if ($key == 'hashtag')
			{
				$search->with($value);
			}

			// From @user
			if ($key == 'from')
			{
				$search->from($value);
			}

			// To @user
			if ($key == 'to')
			{
				$search->to($value);
			}

			// Mention @user
			if ($key == 'mention')
			{
				$search->about($value);
			}
		}

		$results = $search->rpp(50)->results();

		foreach ($results as $result)
		{
			if ( $result->text AND ! $this->_is_retweet($result->text) )
			{
				if ( isset($result->from_user_id) AND ! empty($result->from_user_id) AND 
				 	isset($result->id) AND ! empty($result->id) )
				{
					$this->_save($result);
				}
			};
			
		}
	}


	/**
	 * Save A Tweet!
	 * @param object $result
	 * @return void
	 */
	private function _save($result = NULL)
	{
		if ($result)
		{
			// Get droplet template
			$droplet = Swiftriver_Dropletqueue::get_droplet_template();
			
			// Set the droplet properties
			$droplet['channel'] = 'twitter';
			$droplet['channel_filter_id'] = '1';
			$droplet['identity_orig_id'] = $result->from_user_id_str;
			$droplet['identity_username'] = $result->from_user;
			$droplet['identity_name'] = '';
			$droplet['droplet_orig_id'] = trim((string) $result->id);
			$droplet['droplet_type'] = 'original';
			$droplet['droplet_title'] = '';
			$droplet['droplet_content'] = trim(strip_tags(str_replace('<', ' <', $result->text)));
			$droplet['droplet_locale'] = $result->iso_language_code;
			$droplet['droplet_date_pub'] = date("Y-m-d H:i:s", strtotime($result->created_at));
			
			// 	Add the droplet to the processing queue
			Swiftriver_Dropletqueue::add($droplet, FALSE);
		}
	}


	/**
	 * Is this an old style retweet i.e. (RT @)
	 * @param string $str
	 * @return bool
	 */
	private function _is_retweet($str = NULL)
	{
		if ($str)
		{
			// Case insensitive search on "RT @user"
			$regex1 = 'RT\s+@[a-zA-Z0-9_]*';

			// Case insensitive search on "RT@user"
			$regex2 = 'RT@[a-zA-Z0-9_]*';

			if ( preg_match_all("/".$regex1."/is", $str, $matches) OR 
				preg_match_all("/".$regex2."/is", $str, $matches) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}