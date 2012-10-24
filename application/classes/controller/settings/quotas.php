<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Channel Quotas settings controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Dev Team
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */

class Controller_Settings_Quotas extends Controller_Settings_Main {
	
	/**
	 * Landing page for this controller
	 */
	public function action_index()
	{
		$this->template->header->title = __("Channel Quotas");
		$this->settings_content = View::factory('pages/settings/quotas')
		    ->bind('post_url', $post_url)
		    ->bind('quotas', $quotas);

		$this->active = 'quotas';
		$post_url = URL::site('settings/quotas/manage');
		$quotas = json_encode(Model_Channel_Quota::get_quotas_array());
	}

	/**
	 * REST endpoint for (de)activation of channel quotas
	 */
	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case "POST":
				$payload = json_decode($this->request->body(), TRUE);
				$quota_orm = Model_Channel_Quota::add_quota($payload);

				echo json_encode($quota_orm->as_array());
			break;

			case "PUT":
				$payload = json_decode($this->request->body(), TRUE);
				$quota_id = $payload['id'];
				$quota_orm = ORM::factory('channel_quota', $quota_id);
				if ($quota_orm->loaded())
				{
					$quota_orm->quota = $payload['quota'];
					$quota_orm->save();
				}
			break;
		}
	}
}