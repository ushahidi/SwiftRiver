<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Project Feed Builder Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Project_Builder extends Controller_Project_Main {
	
	/**
	 * @return	void
	 */
	public function before($id = NULL)
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->tab_menu->active = 'builder';
		$this->services = Plugins::services();
	}
	
	/**
	 * List all the Feeds
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index($page = NULL)
	{
		$this->template->content = View::factory('pages/project/builder/overview')
			->bind('services', $this->services)
			->bind('feeds', $result)
			->bind('paging', $pagination)
			->bind('default_sort', $sort)
			->bind('total', $total)
			->bind('project', $this->project);
		
		// Feeds
		$feeds = ORM::factory('feed');
		// Get the total count for the pagination
		$total = $feeds->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'service'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $feeds->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
	
	/**
	 * Create a New Feed - Select Feed Service
	 *
	 * @return	void
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/project/builder/new')
			->bind('project', $this->project)
			->bind('services', $this->services);
	}


	/**
	 * Select Feed Parameters
	 *
	 * @return	void
	 */
	public function action_parameters()
	{
		$this->template->content = View::factory('pages/project/builder/parameters')
			->bind('project', $this->project)
			->bind('service', $_POST['service'])
			->bind('service_name', $service_name)
			->bind('service_options', $service_options);
			
		if ($_POST 
			AND isset($_POST['service']) 
			AND ! empty($_POST['service']))
		{
			if (Plugins::has_service($_POST['service']))
			{
				$service_name = $this->services[$_POST['service']];
				$service_options = Plugins::get_service_options($_POST['service']);
			}
			else
			{
				// Redirect Back
				Request::current()->redirect('project/'.$this->project->id.'/builder/new');
			}
		}
		else
		{
			// Redirect Back
			Request::current()->redirect('project/'.$this->project->id.'/builder/new');
		}
	}


	/**
	 * Enter Service Option Values
	 *
	 * @return	void
	 */
	public function action_options()
	{
		$this->template->content = View::factory('pages/project/builder/options')
			->bind('project', $this->project)
			->bind('service', $_POST['service'])
			->bind('service_option', $_POST['service_option'])
			->bind('service_name', $service_name)
			->bind('service_option_name', $service_option_name)
			->bind('service_option_fields', $service_option_fields);
			
		if ($_POST 
			AND isset($_POST['service']) 
			AND ! empty($_POST['service'])
			AND isset($_POST['service_option']) 
			AND ! empty($_POST['service_option']))
		{
			if (Plugins::has_service($_POST['service']))
			{
				$service_name = $this->services[$_POST['service']];
				$service_options = Plugins::get_service_options($_POST['service']);

				// Make sure the service option we're about to work on
				// exists
				if (isset($service_options[$_POST['service_option']]) 
					AND is_array($service_options[$_POST['service_option']]))
				{
					$service_option_name = $service_options[$_POST['service_option']]['name'];
					$service_option_fields = $service_options[$_POST['service_option']]['fields'];
				}
				else
				{
					// Redirect Back
					Request::current()->redirect('project/'.$this->project->id.'/builder/new');
				}
			}
			else
			{
				// Redirect Back
				Request::current()->redirect('project/'.$this->project->id.'/builder/new');
			}
		}
		else
		{
			// Redirect Back
			Request::current()->redirect('project/'.$this->project->id.'/builder/new');
		}
	}


	/**
	 * Confirm all the details we've entered
	 *
	 * @return	void
	 */
	public function action_confirm()
	{
		$this->template->content = View::factory('pages/project/builder/confirm')
			->bind('project', $this->project)
			->bind('service', $_POST['service'])
			->bind('service_option', $_POST['service_option'])
			->bind('service_name', $service_name)
			->bind('service_options', $service_options)
			->bind('service_option_name', $service_option_name)
			->bind('service_option_fields', $service_option_fields)
			->bind('options', $_POST['options']);		

		if ($_POST 
			AND isset($_POST['service']) 
			AND ! empty($_POST['service'])
			AND isset($_POST['service_option']) 
			AND ! empty($_POST['service_option'])
			AND isset($_POST['options']))
		{
			if (Plugins::has_service($_POST['service']))
			{
				$service_name = $this->services[$_POST['service']];
				$service_options = Plugins::get_service_options($_POST['service']);

				// Make sure the service option we're about to work on
				// exists
				if (isset($service_options[$_POST['service_option']]) 
					AND is_array($service_options[$_POST['service_option']]))
				{
					$service_option_name = $service_options[$_POST['service_option']]['name'];
					$service_option_fields = $service_options[$_POST['service_option']]['fields'];
				}
				else
				{
					// Redirect Back
					Request::current()->redirect('project/'.$this->project->id.'/builder/new');
				}
			}
			else
			{
				// Redirect Back
				Request::current()->redirect('project/'.$this->project->id.'/builder/new');
			}
		}
		else
		{
			// Redirect Back
			Request::current()->redirect('project/'.$this->project->id.'/builder/new');
		}
	}


	/**
	 * Save our work!
	 *
	 * @return	void
	 */
	public function action_save()
	{
		if ($_POST 
			AND isset($_POST['service']) 
			AND ! empty($_POST['service'])
			AND isset($_POST['service_option']) 
			AND ! empty($_POST['service_option'])
			AND isset($_POST['options']))
		{
			if (Plugins::has_service($_POST['service']))
			{
				$service_name = $this->services[$_POST['service']];
				$service_options = Plugins::get_service_options($_POST['service']);

				// Make sure the service option we're about to work on
				// exists
				if (isset($service_options[$_POST['service_option']]) 
					AND is_array($service_options[$_POST['service_option']]))
				{
					// First Save Feed
					$feed = ORM::factory('feed');
					$feed->service = $_POST['service'];
					$feed->service_option = $_POST['service_option'];
					$feed->save();


					// Save Feed Options
					foreach ($_POST['options'] as $key => $value)
					{
						$option = ORM::factory('feed_option');
						$option->feed_id = $feed->id;
						$option->key = $key;
						$option->value = $value;
						$option->save();
					}

					// Done, Redirect
					Request::current()->redirect('project/'.$this->project->id.'/builder');
				}
				else
				{
					// Redirect Back
					Request::current()->redirect('project/'.$this->project->id.'/builder/new');
				}
			}
			else
			{
				// Redirect Back
				Request::current()->redirect('project/'.$this->project->id.'/builder/new');
			}
		}
		else
		{
			// Redirect Back
			Request::current()->redirect('project/'.$this->project->id.'/builder/new');
		}
	}	
}