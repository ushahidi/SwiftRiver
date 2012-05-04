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
	 * Search filters
	 * @var array
	 */
	private $url_params;

	/**
	 * @var string
	 */
	private $search_term;

	/**
	 * View for the content below the navigation section
	 * @var Kohana_View
	 */
	private $sub_content;


	public function before()
	{
		parent::before();

		// Layout for the seach page
		$this->template->content = View::factory('pages/search/layout')
		    ->bind('sub_content', $this->sub_content);

		// Bind/set search term
		$search_term = Cookie::get(Swiftriver::COOKIE_SEARCH_TERM);
		if (empty($search_term))
		{
			$this->template->content->bind('search_term', $this->search_term);
		}
		else
		{
			$this->search_term = $search_term;
			$this->template->content->search_term = $search_term;
		}
		
		// Bind/set URL Parameters
		$url_params = Cookie::get('url_params');
		if (empty($url_params))
		{
			$this->template->content->bind('url_params', $this->url_params);
		}
		else
		{
			$this->template->content->url_params = $url_params;
		}
	}

	/**
	 * Landing page
	 */
	public function action_index()
	{
		$this->template->header->title = __("Search");
		$this->template->content->active = 'drops';

		// Check for seach query
		if ( ! empty($_GET['q']))
		{
			$this->sub_content = View::factory('pages/drop/drops')
			    ->bind('droplet_js', $droplet_js)
			    ->bind('user', $this->user)
			    ->bind('owner', $this->owner)
		        ->bind('anonymous', $this->anonymous);

			// Get the search results
			$results = $this->_handle_drops_search($_GET);

			// Reset the search term - for cases where parent() uses
			// a stale copy of the search term
			$this->template->content->search_term = $this->search_term;

			// Bootstrap the droplet list
			$droplet_js = View::factory('pages/drop/js/drops')
			    ->bind('user', $this->user)
			    ->bind('filters', $filters);

			// Store the current search scope before overwriting it
			Cookie::set(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE, 
				Cookie::get(Swiftriver::COOKIE_SEARCH_SCOPE));

			$filters = json_encode(array(
				'q' => Cookie::get(Swiftriver::COOKIE_SEARCH_TERM),
				'scope' => Cookie::get(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE)
			));

			$droplet_js->fetch_base_url = URL::site().'search';
			$droplet_js->droplet_list = @json_encode($results['droplets']);
			$droplet_js->max_droplet_id = PHP_INT_MAX;
			$droplet_js->bucket_list = json_encode($this->user->get_buckets_array());
			$droplet_js->polling_enabled = FALSE;
			$droplet_js->channels = json_encode(array());
			$droplet_js->default_view = "drops";

			$this->sub_content->nothing_to_display = View::factory('pages/search/nothing_to_display');
			$this->sub_content->nothing_to_display->search_term = $this->search_term;

			// Reset the search scope
			Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, 'all');
		}
		else
		{
			// No search data - clean out any existing data
			Cookie::delete(Swiftriver::COOKIE_SEARCH_TERM);
			Cookie::delete(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);
			Cookie::delete(Swiftriver::COOKIE_SEARCH_ITEM_ID);
			Cookie::delete('url_params');

			// Redirect to the dashboard
			$this->request->redirect($this->dashboard_url);
		}

	}

	/**
	 * Loads the search dialog
	 */
	public function action_main()
	{
		// Only serve the dialog via XHR
		if ($this->request->is_ajax())
		{
			$this->template = View::factory('pages/search/main');
			$search_scope = Cookie::get(Swiftriver::COOKIE_SEARCH_SCOPE);
			$this->template->search_scope = $search_scope;
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

	public function action_drop()
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
				$results = $this->_handle_drops_search($_GET);
				
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
	private function _handle_drops_search($parameters)
	{
		// Sanitize the search term - strip all HTML
		$this->search_term = strip_tags($parameters['q']);

		Cookie::set(Swiftriver::COOKIE_SEARCH_TERM, $this->search_term);

		// Get the scope of the search
		$search_scope = (isset($parameters['search_scope'])) 
		    ? $parameters['search_scope'] 
		    : Cookie::get(Swiftriver::COOKIE_SEARCH_SCOPE);

		// Defaults the scope to 'all' if no scope exists
		if (empty($search_scope))
		{
			$search_scope = "all";
		}

		// Reset 'previous_search_scope' to all
		if ($search_scope == "all")
		{
			Cookie::set(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE, 'all');
		}

		// Reset the search scope - for cases where the value
		// in $parameters is different from the one in cookie data
		Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, $search_scope);

		// Get the page number for the request
		$page = (isset($parmaters['page']) AND intval($parameters['page']) > 0) 
		    ? intval($parameters['page']) 
		    : 1;

		// Array to store each of the search results items
		$results = array(
			// Matched droplets
			'droplets' => array(),

			// Page used for the search
			'page' => $page
		);

		// Build the search filters as HTTP query parameters
		$this->url_params = http_build_query(array('q'=>$this->search_term));
		Cookie::set('url_params', $this->url_params);

		$user_id = $this->user->id;

		// Query filters for the droplet fetch
		$query_filters = array(
			'places' => array($this->search_term),
			'tags' => array($this->search_term)
		);

		// Check the search scope
		switch ($search_scope)
		{
			// Global search
			case 'all':
				// Get the droplets
				$results['droplets'] = Model_Droplet::search($query_filters, $user_id, $page);

			break;

			// River search
			case 'river':
				// Get the river id
				$river_id = Cookie::get('search_item_id');

				$data = Model_River::get_droplets($user_id, $river_id, 0, $page, 
					PHP_INT_MAX, 'DESC', $query_filters);

				$results['droplets'] = $data['droplets'];
			break;

			// Bucket search
			case 'bucket':
				// Get the bucket id
				$bucket_id = Cookie::get('search_item_id');

				// Get the droplets
				$data = Model_Bucket::get_droplets($user_id, $bucket_id, 0, $page, 
					PHP_INT_MAX, $query_filters);

				$results['droplets'] = $data['droplets'];
			break;

		}

		// Return
		return $results;
	}

	/**
	 * Searches and display the buckets
	 */
	public function action_buckets()
	{
		$this->template->content->active = 'buckets';

		$this->sub_content = View::factory('pages/search/buckets')
		    ->bind('search_term', $this->search_term)
		    ->bind('buckets', $buckets);
		
		$this->search_term = Cookie::get(Swiftriver::COOKIE_SEARCH_TERM);

		// Get buckets - public, owned and those collaborating on
		$buckets = Model_Bucket::get_like($this->search_term, $this->user->id);
	}


	/**
	 * Searches and displays the users
	 */
	public function action_rivers()
	{
		$this->template->content->active = 'rivers';

		$this->sub_content = View::factory('pages/search/rivers')
		    ->bind('search_term', $this->search_term)
		    ->bind('rivers', $rivers);

		$this->search_term = Cookie::get(Swiftriver::COOKIE_SEARCH_TERM);

		// Get rivers - public, owned and those collaborating on
		$rivers = Model_River::get_like($this->search_term, $this->user->id);
	}

	public function action_users()
	{
		$this->template->content->active = 'users';

		$this->sub_content = View::factory('pages/search/users')
		    ->bind('search_term', $this->search_term)
		    ->bind('users', $users);

		$this->search_term = Cookie::get(Swiftriver::COOKIE_SEARCH_TERM);

		// Get users
		$users = Model_User::get_like($this->search_term);
	}
}
?>