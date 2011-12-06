<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Controller
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
class Controller_River extends Controller_Swiftriver {

	/**
	 * Channels
	 */
	protected $channels;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Get all available channels from plugins
		$this->channels = Swiftriver_Plugins::channels();
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/river/main')
			->bind('river', $river)
			->bind('droplets', $droplets)
			->bind('filtered_total', $filtered_total)
			->bind('meter', $meter)
			->bind('filters_url', $filters_url)
			->bind('settings_url', $settings_url)
			->bind('more_url', $more_url);

		// First we need to make sure this river
		// actually exists
		$id = (int) $this->request->param('id', 0);
		
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();
		if ( ! $river->loaded())
		{
			// It doesn't -- redirect back to dashboard
			$this->request->redirect('dashboard');
		}

		// Build River Query
		$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
		                    'droplet_title', 'droplet_content', 
		                    'droplets.channel','identity_name', 'droplet_date_pub')
		    ->from('droplets')
		    ->join('channel_filter_droplets', 'INNER')
		    ->on('channel_filter_droplets.droplet_id', '=', 'droplets.id')
	        ->join('channel_filters', 'INNER')
	        ->on('channel_filters.id', '=', 'channel_filter_droplets.channel_filter_id')
	        ->join('identities')
	        ->on('droplets.identity_id', '=', 'identities.id')		    
		    ->where('channel_filters.river_id', '=', $river->id)
		    ->order_by('droplet_date_pub', 'DESC');

		// Clone query before any filters have been applied
		$pre_filter = clone $query;
		$total = (int) $pre_filter->execute()->count();

		// SwiftRiver Plugin Hook -- Hook into River Droplet Query
		//++ Allows for adding for more filters via Plugin
		Swiftriver_Event::run('swiftriver.river.filter', $query);

		// First Pass (Limit 20)
		$query->limit(20);

		// Get our droplets as an Array (not Object)
		$droplets = $query->execute()->as_array();
		$filtered_total = (int) count($droplets);

		// Droplets Meter - Percentage of Filtered Droplets against All Droplets
		$meter = 0;
		if ($total > 0)
		{
			$meter = round( ($filtered_total / $total) * 100 );
		}

		// URL's to pages that are ajax rendered on demand
		$filters_url = url::site().$this->account->account_path.'/river/filters/'.$id;
		$settings_url = url::site().$this->account->account_path.'/river/settings/'.$id;
		$more_url = url::site().$this->account->account_path.'/river/more/';
	}

	/**
	 * Create a New River
	 *
	 * @return	void
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/river/new')
			->bind('post', $post)
			->bind('errors', $errors);
		$this->template->header->js = View::factory('pages/river/js/new');
		$this->template->header->js->settings = url::site().$this->account->account_path.'/river/settings/';

		// save the river
		if ($_POST)
		{
			$river = ORM::factory('river');
			$post = $river->validate($_POST);

			if ($post->check())
			{
				$river->river_name = $post['river_name'];
				$river->account_id = $this->account->id;
				$river->save();

				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('river/index/'.$river->id);
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('river');
			}

		}
	}

	/**
	 * Ajax rendered filter control box
	 * 
	 * @return	void
	 */
	public function action_filters()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		echo View::factory('pages/river/filters_control');
	}

	/**
	 * Ajax rendered settings control box
	 * 
	 * @return	void
	 */
	public function action_settings()
	{
		$id = (int) $this->request->param('id', 0);
		
		// Get River (if set)
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->account->id)
			->find();

		$this->template = '';
		$this->auto_render = FALSE;
		$settings = View::factory('pages/river/settings_control');
		$settings->channels = $this->channels;
		$settings->river = $river;
		echo $settings;
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
		echo View::factory('pages/river/more_control');
	}	

	/**
	 * Ajax Title Editing Inline
	 *
	 * Edit River Name
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

			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['edit_id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($river->loaded())
			{
				$river->river_name = $_REQUEST['edit_value'];
				$river->save();
			}
		}
	}

	/**
	 * Ajax Delete River
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
			$river = ORM::factory('river')
				->where('id', '=', $_REQUEST['id'])
				->where('account_id', '=', $this->account->id)
				->find();

			if ($river->loaded())
			{
				$river->delete();
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