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
		$data = json_decode($status, true);
		if (is_array($data) AND isset($data['user']['screen_name']))
		{
			$droplet = array(
				'channel' => 'twitter',
				'channel_filter_id' => '1',
				'identity_orig_id' => $data['user']['id'],
				'identity_username' => $data['user']['screen_name'],
				'identity_name' => $data['user']['name'],
				'droplet_orig_id' => $data['id'],
				'droplet_type' => 'original',
				'droplet_title' => '',
				'droplet_content' => $data['text'],
				'droplet_raw' => $data['text'],
				'droplet_locale' => $data['user']['lang'],
				'droplet_date_pub' => date("Y-m-d H:i:s", strtotime($data['created_at'])),
			);

			Swiftriver_Dropletqueue::add($droplet);
		}
	}

}