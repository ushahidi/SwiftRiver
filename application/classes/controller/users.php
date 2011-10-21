<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Users Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Users extends Controller_Swiftriver {
	
	/**
	 * Access privileges for this controller and its children
	 */
	public $auth_required = 'admin';

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		$this->template->header->page_title = __('Users');
		$this->template->header->tab_menu = View::factory('pages/users/menu');
		$this->template->header->tab_menu->active = "";
	}
	
	/**
	 * List all the Users
	 *
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/users/overview')
			->bind('users', $result)
			->bind('total', $total)
			->bind('paging', $pagination)
			->bind('default_sort', $sort);
		
		// Users
		$users = ORM::factory('user');
		// Get the total count for the pagination
		$total = $users->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name'; // set default sorting
		$dir = isset($_GET['dir']) ? 'ASC' : 'DESC'; // set order_by
		$result = $users->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}

	/**
	 * Add / Edit user
	 * @param string $id
	 * @return void
	 */
	public function action_edit()
	{
		$id = $this->request->param('id');
		$this->template->header->page_title = __('Create a new User');
		$this->template->content = View::factory('pages/users/edit')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('all_roles', $all_roles)
			->bind('id', $id);
		$this->template->header->tab_menu->active = 'edit';	

		// save the data
		if (! empty($_POST))
		{
			//FIXME: Use Model_User in the controller insteat ORM::factory() for model generic driver compatibility
			// sample code paths for edit and create
			if (is_numeric($id))
			{
				// EDIT: load the model with ID
				$user = ORM::factory('user', $id);
			}
			else
			{
				// CREATE: do not specify id
				$user = ORM::factory('user');
			}
			if (empty($_POST['password']) || empty($_POST['password_confirm']))
			{
				// force unsetting the password! Otherwise Kohana3 will automatically hash the empty string - preventing logins
				unset($_POST['password'], $_POST['password_confirm']);
			}
			// you can't change your user id
			unset($_POST['id']);
			$user->values($_POST);
			// since we combine both editing and creating here we need a separate variable
			// you can get rid of it if your actions don't need to do that
			$result = false;
			$errors = null;
			if (is_numeric($id))
			{
				// EDIT: check using alternative rules
				try
				{
					$user->update_user($_POST, array(
						'username', 
						'password', 
						'email'
					));
					$result = true;
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors = $e->errors('register');
					$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				}
			}
			else
			{
				// CREATE: check using default rules
				try
				{
					$user->create_user($_POST, array(
						'username', 
						'password', 
						'email'
					));
					$result = true;
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors = $e->errors('register');
					$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				}
			}
			if ($result)
			{
				// roles have to be added separately, and all users have to have the login role
				// you first have to remove the items, otherwise add() will try to add duplicates
				if (is_numeric($id))
				{
					// could also use array_diff, but this is much simpler
					DB::delete('roles_users')->where('user_id', '=', $id)
						->execute();
				}
				$user->add( 'roles', ORM::factory('role')->where('name', '=', 'login')->find() );
				$user->add( 'roles', ORM::factory('role', $_POST['role']) );

				// redirect and exit
				$this->request->redirect('users');
				return;
			}
			else
			{
				// Pass on the old form values --> to AppForm
				$post = $_POST;
			}
		}

		// if an ID is set, load the information
		if (is_numeric($id))
		{
			// instantiatiate a new model
			$user = ORM::factory('user', $id);		
			if ($user->loaded())
			{
				// retrieve roles into array
				foreach($user->roles->find_all() as $role)
				{
					$role = $role->id;
				}

				$post = $user->as_array();
				$post['role'] = $role;
				$this->template->header->page_title = __('Edit').' '.$post['name'];
			}
		}			

		// get all roles
		$all_roles = array();
		$role_model = ORM::factory('role');
		foreach ($role_model->find_all() as $role)
		{
			$all_roles[$role->id] = strtoupper($role->name);
		}
	}	
}
