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
		
		$droplet_id = intval($this->request->param('droplet_id', 0));
		$river_id = intval($this->request->param('river_id', 0));
		$bucket_id = intval($this->request->param('bucket_id', 0));
		$tag_id = intval($this->request->param('tag_id', 0));
		
		// Determine the account depending on the route used
		$visited_account = NULL;
		if ($river_id)
		{
			$river_orm = ORM::factory('river', $river_id);
			if ( ! $river_orm->loaded())
			{
				throw new HTTP_Exception_404(
			        'The requested page :page was not found on this server.',
			        array(':page' => $page)
			        );
			}
			else
			{
				$visited_account = $river_orm->account;
			}
		}
		else if ($bucket_id)
		{
			$bucket_orm = ORM::factory('bucket', $bucket_id);
			if ( ! $bucket_orm->loaded())
			{
				throw new HTTP_Exception_404(
			        'The requested page :page was not found on this server.',
			        array(':page' => $page)
			        );
			}
			else
			{
				$visited_account = $bucket_orm->account;
			}
		}
		
		if ( ! $visited_account ) {
			throw new HTTP_Exception_404(
		        'The requested page :page was not found on this server.',
		        array(':page' => $page)
		        );
		}
		
				
		switch ($this->request->method())
		{
			case "POST":
				$tag_array = json_decode($this->request->body(), true);
				$tag_name = $tag_array['tag'];
				$account_id = $visited_account->id;
				$tag_orm = Model_Account_Droplet_Tag::get_tag($tag_name, $droplet_id, $account_id);
				echo json_encode(array('id' => $tag_orm->tag->id, 'tag' => $tag_orm->tag->tag));
			break;
			case "DELETE":				
				$response = array("success" => FALSE);
				
				if (Model_Droplet::delete_tag($droplet_id, $tag_id, $visited_account->id)) {
					$response['success'] = TRUE;
				}
				
				echo json_encode($response);
			break;
		}
	}
}