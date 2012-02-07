<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Droplet Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Tag extends Controller_Swiftriver {
	
	 /**
	  * Single droplet restful api
	  */ 
	 public function action_api()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
			case "POST":
				$droplet_id = intval($this->request->param('droplet_id', 0));
				$tag_array = json_decode($this->request->body(), true);
				$tag_name = $tag_array['tag'];
				$account_id = $this->account->id;
				$tag_orm = Model_Account_Droplet_Tag::get_tag($tag_name, $droplet_id, $account_id);
				echo json_encode(array('id' => $tag_orm->tag->id, 'tag' => $tag_orm->tag->tag));
			break;
			case "DELETE":
				$droplet_id = intval($this->request->param('droplet_id', 0));
				$tag_id = intval($this->request->param('tag_id', 0));
				$response = array("success" => FALSE);
				
				if (Model_Droplet::delete_tag($droplet_id, $tag_id, $this->account->id)) {
					$response['success'] = TRUE;
				}
				
				echo json_encode($response);
			break;
		}
	}
}