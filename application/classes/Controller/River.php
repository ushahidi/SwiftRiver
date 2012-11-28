<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Controller
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
class Controller_River extends Controller_Drop_Base {

	/**
	 * Channels
	 */
	protected $channels;
	
	/**
	 * ORM reference for the currently selected river
	 * @var Model_River
	 */
	protected $river;
	
	/**
	 * Boolean indicating whether the logged in user owns the river
	 * @var bool
	 */
	protected $owner = FALSE; 

	/**
	 * Whether the river is newly created
	 * @var bool
	 */
	protected $is_newly_created = FALSE;
	
	/**
	 * Base URL for this river.
	 */
	protected $river_base_url = NULL;
	
	/**
	 * Whether photo drops only are selected.
	 */
	protected $photos = FALSE;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the river name from the url
		$river_name_url = $this->request->param('name');
		$action = $this->request->action();
		
		// This check should be made when this controller is accessed
		// and the database id of the rive is non-zero
		$this->river = ORM::factory('River')
			->where('river_name_url', '=', $river_name_url)
			->where('account_id', '=', $this->visited_account->id)
			->find();
			
		if ($river_name_url AND ! $this->river->loaded() AND $action != 'manage')
		{
			$this->redirect($this->dashboard_url, 302);
		}
		
		// Action involves a specific river, check permissions
		if ($this->river->loaded())
		{					
			$this->owner = $this->river->is_owner($this->user->id);
			$this->collaborator = $this->river->is_collaborator($this->user->id);
			
			// If this river is not public and no ownership...
			if ( ! $this->river->river_public AND 
				 ! $this->owner AND 
				 ! $this->collaborator AND
				 ! $this->river->is_valid_token($this->request->query('at')))
			{
				$this->redirect($this->dashboard_url, 302);
			}

			$this->river_base_url = $this->river->get_base_url();
			$this->settings_url = $this->river_base_url.'/settings';

			// Navigation Items
			$this->nav = Swiftriver_Navs::river($this->river);
			
			if ($this->river->account->user->id == $this->user->id OR 
				$this->river->account->user->username == 'public')
			{
				$this->page_title = $this->river->river_name;
			}
			else
			{
				$this->page_title = $this->river->account->account_path.' / '.$this->river->river_name;
			}
			$this->template->header->title = $this->page_title;

			$this->template->content = View::factory('pages/river/layout')
				->bind('river', $this->river)
				->bind('droplets_view', $this->droplets_view)
				->bind('river_base_url', $this->river_base_url)
				->bind('settings_url', $this->settings_url)
				->bind('owner', $this->owner)
				->bind('anonymous', $this->anonymous)
				->bind('user', $this->user)
				->bind('nav', $this->nav)
				->bind('active', $this->active);
			$this->template->content->is_collaborator = $this->collaborator;
			
			if ( ! $this->owner)
			{
				$river_item = json_encode(array(
					'id' => $this->river->id, 
					'type' => 'river',
					'subscribed' => $this->river->is_subscriber($this->user->id)
				));

				// Action URL - To handle the follow/unfollow actions on the river
				$action_url = URL::site().$this->visited_account->account_path.'/user/river/manage';

				$this->template->content->river_item = $river_item;
				$this->template->content->action_url = $action_url;
			}
		}
	}

	/**
	 * @return	void
	 */
	public function action_index()
	{
		// Get the id of the current river
		$river_id = $this->river->id;

		// Cookies to help determine the search options to display
		Cookie::set(Swiftriver::COOKIE_SEARCH_SCOPE, 'river');
		Cookie::set(Swiftriver::COOKIE_SEARCH_ITEM_ID, $river_id);
				
		// The maximum droplet id for pagination and polling
		$max_droplet_id = Model_River::get_max_droplet_id($river_id);

		// River filters
		$filters = $this->_get_filters();

		//Get Droplets
		$droplets_array = Model_River::get_droplets($this->user->id, $river_id, 0, 1, 
			$max_droplet_id, NULL, $filters, $this->photos);
		
		// Bootstrap the droplet list
		$this->template->header->js .= HTML::script("themes/default/media/js/drops.js");
		$droplet_js = View::factory('pages/drop/js/drops');
		$droplet_js->fetch_base_url = $this->river_base_url;
		$droplet_js->default_view = $this->river->default_layout;
		$droplet_js->photos = $this->photos ? 1 : 0;
		// Check if any filters exist and modify the fetch urls
		$droplet_js->filters = NULL;
		if ( ! empty($filters))
		{
			$droplet_js->filters = json_encode($filters);
		}
		$droplet_js->droplet_list = json_encode($droplets_array['droplets']);
		$droplet_js->max_droplet_id = $max_droplet_id;
		$droplet_js->channels = json_encode($this->river->get_channels());

		// Select droplet list view with drops view as the default if list not specified
		$this->droplets_view = View::factory('pages/drop/drops')
		    ->bind('droplet_js', $droplet_js)
		    ->bind('user', $this->user)
		    ->bind('owner', $this->owner)
		    ->bind('anonymous', $this->anonymous);


		// Show expiry notice to owners only
		if ($this->owner AND $this->river->is_expired($this->owner))
		{
			$this->droplets_view->nothing_to_display = "";

			$expiry_notice = View::factory('pages/river/expiry_notice');
			$expiry_notice->river_base_url = $this->river_base_url."/extend";
			$expiry_notice->extension_period = Model_Setting::get_setting('default_river_lifetime');
			$this->droplets_view->river_notice = $expiry_notice;

		}
		elseif ($this->owner AND $this->river->is_full())
		{
			$this->droplets_view->nothing_to_display = "";
			$this->droplets_view->river_notice = View::factory('pages/river/full_notice');

		}
		else
		{
			$this->droplets_view->river_notice = '';
			$this->droplets_view->nothing_to_display = View::factory('pages/river/nothing_to_display')
			    ->bind('anonymous', $this->anonymous);
			$this->droplets_view->nothing_to_display->river_url = $this->request->url(TRUE);
		}
		
		
		// Extend rivers accessed by an owner during notice perio
		if ($this->owner AND ! $this->river->is_expired($this->owner))
		{
		 	$days_remaining = $this->river->get_days_to_expiry();
			$notice_period = Model_Setting::get_setting('default_river_lifetime');
			
			if (($days_remaining <= $notice_period) AND $this->river->is_notified())
			{
				Kohana::$log->add(Log::DEBUG, __("Extending lifetime of river with id :id", array(':id' => $this->river->id)));
				$this->river->extend_lifetime();
			}
		}

	}
	
	/**
	* Below are aliases for the index.
	*/
	public function action_drops()
	{
		$this->action_index();
	}
	
	public function action_list()
	{
		$this->action_index();
	}

	public function action_photos()
	{
		$this->photos = TRUE;
		$this->action_index();
	}	

	public function action_drop()
	{
		$this->action_index();
	}

	
	/**
	 * XHR endpoint for fetching droplets
	 */
	public function action_droplets()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case "GET":
				$drop_id = $this->request->param('id');
				$page = 1;
				if ($drop_id)
				{
					// Specific drop requested
					$droplets_array = Model_River::get_droplets($this->user->id, 
				    	$this->river->id, $drop_id);
					$droplets = array_pop($droplets_array['droplets']);
				}
				else
				{
					//Use page paramter or default to page 1
					$page = $this->request->query('page') ? intval($this->request->query('page')) : 1;
					$max_id = $this->request->query('max_id') ? intval($this->request->query('max_id')) : PHP_INT_MAX;
					$since_id = $this->request->query('since_id') ? intval($this->request->query('since_id')) : 0;
					$photos = $this->request->query('photos') ? intval($this->request->query('photos')) : 0;
					$filters = $this->_get_filters();

					if ($since_id)
					{
					    $droplets_array = Model_River::get_droplets_since_id($this->user->id, 
					    	$this->river->id, $since_id, $filters, $photos == 1);
					}
					else
					{
					    $droplets_array = Model_River::get_droplets($this->user->id, 
					    	$this->river->id, 0, $page, $max_id, NULL, $filters, $photos == 1);
					}
					$droplets = $droplets_array['droplets'];
				}				
				

				//Throw a 404 if a non existent page/drop is requested
				if (($page > 1 OR $drop_id) AND empty($droplets))
				{
				    throw new HTTP_Exception_404('The requested page was not found on this server.');
				}

				echo @json_encode($droplets);

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
				$droplet_orm->update_from_array($droplet_array, $this->user->id);
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
				
				ORM::factory('River', $this->river->id)->remove('droplets', $droplet_orm);
			break;
		}
	}


	
	/**
	 * River collaborators restful api
	 * 
	 * @return	void
	 */
	public function action_collaborators()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// No anonymous here
		if ($this->anonymous)
		{
			throw new HTTP_Exception_403();
		}
		
		$query = $this->request->query('q') ? $this->request->query('q') : NULL;
		
		if ($query)
		{
			echo json_encode(Model_User::get_like($query, array($this->user->id, $this->river->account->user->id)));
			return;
		}
		
		switch ($this->request->method())
		{
			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
							
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('User', $user_id);
				
				if ( ! $user_orm->loaded()) 
					return;
					
				$collaborator_orm = $this->river->river_collaborators->where('user_id', '=', $user_orm->id)->find();
				if ($collaborator_orm->loaded())
				{
					$collaborator_orm->delete();
					Model_User_Action::delete_invite($this->user->id, 'river', $this->river->id, $user_orm->id);
				}
			break;
			
			case "PUT":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
			
				$user_id = intval($this->request->param('id', 0));
				$user_orm = ORM::factory('User', $user_id);
				
				$collaborator_array = json_decode($this->request->body(), TRUE);
				
				$collaborator_orm = ORM::factory("River_Collaborator")
									->where('river_id', '=', $this->river->id)
									->where('user_id', '=', $user_orm->id)
									->find();
				
				if ( ! $collaborator_orm->loaded())
				{
					$collaborator_orm->river = $this->river;
					$collaborator_orm->user = $user_orm;
					Model_User_Action::create_action($this->user->id, 'river', $this->river->id, $user_orm->id);
				}
				
				if (isset($collaborator_array['read_only']))
				{
					$collaborator_orm->read_only = (bool) $collaborator_array['read_only'];
				}
				
				$collaborator_orm->save();
			break;
		}
	}
	
	
	/**
	 * River management
	 */
	public function action_manage()
	{
		$this->template = "";
		$this->auto_render = FALSE;
				
		switch ($this->request->method())
		{
			case "PUT":
				// No anonymous
				if ($this->anonymous)
				{
					throw new HTTP_Exception_403();
				}
				$river_array = json_decode($this->request->body(), TRUE);
				$river_orm = ORM::factory('River', $river_array['id']);
				
				if ( ! $river_orm->loaded())
				{
					throw new HTTP_Exception_404();
				}

				if ( ! $river_array['subscribed'])
				{
					// Unsubscribe					
					if ($this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->remove('river_subscriptions', $river_orm);
					}
					
					// Stop collaborating
					$collaborator_orm = $river_orm->river_collaborators
					                        ->where('user_id', '=', $this->user->id)
					                        ->where('collaborator_active', '=', 1)
					                        ->find();
					if ($collaborator_orm->loaded())
					{
						$collaborator_orm->delete();
						$river_array['is_owner'] = FALSE;
						$river_array['collaborator'] = FALSE;
					}

					Cache::instance()->delete('user_rivers_'.$this->user->id);
				}
				else
				{
					// Subscribing				
					if ( ! $this->user->has('river_subscriptions', $river_orm))
					{
						$this->user->add('river_subscriptions', $river_orm);
					}

					Cache::instance()->delete('user_rivers_'.$this->user->id);
				}
				// Return updated bucket
				echo json_encode($river_array);
			break;

			case "DELETE":
				$river_id = intval($this->request->param('id', 0));
				$river_orm = ORM::factory('River', $river_id);
				
				if ($river_orm->loaded())
				{
					if ( ! $river_orm->is_creator($this->user->id))
					{
						// Only creator can delete
						throw new HTTP_Exception_403();
					}
					
					$river_orm->delete();
					Cache::instance()->delete('user_rivers_'.$this->user->id);
				}
				else
				{
					throw new HTTP_Exception_404();
				}
			break;
		}
		
		// Force refresh of cached rivers
		if (in_array($this->request->method(), array('DELETE', 'PUT', 'POST')))
		{
			Cache::instance()->delete('user_rivers_'.$this->user->id);
		}
	}
	
	
	/**
	 * Return filter parameters as a hash array
	 */
	private function _get_filters()
	{
		$filters = array();
		$parameters = array('tags', 'places', 'channel', 'start_date', 'end_date');
		
		foreach ($parameters as $parameter)
		{
			$values = $this->request->query($parameter);
			if ($values)
			{
				$filters[$parameter] = array();				
				// Parameters are array strings that are comma delimited
				// The below converts them into a php array, trimming each
				// value
				foreach (explode(',', urldecode($values)) as $value)
				{
					$filters[$parameter][] = strtolower(trim($value));
				}
			}
		}
		
		return $filters;
	}
	
	/**
	 * @return	void
	 */
	public function action_trends()
	{
		$this->droplets_view = View::factory('pages/river/trend');
		$this->droplets_view->river_base_url = $this->river_base_url;
		$this->active = 'trends';
		
		$this->droplets_view->trends = array();
		$cur_date = new DateTime(null, new DateTimeZone('UTC'));
		$tag_types =  array('person' => 'People', 
							'place' => 'Places', 
							'organization' => 'Organizations');
		$periods = array('hour' => 'This Hour',
		 				 'day' => 'Today',
						 'week' => 'This Week', 
						 'month' => 'This Month',
						 'all' => 'All Time');
		foreach($tag_types as $type_key => $type_title)
		{
			$has_data = FALSE;
			foreach($periods as $period_key => $period_value)
			{
				$start_time = $this->get_period_start_time($period_key, $cur_date);
				$data = Model_River_Tag_Trend::get_trend($this->river->id, $start_time, $type_key);
				foreach ($data as & $trend)
				{
					$trend['url'] = $this->river_base_url.
									'?'.($type_key == 'place' ? 'places' : 'tags').'='.
									urlencode($trend['tag']);
					if ($start_time)
					{
						$trend['url'] .= '&start_date='.urlencode($start_time);
					}
				}
				$this->droplets_view->trends[$type_title]['data'][$period_value] = $data;
				if (! empty($data))
				{
					$has_data = TRUE;
				}
			}
			$this->droplets_view->trends[$type_title]['has_data'] = $has_data;
		}
	}
	
	/**
	 * Given a current time, return the star date for the requested period.
	 *
	 * @param string $period 'hour', 'day', 'week', 'month' or all
	 * @param DateTime $cur_date current date
	 * @return string
	 */
	private function get_period_start_time($period, $cur_date)
	{
		switch ($period)
		{
			case 'hour':
				return date_format($cur_date, 'Y-m-d H:00:00');
				break;
			case 'day':
				return date_format($cur_date, 'Y-m-d 00:00:00');
				break;
			case 'week':
				$ts = date_timestamp_get($cur_date);
				$start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
				return date('Y-m-d', $start);
				break;
			case 'month':
				return date_format($cur_date, 'Y-m-01 00:00:00');
				break;				
			case 'all':
				return NULL;
				break;
		}
	}

	/**
	 * Endpoint for extending the lifetime of the river
	 * If the current user is not an owner of the river, they are redirected
	 * the the river's main page
	 */
	public function action_extend()
	{
		if ( ! $this->owner || ! $this->river->is_expired($this->owner))
		{
			$this->redirect($this->river_base_url, 302);
		}

		$this->auto_render = FALSE;
		$this->template = "";
		$this->river->extend_lifetime();
		$this->redirect($this->river_base_url, 302);
	}
}