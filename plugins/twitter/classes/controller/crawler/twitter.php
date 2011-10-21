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
	 * This Project
	 */
	protected $project;

	/**
	 * This Feed
	 */
	protected $feed;

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
			$this->feed = $feed;
			$this->project = $feed->project;

			// Which Twitter Option are we going to use?
			$service_option = $feed->service_option;

			$options = array();

			// Get the Search parameters
			$params = $feed->feed_options->find_all();
			foreach ($params as $param)
			{
				if ($param->value)
				{
					$options[$param->key] = $param->value;
				}
			}

			if (count($options))
			{
				$this->_search($options);
			}
		}
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
			// Create a new Source
			$source = ORM::factory('source')
				->where( 'source_orig_id', '=', trim((string) $result->from_user_id) )
				->find();
			if ( ! $source->loaded() )
			{
				$source->source_orig_id = trim((string) $result->from_user_id);
				$source->service = 'twitter';
				$source->source_username = $result->from_user;
				$source->save();
			}

			// Create a new Item
			$item = ORM::factory('item')
				->where( 'item_orig_id', '=', trim((string) $result->id) )
				->find();
			if ( ! $item->loaded() )
			{
				$item->service = 'twitter';
				$item->source_id = $source->id; // the source we just saved above
				$item->item_orig_id = trim((string) $result->id);
				$item->project_id = $this->project->id;
				$item->feed_id = $this->feed->id;
				$item->item_content = trim(strip_tags(str_replace('<', ' <', $result->text)));
				$item->item_raw = $result->text;
				$item->item_author = $result->from_user;
				$item->item_locale = $result->iso_language_code;
				$item->item_date_pub = date("Y-m-d H:i:s", strtotime($result->created_at));
				$item->item_date_add = date("Y-m-d H:i:s", time());
				$item->save();
			}
		}
	}


	/**
	 * Is this an old style retweet i.e. (RT @)
	 * @param string $str
	 * @return bool
	 */
	private function _is_retweet($str = NULL)
	{
		if ( $str )
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