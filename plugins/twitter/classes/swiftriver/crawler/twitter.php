<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Twitter crawler
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Crawler_Twitter  {
	
	/**
	 * 'crawls' Twitter by piggybacking on the Twitter Search API
	 *
	 * @param int $river_id ID of the river the tweets shall be added to
	 */
	public function crawl($river_id)
	{
		// If the river ID is NULL or non-existent, exit
		if (empty($river_id) OR ! ORM::factory('river', $river_id)->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Invalid database river id: :river_id', 
				array(':river_id' => $river_id));
			
			return FALSE;
		}
				
		// Get the keywords and users to search form the db
		$filter_options = Model_Channel_Filter::get_channel_filter_options('twitter', $river_id);
		
		if ( ! empty($filter_options))
		{
			$search = new TwitterSearch();
			$search->user_agent = 'phptwittersearch:team@ushahidi.com';
												
			// Build the query
			$keywords = '';
			$people = '';
			foreach ($filter_options as $option)
			{				
				$value = $option['data']['value'];

				// If the values are comma separated, substitute the "," with " OR "
				// after applying a trim function to each element in the array
				if ($values_array = array_map("trim", explode(",", $value)))
				{
					$value = implode(" OR ", $values_array);
				}

				switch($option['key'])
				{
					case 'keyword':						
						$keywords .= $value.' OR '; 
					break;
					
					case 'person':
						$people .= 'from:'.$value.' OR '; 
					break;				
				}							
			}
			
			$twitter_crawls = ORM::factory('twitter_crawl')
			    ->where('river_id', '=', $river_id)
			    ->find();
			
			$results = NULL;
			$request_hash = hash('sha256', $keywords.$people);
			if ($twitter_crawls->loaded() AND $twitter_crawls->request_hash == $request_hash)
			{
				// Request hasn't changed and we have a previous crawl
				// Use the refresh_url the api provided then
				$results = $search->results($twitter_crawls->refresh_url);
			}
			else
			{
				// Request changed or this is a new channel.
				// Do a new crawl in this case
				if ($people)
				{
					// Remove trailing OR
					$people = substr($people, 0, strlen($people) - 4);				
					$search->contains($people);
				}
				
				if ($keywords)
				{
					// Remove trailing OR
					$keywords = substr($keywords, 0, strlen($keywords) - 4);
					$search->contains($keywords);
				}

				$results = $search->results();
			}
			
			// Add droplets
			foreach ($results['tweets'] as $tweet)
			{
				// Get the droplet template
				$droplet = Swiftriver_Dropletqueue::get_droplet_template();

				// Populate the droplet
				$droplet['channel'] = 'twitter';
				$droplet['river_id'] = array($river_id);
				$droplet['identity_orig_id'] = $tweet->from_user_id;
				$droplet['identity_username'] = $tweet->from_user;
				$droplet['identity_name'] = $tweet->from_user_name;
				$droplet['identity_avatar'] = $tweet->profile_image_url;
				$droplet['droplet_orig_id'] = $tweet->id;
				$droplet['droplet_type'] = 'original';
				$droplet['droplet_title'] = $tweet->text;
				$droplet['droplet_raw'] = $droplet['droplet_content'] = $tweet->text;
				$droplet['droplet_locale'] = $tweet->iso_language_code;
				$droplet['droplet_date_pub'] = gmdate("Y-m-d H:i:s", strtotime($tweet->created_at));

				Swiftriver_Dropletqueue::add($droplet);
			}
			
			// Save refresh_url if provided
			if ($results['refresh_url'])
			{
				$twitter_crawls->refresh_url = $results['refresh_url'];
				$twitter_crawls->request_hash = $request_hash;
				$twitter_crawls->river_id = $river_id;
				$twitter_crawls->save();
			}
		}
										
		return FALSE;
	}
}
?>