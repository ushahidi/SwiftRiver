<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Create River Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
			->bind('step_content', $this->step_content)
			->bind('open_url', $this->open_url)
			->bind('view_url', $this->view_url);

		// Account Path
		$this->account_path = $this->user->account->account_path;
		// Open Url
		$this->open_url = '#';
		// View Url
		$this->view_url = '#';
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

				// Delete the rivers cache so that it's recreated on page reload
				Cache::instance()->delete('user_rivers_'.$this->user->id);

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
		$this->step_content = View::factory('pages/river/settings/channels');
		$this->step = 'open';	

		// This River
		$id = $this->request->param('id', 0);
		$river = ORM::factory('river', $id);
		if ( ! $river->loaded())
		{
			$this->request->redirect(URL::site().$this->account_path.'/river/create');
		}

		$this->step_content->channels_config = json_encode(Swiftriver_Plugins::channels());
		$this->step_content->channels = json_encode($river->get_channels(TRUE));
		$this->step_content->base_url = $river->get_base_url().'/settings/channels';

		// Open Url
		$this->open_url = URL::site().$this->account_path.'/river/create/open/'.$river->id;
		// View Url
		$this->view_url = URL::site().$this->account_path.'/river/create/view/'.$river->id;	
	}

	/**
	 * Create a New River
	 * Step 3 - View the River
	 * @return	void
	 */
	public function action_view()
	{
		$this->step_content = View::factory('pages/river/create/view')
			->bind('river_base_url', $river_base_url);
		$this->step = 'view';

		// This River
		$id = $this->request->param('id', 0);
		$river = ORM::factory('river', $id);
		if ( ! $river->loaded())
		{
			$this->request->redirect(URL::site().$this->account_path.'/river/create');
		}

		$river_base_url = $river->get_base_url();

		// Open Url
		$this->open_url = URL::site().$this->account_path.'/river/create/open/'.$river->id;
		// View Url
		$this->view_url = URL::site().$this->account_path.'/river/create/view/'.$river->id;	
	}	
}