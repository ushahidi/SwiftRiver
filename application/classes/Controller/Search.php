<?php defined('SYSPATH') or die('No direct script acccess'); 
/**
 * Search controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
	 * @var string
	 */
	private $search_scope;

	/**
	 * View for the content below the navigation section
	 * @var Kohana_View
	 */
	private $sub_content;

	/**
	 * Whether to display photos
	 * @var bool
	 */
	private $photos = FALSE;


	public function before()
	{
		parent::before();

		// Check fo URL parameters
		if ( ! empty($_GET['q']))
		{
			// Sanitize the search term - strip all HTML
			// Layout for the seach page
			$this->template->content = View::factory('pages/search/layout')
			    ->bind('sub_content', $this->sub_content)
			    ->bind('search_term', $this->search_term)
			    ->bind('url_params', $this->url_params);

			// Set the active tab
			$this->template->content->active = $this->request->action();

			// Get the search term
			$this->search_term = trim(strip_tags($_GET['q']));

			if (in_array($this->request->action(), array('photos', 'list')))
			{
				// Set the search scope to the previous search scope
				// Necessary when the user the search is on a river/bucket
				// so that the user doesn't get presented with extra tabs
				$search_scope = Cookie::get(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);
				if ($search_scope != 'all')
				{
					$this->search_scope = $search_scope;
				}
			}
			else
			{
				// Set the search scope
				$this->search_scope = (isset($_GET['search_scope'])) 
				    ? $_GET['search_scope'] 
				    : Cookie::get(Swiftriver::COOKIE_SEARCH_SCOPE);
			}

			// Defaults the scope to 'all' if no scope exists
			if (empty($this->search_scope))
			{
				$this->search_scope = "all";
			}

			// Set the search scope
			Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, $this->search_scope);

			// URL Parameters
			$this->url_params = http_build_query(array('q'=>$this->search_term));

			// If previous scope if empty, set to the current search scope
			$previous_scope = Cookie::get(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);
			if (empty($previous_scope))
			{
				$this->template->content->search_scope = $this->search_scope;
			}
		}

	}

	/**
	 * Landing page
	 */
	public function action_index()
	{
		$this->template->header->title = __("Search");

		// Check for seach query
		if ( ! empty($_GET['q']))
		{
			$this->sub_content = View::factory('pages/drop/drops')
			    ->bind('droplet_js', $droplet_js)
			    ->bind('user', $this->user)
			    ->bind('owner', $this->owner)
		        ->bind('anonymous', $this->anonymous);

		    // Suppress expiry notices
		    $this->sub_content->expiry_notice = "";

			// Get the search results
			$results = $this->_handle_drops_search($_GET);

			// Bootstrap the droplet list
			$droplet_js = View::factory('pages/drop/js/drops')
			    ->bind('user', $this->user);

			$droplet_js->filters = json_encode(array(
				'q' => $this->search_term,
				'scope' => $this->search_scope
			));

			$droplet_js->fetch_base_url = URL::site().'search';
			$droplet_js->droplet_list = @json_encode($results['droplets']);
			$droplet_js->max_droplet_id = PHP_INT_MAX;
			$droplet_js->bucket_list = json_encode($this->user->get_buckets_array($this->user));
			$droplet_js->polling_enabled = FALSE;
			$droplet_js->channels = json_encode(array());
			$droplet_js->default_view = "list";
			$droplet_js->photos = $this->photos ? 1 : 0;

			$this->sub_content->nothing_to_display = View::factory('pages/search/nothing_to_display');
			$this->sub_content->nothing_to_display->search_term = $this->search_term;

			// Set the current search scope as the previous one
			$this->template->content->search_scope = $this->search_scope;
			Cookie::set(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE, $this->search_scope);

			// Reset the search scope
			Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, 'all');
		}
		else
		{
			Kohana::$log->add(Log::INFO, "Something went wrong! Deleting search cookies");

			// No search data - clean out any existing data
			Cookie::delete(Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);
			Cookie::delete(Swiftriver::COOKIE_SEARCH_ITEM_ID);

			// Redirect to the dashboard
			$this->redirect($this->dashboard_url, 302);
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
		else
		{
			$this->redirect($this->dashboard_url, 302);
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

	public function action_photos()
	{
		$this->photos = TRUE;
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

				// Suppress encoding errors - Temporary measure for now
				echo @json_encode($results['droplets']);
			break;
			
			case "PUT":
				// No anonymous actions
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}

				$droplet_array = json_decode($this->request->body(), TRUE);
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('Droplet', $droplet_id);
				$droplet_orm->update_from_array($droplet_array);
			break;
			
			case "DELETE":
				$droplet_id = intval($this->request->param('id', 0));
				$droplet_orm = ORM::factory('Droplet', $droplet_id);
				
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
				
				ORM::factory('Bucket', $this->bucket->id)->remove('droplets', $droplet_orm);
			break;
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
		// Get the page number for the request
		$page = (isset($parameters['page']) AND intval($parameters['page']) > 0) 
		    ? intval($parameters['page']) 
		    : 1;

		// Array to store each of the search results items
		$results = array(
			// Matched droplets
			'droplets' => array(),

			// Page used for the search
			'page' => $page
		);

		$user_id = $this->user->id;

		// Query filters for the droplet fetch
		$query_filters = array(
			'places' => array($this->search_term),
			'tags' => array($this->search_term)
		);

		// Check the search scope
		switch ($this->search_scope)
		{
			// Global search
			case 'all':
				// Get the droplets
				$results['droplets'] = Model_Droplet::search($query_filters,
				    $user_id, $page, $this->photos);

			break;

			// River search
			case 'river':
				// Get the river id
				$river_id = Cookie::get('search_item_id');

				$data = Model_River::get_droplets($user_id, $river_id, 0, $page, 
					PHP_INT_MAX, 'DESC', $query_filters, $this->photos);

				$results['droplets'] = $data['droplets'];
			break;

			// Bucket search
			case 'bucket':
				// Get the bucket id
				$bucket_id = Cookie::get('search_item_id');

				// Get the droplets
				$data = Model_Bucket::get_droplets($user_id, $bucket_id, 0, $page, 
					PHP_INT_MAX, $this->photos, $query_filters);

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
		$this->sub_content = View::factory('pages/search/buckets')
		    ->bind('search_term', $this->search_term)
		    ->bind('buckets', $buckets);
		
		$this->template->content->search_scope = Cookie::get(
			Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);

		// Get buckets - public, owned and those collaborating on
		$buckets = Model_Bucket::get_like($this->search_term, $this->user->id);
	}


	/**
	 * Searches and displays the users
	 */
	public function action_rivers()
	{
		$this->sub_content = View::factory('pages/search/rivers')
		    ->bind('search_term', $this->search_term)
		    ->bind('rivers', $rivers);

		$this->template->content->search_scope = Cookie::get(
			Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);

		// Get rivers - public, owned and those collaborating on
		$rivers = Model_River::get_like($this->search_term, $this->user->id);
	}

	public function action_users()
	{
		$this->sub_content = View::factory('pages/search/users')
		    ->bind('search_term', $this->search_term)
		    ->bind('users', $users);

		$this->template->content->search_scope = Cookie::get(
			Swiftriver::COOKIE_PREVIOUS_SEARCH_SCOPE);
			
		// Get the ids of users with private accounts
		$private_user_ids = DB::select('user_id')
				->from('accounts')
				->where('account_private', '=', 1)
				->execute()
				->as_array();

		// Get users
		$users = Model_User::get_like($this->search_term, $private_user_ids);
	}
}
?>