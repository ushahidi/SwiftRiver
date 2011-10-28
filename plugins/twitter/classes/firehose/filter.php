<?php defined('SYSPATH') or die('No direct script access');

include_once Kohana::find_file('vendor', 'phirehose/Phirehose');

class Firehose_Filter extends Phirehose {
	
	/**
	 * Enqueue each status
	 *
	 * @param string $status
	 */
	public function enqueueStatus($status)
	{
		$data = json_decode($status, TRUE);
		if (is_array($data) AND isset($data['user']['screen_name']))
		{
			// Get the droplet template
			$droplet = Swifriver_Dropletqueue::get_droplet_template();
			
			// Populate the droplet
			$droplet['channel'] = 'twitter';
			$droplet['channel_filter_id'] = '1';
			$droplet['identity_orig_id'] = $data['user']['id'];
			$droplet['identity_username'] = $data['user']['screen_name'];
			$droplet['identity_name'] => $data['user']['name'];
			$droplet['droplet_orig_id'] => $data['id'];
			$droplet['droplet_type'] => 'original';
			$droplet['droplet_title'] => '';
			$droplet['droplet_content'] => $data['text'];
			$droplet['droplet_locale'] => $data['user']['lang'];
			$droplet['droplet_date_pub'] => date("Y-m-d H:i:s", strtotime($data['created_at']));

			Swiftriver_Dropletqueue::add($droplet, FALSE);
		}
	}

}