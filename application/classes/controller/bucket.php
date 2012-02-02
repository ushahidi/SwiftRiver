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
		
		$this->template->content = View::factory('pages/bucket/main')
			->bind('bucket', $bucket)
			->bind('droplets_list', $droplets_list)
			->bind('settings', $settings)
			->bind('more', $more);
			
		//Use page paramter or default to page 1
		$page = $this->request->query('page') ? $this->request->query('page') : 1;

		$droplet_js = View::factory('common/js/droplets')
				->bind('fetch_url', $fetch_url)
				->bind('polling_enabled', $polling_enabled);
		
		// Turn off ajax polling
		$polling_enabled = "false";
		
		// URL for fetching droplets
		$fetch_url = url::site().$this->account->account_path.'/bucket/droplets/'.$id;
				
		// Generate the List HTML
		$droplets_list = View::factory('pages/droplets/list')
			->bind('droplet_js', $droplet_js);
		
		//Get Droplets
		$droplets_array = Model_Bucket::get_droplets($bucket->id, $page);

		// Total Droplets Before Filtering
		$total = $droplets_array['total'];
		
		// The Droplets
		$droplets = $droplets_array['droplets'];
		
		//Throw a 404 if a non existent page is requested
		if ($page > 1 AND empty($droplets))
		{
		    throw new HTTP_Exception_404(
		        'The requested page :page was not found on this server.',
		        array(':page' => $page)
		        );
		}

		$buckets = ORM::factory('bucket')
			->where('account_id', '=', $this->account->id)
			->find_all();

		// Links to ajax rendered menus
		$settings = url::site().$this->account->account_path.'/bucket/settings/'.$id;
		$more = url::site().$this->account->account_path.'/bucket/more/';
		$view_more_url = url::site().$this->account->account_path.'/bucket/index/'.$id.'?page='.($page+1);
	}
	
	/**
	 * Gets the droplets for the specified bucket and page no. contained
	 * in the URL variable "page"
	 * The result is packed into JSON and returned to the requesting client
	 */
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$bucket_id = intval($this->request->param('id'));
		
		// Get the page number
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$droplets = Model_Bucket::get_droplets($bucket_id, $page);
		
		// Return the droplets
		echo json_encode($droplets['droplets']);
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
				
				echo json_encode(array(
					"success" => TRUE,
					"bucket" => array(
						'id' => $bucket->id,
						'bucket_name' => $bucket->bucket_name
						)));
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('bucket');
				echo json_encode(array("success" => FALSE, "errors" => $errors));
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

				echo json_encode(array("success" => TRUE));
			}
			else
			{
				echo json_encode(array("success"=> FALSE));
			}
		}
		else
		{
			echo json_encode(array("success" => FALSE));
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
	
	/**
	 * Returns a JSON response of the list of buckets accessible to the 
	 * currently logged in user
	 */
	public function action_list_buckets()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		echo json_encode($this->user->get_buckets());
	}
}