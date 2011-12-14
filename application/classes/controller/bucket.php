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
			->bind('droplets_list', $droplets_list)
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
		
		//Use page paramter or default to page 1
		$page = $this->request->query('page') ? $this->request->query('page') : 1;

		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplets', $droplets)
			->bind('view_more_url', $view_more_url)
			->bind('buckets', $buckets);

		//Get Droplets
		$droplets_array = Model_Droplet::get_bucket($bucket->id);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		// The Droplets
		$droplets = $droplets_array['droplets'];

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Links to ajax rendered menus
		$settings = url::site().$this->account->account_path.'/bucket/settings/'.$id;
		$more = url::site().$this->account->account_path.'/bucket/more/';
		$view_more_url = url::site().$this->account->account_path.'/bucket/index/'.$id.'?page='.($page+1);
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
					"bucket" => array(
						'id' => $bucket->id,
						'name' => $bucket->bucket_name
						)));
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
	 * Ajax Add Droplet to Bucket
	 * 
	 * @return string - json
	 */
	public function action_ajax_droplet()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// check, has the form been submitted
		if ($_REQUEST AND
			isset($_REQUEST['bucket_id'], $_REQUEST['droplet_id'], $_REQUEST['action']) AND
			! empty($_REQUEST['bucket_id']) AND 
			! empty($_REQUEST['droplet_id']) AND 
			in_array($_REQUEST['action'], array('add', 'remove')) )
		{
			// First make sure this account owns this bucket
			$bucket = ORM::factory('bucket')
				->where('id', '=', $_REQUEST['bucket_id'])
				->where('account_id', '=', $this->account->id)
				->find();

			// Get Droplet
			$droplet = ORM::factory('droplet', $_REQUEST['droplet_id']);

			if ($bucket->loaded() AND $droplet->loaded())
			{
				switch ($_REQUEST['action'])
				{
					// Add droplet to bucket
					case 'add':
						if ( ! $bucket->has('droplets', $droplet))
						{
							$bucket->add('droplets', $droplet);
						}
						break;
					// Remove droplet
					default:
						$bucket->remove('droplets', $droplet);
						break;
				}

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