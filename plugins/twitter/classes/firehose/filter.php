<?php defined('SYSPATH') or die('No direct script access');

include_once Kohana::find_file('vendor', 'phirehose/Phirehose');
include_once Kohana::find_file('vendor', 'phirehose/OauthPhirehose');

class Firehose_Filter extends  OauthPhirehose{

	private $_options = array();

	
	/**
	 * Enqueue each status
	 *
	 * @param string $status
	 */
	public function enqueueStatus($status)
	{
		$data = json_decode($status, TRUE);
		if (is_array($data) AND isset($data['user']['name']))
		{
			// Get the droplet template
			$droplet = Swiftriver_Dropletqueue::get_droplet_template();
			
			// Populate the droplet
			$droplet['channel'] = 'twitter';
			$droplet['identity_orig_id'] = $data['user']['id'];
			$droplet['identity_username'] = $data['user']['screen_name'];
			$droplet['identity_name'] = $data['user']['name'];
			$droplet['droplet_orig_id'] = $data['id'];
			$droplet['droplet_type'] = 'original';
			$droplet['droplet_title'] = $data['text'];
			$droplet['droplet_content'] = $data['text'];
			$droplet['droplet_locale'] = $data['user']['lang'];
			$droplet['droplet_date_pub'] = date("Y-m-d H:i:s", strtotime($data['created_at']));


			Swiftriver_Dropletqueue::add($droplet, FALSE);
		}
	}


	/**
	 * Update the track words
	 */
	public function checkFilterPredicates() {
            $this->setTrack(Util_Channel_Filter::get_keywords()); 
	}


}
