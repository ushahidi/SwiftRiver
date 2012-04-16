<?php defined('SYSPATH') or die('No direct script acccess'); 
/**
 * Search controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Search extends Controller_Swiftriver {

	/**
	 * @var string
	 * Scope of the search
	 */
	private $search_scope;

	/**
	 * Term being searched
	 * @var string
	 */
	private $search_term;

	/**
	 * Search filters
	 * @var array
	 */
	private $filters;
	

	public function action_index()
	{
		$this->template->header->title = __("Search");

		$this->template->content = View::factory('pages/search/main')
		    ->bind('search_term', $this->search_term)
		    ->bind('droplets_view', $droplets_view)
		    ->bind('search_scope', $this->search_scope);

		// Check for seach query
		if ( ! empty($_GET['q']))
		{
			// Get the search results
			$results = $this->_handle_search($_GET);

			// Bootstrap the droplet list
			$droplet_js = View::factory('pages/drop/js/drops')
			    ->bind('user', $this->user)
			    ->bind('filters', $this->filters);

			$droplet_js->fetch_base_url = URL::site().'search';
			$droplet_js->droplet_list = json_encode($results['droplets']);
			$droplet_js->max_droplet_id = $results['droplets'][0]['id'];
			$droplet_js->bucket_list = json_encode($this->user->get_buckets_array());
			$droplet_js->polling_enabled = FALSE;

			// Generate the List HTML
			$droplets_view = View::factory('pages/drop/drops')
				->bind('droplet_js', $droplet_js)
				->bind('user', $this->user)
				->bind('owner', $this->owner)
			    ->bind('anonymous', $this->anonymous);

			$droplets_view->nothing_to_display = View::factory('pages/search/nothing_to_display')
			    ->bind('search_term', $this->search_term);


			// Set the search scope to all
			Session::instance()->set('search_scope', 'all');
			
		}

	}

	// Aliases for the index action
	public function action_drops()
	{
		$this->action_index();
	}

	public function action_list()
	{
		$this->action_index();
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
		
		switch ($this->request->method())
		{
			case "GET":
				$results = $this->_handle_search($_GET);
				
				//Throw a 404 if a non existent page is requested
				if (empty($results['droplets']))
				{
				    throw new HTTP_Exception_404(
				        'The requested page :page was not found on this server.',
				        array(':page' => $results['page'])
				        );
				}
				
				echo json_encode($results['droplets']);
			break;
			
			case "PUT":
				// No anonymous actions
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}
			
				$droplet_array = json_decode($this->request->body(), TRUE);
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('droplet', $droplet_id);
				$droplet_orm->update_from_array($droplet_array);
			break;
			
			case "DELETE":
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('droplet', $droplet_id);
				
				// Does the user exist
				if ( ! $droplet_orm->loaded())
				{
					throw new HTTP_Exception_404(
				        'The requested page :page was not found on this server.',
				        array(':page' => $page)
				        );
				}
				
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				ORM::factory('bucket', $this->bucket->id)->remove('droplets', $droplet_orm);
		}
	}

	/**
	 * Internal helper that performs the search and returns the results
	 * to the invoking controller action
	 *
	 * @param array $parameters An array of search parameters
	 * @return array
	 */
	private function _handle_search($parameters)
	{
		// Sanitize the search term - strip all HTML
		$this->search_term = strip_tags($parameters['q']);
		$this->search_scope = $parameters['scope'];

		// Get the page number for the request
		$page = (isset($parmaters['page']) AND intval($parameters['page']) > 0) 
		    ? intval($parameters['page']) 
		    : 1;

		// Array to store each of the search results items
		$results = array(
			// Matched droplets
			'droplets' => array(),

			// Matched users
			'users' => array(),

			// Matched buckets
			'buckets' => array(),

			// Matched rivers
			'rivers' => array(),

			// Page used for the search
			'page' => $page
		);

		// Build the search filters as HTTP query parameters
		$this->filters = http_build_query(array(
			'scope' => $this->search_scope, 
			'q'=>$this->search_term
		));

		// Check the search scope
		switch ($this->search_scope)
		{
			// Global search
			case 'all':
				// Get users
				$results['users'] = Model_User::get_like($this->search_term);

				// Get buckets - public, owned and those collaborating on
				$results['buckets'] = Model_Bucket::get_like($this->search_term, 
					$this->user->id);

				// Get rivers - public, owned and those collaborating on
				$results['rivers'] = Model_River::get_like($this->search_term, 
					$this->user->id);

				// Get the droplets
				$results['droplets'] = Model_Droplet::search($this->search_term, 
					$this->user->id, $page);

			break;

			// River search
			case 'river':
				// Get the river id
				$river_id = Session::instance()->get('search_river_id');
				$results['droplets'] = Model_River::search($this->search_term, 
					$river_id, $this->user->id, $page);
			break;

			// Bucket search
			case 'bucket':
				// Get the bucket id
				$bucket_id = Session::instance()->get('search_bucket_id');

				// Get the droplets
				$results['droplets'] = Model_Bucket::search($this->search_term, 
					$bucket_id, $this->user->id, $page);
			break;

		}

		// Return
		return $results;
	}
}
?>