<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Controller - Handles Individual Buckets
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
class Controller_Bucket extends Controller_Swiftriver {
	/**
	 * This Bucket
	 */
	protected $bucket;

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
		
	}
	
	/**
	 * Ajax Create New Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_new()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		// save the bucket
		if ($_POST)
		{
			$bucket = ORM::factory('bucket');
			$post = $bucket->validate($_POST);

			if ($post->check())
			{
				$bucket->bucket_name = $post['bucket_name'];
				$bucket->user_id = $this->user->id;
				$bucket->account_id = $this->account->id;
				$bucket->save();

				echo json_encode(array("status"=>"success",
					"bucket" => "<a href=\"". URL::site().'bucket/index/'.$bucket->id."\">".$bucket->bucket_name."</a>"));
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('bucket');
				echo json_encode(array("status"=>"error", "errors" => $errors));
			}
		}
	}

	/**
	 * Ajax Delete Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_delete()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		if ( $_REQUEST AND isset($_REQUEST['id']) AND
			! empty($_REQUEST['id']) )
		{
			$bucket = ORM::factory('bucket')
				->where('id', '=', $_REQUEST['id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($bucket->loaded())
			{
				$bucket->delete();
				echo json_encode(array("status"=>"success"));
			}
			else
			{
				echo json_encode(array("status"=>"error"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error"));
		}
	}	
}