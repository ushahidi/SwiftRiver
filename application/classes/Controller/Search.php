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
	 * @var string
	 */
	private $search_term;

	/**
	 * View for the content below the navigation section
	 * @var Kohana_View
	 */
	private $search_results;
	
	/**
	 * @var Service_Search
	 */
	private $search_service;
	
	/**
	 * Type of search being performed. Possible values are:
	 *    drops, rivers, buckets, users
	 *
	 * @var string
	 */
	private $search_type;

	public function before()
	{
		parent::before();

		$this->search_service = new Service_Search($this->api);

		// Check fo URL parameters
		if ( ! empty($_GET['q']))
		{
			// Sanitize the search term - strip all HTML
			// Layout for the seach page
			$this->template->content = View::factory('pages/search/layout')
				->bind('search_results', $this->search_results)
				->bind('search_term', $this->search_term)
				->bind('active', $this->search_type)
				->bind('navigation_links', $navigation_links);

			// Set the active tab
			$active = strtolower($this->request->query('type'));
			$this->search_type = empty($active) ? 'drops' : $active;

			// Get the search term
			$this->search_term = urldecode($this->request->query('q'));
			$navigation_links = $this->get_search_nav();
		}
		else
		{
			$this->template->content = View::factory('pages/search/main');
		}
	}

	/**
	 * Landing page
	 */
	public function action_index()
	{
		$this->template->header->title = __("Search");

		// Check for seach query
		if ( ! empty($this->search_term))
		{
			// Check for the search type
			switch ($this->search_type)
			{
				case "drops":
					$this->drop_search();
				break;
				
				case "rivers":
					$this->river_search();
				break;
				
				case "buckets":
					$this->bucket_search();
				break;
				
				case "users":
					$this->user_search();
				break;
				
				default:
					$this->drop_search();
			}
		}
	}

	public function action_drops()
	{
		$this->action_index();
	}

	/**
	 * Internal helper to handle drop search queries
	 */
	private function drop_search()
	{
		// Bootstrap the droplet list
		$this->template->header->js .= HTML::script("themes/default/media/js/drops.js");

		$this->search_results = View::factory('pages/drop/drops')
			->set('no_content_view', '')
			->set('asset_templates', View::factory('template/assets'))
			->bind('droplet_js', $droplet_js)
			->bind('user', $this->user)
			->bind('owner', $this->owner)
			->bind('anonymous', $this->anonymous);

		// Bootstrap the droplet list
		$droplet_js = View::factory('pages/drop/js/drops')
			->set('default_view', 'drops')
			->set('photos', 0)
			->set('polling_enabled', FALSE)
			->set('max_droplet_id', PHP_INT_MAX)
			->bind('user', $this->user)
			->bind('filters', $filters)
			->bind('fetch_base_url', $fetch_base_url)
			->bind('droplet_list', $droplet_list);

		$filters = json_encode(array('q' => rawurlencode($this->search_term)));
		$fetch_base_url = URL::site('/search');
		$droplet_list = json_encode($this->search_service->find_drops($this->search_term));
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
		
		$this->response->headers('Content-type', 'application/json;charset=UFT-8');
		switch ($this->request->method())
		{
			case "GET":
			// Get the page
			$page = intval($this->request->query('page'));
			
			if ($page > 0)
			{
				$drops = $this->search_service->find_drops($this->search_term, $page);
				
				echo json_encode($drops);
			}
			break;
		}
	}

	/**
	 * Searches and display the buckets
	 */
	private function bucket_search()
	{
		$this->search_results = View::factory('pages/search/buckets')
		    ->bind('search_term', $this->search_term)
		    ->bind('buckets', $buckets);
			
		$buckets = $this->search_service->find_buckets($this->search_term, $this->user);
	}


	/**
	 * Searches and displays the users
	 */
	private function river_search()
	{
		$this->search_results = View::factory('pages/search/rivers')
		    ->bind('search_term', $this->search_term)
		    ->bind('rivers', $rivers);
			
		$rivers = $this->search_service->find_rivers($this->search_term, $this->user);
	}

	private function user_search()
	{
		$this->search_results = View::factory('pages/search/users')
		    ->bind('search_term', $this->search_term)
		    ->bind('users', $users);
		
		$users = $this->search_service->find_users($this->search_term);	
	}
	
	/**
	 * Returns the navigation links for the search page
	 *
	 * @return array
	 */
	private function get_search_nav()
	{
		$base_url = URL::site('search').'?';

		$search_nav = array(
			// Drops search link
			'drops' => array(
				'label' => __("Drops"),
				'link' => $base_url.http_build_query(array('q' => $this->search_term, 'type' => 'drops'))
			),
			
			// Buckets
			'buckets' => array(
				'label' => __("Buckets"),
				'link' => $base_url.http_build_query(array('q' => $this->search_term, 'type' => 'buckets'))
			),
			
			// Rivers
			'rivers' => array(
				'label' => __("Rivers"),
				'link' => $base_url.http_build_query(array('q' => $this->search_term, 'type' => 'rivers'))
			),
			
			// Users
			'users' => array(
				'label' => __("Users"),
				'link' => $base_url.http_build_query(array('q' => $this->search_term, 'type' => 'users'))
			)
		);
		
		return $search_nav;
	}
}
?>