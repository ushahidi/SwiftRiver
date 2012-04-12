<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Create River Controller
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
class Controller_River_Create extends Controller_River {
	
	/**
	 * Which step in the river creation process?
	 * @var step
	 */
	protected $step = 'name';
	
	/**
	 * Account Owner
	 * @var string
	 */
	protected $account_path = NULL;

	/**
	 * This steps content/form
	 * @var string
	 */
	protected $step_content = NULL;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		// Only account owners are alllowed here
		if ( ! $this->account->is_owner($this->visited_account->user->id) OR $this->anonymous)
		{
			throw new HTTP_Exception_403();
		}

		// The main create template
		$this->template->content = View::factory('pages/river/create')
			->bind('account_path', $this->account_path)
			->bind('step', $this->step)
			->bind('step_content', $this->step_content);

		// Account Path
		$this->account_path = $this->user->account->account_path;
	}

	/**
	 * Create a New River
	 * Step 1
	 * @return	void
	 */
	public function action_index()
	{
		$this->step_content = View::factory('pages/river/create/name')
			->bind('post', $post)
			->bind('errors', $errors);

		// Check for form submission
		if ($_POST AND CSRF::valid($_POST['form_auth_id']))
		{

			$post = Arr::extract($_POST, array('river_name', 'river_public'));
			try
			{
				$river = Model_River::create_new($post['river_name'], $post['river_public'], $this->user->account);

				// Redirect to the /create/open/<id> to open channels
				$this->request->redirect(URL::site().$this->account_path.'/river/create/open/'.$river->id);
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('validation');
			}
			catch (Database_Exception $e)
			{
				$errors = array(__("A river with the name ':name' already exists", 
					array(':name' => $post['river_name'])
				));
			}
		}		
	}

	/**
	 * Create a New River
	 * Step 2 - Open Channels
	 * @return	void
	 */
	public function action_open()
	{
		// This River
		$id = $this->request->param('id', 0);
		$river = ORM::factory('river')
			->where('id', '=', $id)
			->where('account_id', '=', $this->visited_account->id)
			->find();

		if ( ! $river->loaded())
		{
			$this->request->redirect(URL::site().$this->account_path.'/river/create');
		}

		$this->step_content = View::factory('pages/river/settings/channels')
			->bind('channels_url', $channels_url)
			->bind('channel_options_url', $channel_options_url)
			->bind('channels_list', $channels_list);

		$this->step = 'open';

		// Get the base url for this specific river
		$river_base_url = $river->get_base_url();

		// URLs for XHR endpoints
		$channels_url = $river_base_url.'/channels';
		$channel_options_url = $river_base_url.'/channel_options';

		$channels_list = json_encode($river->get_channel_filter_data());
	}

	/**
	 * Create a New River
	 * Step 3 - View the River
	 * @return	void
	 */
	public function action_view()
	{
		$this->step_content = View::factory('pages/river/create/view');
		$this->step = 'view';
	}	
}