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
		$this->template->content = View::factory('pages/bucket/main')
			->bind('bucket', $bucket)
			->bind('settings', $settings)
			->bind('more', $more);

		// First we need to make sure this bucket exists
		$id = (int) $this->request->param('id', 0);
		$bucket = ORM::factory('bucket')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();
		if ( ! $bucket->loaded())
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect('dashboard');
		}

		// Get this buckets droplets
		$droplets = $bucket->droplets->find_all()->as_array();

		// Links to ajax rendered menus
		$settings = url::site().$this->account->account_path.'/bucket/settings/'.$id;
		$more = url::site().$this->account->account_path.'/bucket/more/';
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
	 * Ajax rendered settings control box
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// First we need to make sure this bucket exists
		$id = (int) $this->request->param('id', 0);
		$bucket = ORM::factory('bucket')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();

		// Return this control only if bucket is loaded
		if ($bucket->loaded())
		{
			$settings = View::factory('pages/bucket/settings_control');
			$settings->bucket = $bucket;
			echo $settings;	
		}
	}	

	/**
	 * Ajax rendered more control box
	 * 
	 * @return	void
	 */
	public function action_more()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/bucket/more_control');
	}

	/**
	 * Ajax Title Editing Inline
	 *
	 * Edit Bucket Name
	 * 
	 * @return	void
	 */
	public function action_ajax_title()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_REQUEST AND
			isset($_REQUEST['edit_id'], $_REQUEST['edit_value']) AND
			! empty($_REQUEST['edit_id']) AND 
			! empty($_REQUEST['edit_value']) )
		{

			$bucket = ORM::factory('bucket')
				->where('id', '=', $_REQUEST['edit_id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($bucket->loaded())
			{
				$bucket->bucket_name = $_REQUEST['edit_value'];
				$bucket->save();
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