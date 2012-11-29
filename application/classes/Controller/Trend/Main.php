<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Trend Controller
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
class Controller_Trend_Main extends Controller_Swiftriver {

	// The Droplets Array
	protected $droplets = array(
		'total' => 0,
		'droplets' => array()
		);
	
	private $more_url = '#';

	// Active Menu
	public static $active = 'droplets';

	protected $trend;
	
	protected $context;
	
	protected $bucket;
	
	protected $river;

	protected $id;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Get the river/bucket name from the url
		$name_url = $this->request->param('name', 0);

		$this->context = $this->request->param('context');

		switch ($this->context)
		{
			case "river":
				// Make sure River exists
				$this->river = ORM::factory('River')
					->where('river_name_url', '=', $name_url)
					->where('account_id', '=', $this->visited_account->id)
					->find();
				if ( ! $this->river->loaded())
				{
					// It doesn't -- redirect back to dashboard
					$this->redirect($this->dashboard_url, 302);
				}

				// Is the logged in user an owner
				if ($this->river->is_owner($this->user->id)) 
				{
					$this->owner = TRUE;
				}
				
				// If this river is not public and no ownership...
				if( ! $this->river->river_public AND ! $this->owner)
				{
					$this->redirect($this->dashboard_url, 302);
				}
				
				$this->id = $this->river->id;
				$this->droplets = Model_River::get_droplets($this->user->id, $this->river->id, 0);
				
				// Default template for river content
				$this->template->content = View::factory('pages/river/layout')
					->bind('river', $this->river)
					->bind('droplets_view', $droplets_view)
					->bind('river_base_url', $this->river_base_url)
					->bind('settings_url', $this->settings_url)
					->bind('owner', $this->owner)
					->bind('user', $this->user)
					->bind('river_base_url', $this->river_base_url)
					->bind('nav', $this->nav);

				// Trend template
				$droplets_view = View::factory('pages/trend/river')
					->bind('trend', $this->trend);

				// Set the base url for this specific river
				$this->river_base_url = $this->river->get_base_url();

				// Settings url
				$this->settings_url = $this->river_base_url.'/settings';
				
				// Navigation Items
				$this->nav = Swiftriver_Navs::river($this->river);				
				break;
							
			case "bucket":
				// Make sure Bucket exists
				$this->bucket = ORM::factory('Bucket')
					->where('bucket_name_url', '=', $name_url)
					->where('account_id', '=', $this->visited_account->id)
					->find();
				if ( ! $this->bucket->loaded())
				{
					// It doesn't -- redirect back to dashboard
					$this->redirect($this->dashboard_url, 302);
				}
				
				$this->id = $this->bucket->id;
				$this->droplets = Model_Bucket::get_droplets($this->user->id, $this->bucket->id, 0);
				
				// Default template for bucket trends
				$this->template->content = View::factory('pages/trend/bucket')
					->bind('bucket', $this->bucket)
					->bind('droplets', $this->droplets)
					->bind('more_url', $this->more_url)
					->bind('drops_url', $drops_url)
					->bind('active', self::$active)
					->bind('trend', $this->trend);

				// Set the base url for this specific bucket
				$this->bucket_base_url = $this->bucket->get_base_url();					
				
				$drops_url = URL::site().$this->bucket->account->account_path.'/bucket/'.$this->bucket->bucket_name_url;
				$this->more_url = url::site().$this->account->account_path.'/bucket/'.$this->bucket->bucket_name_url.'/more';
				break;	    
		}
	
	}
}