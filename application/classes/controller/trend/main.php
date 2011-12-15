<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Trend Controller
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
class Controller_Trend_Main extends Controller_Swiftriver {

	// The Droplets Array
	private $droplets = array(
		'total' => 0,
		'droplets' => array()
		);
	
	private $more_url = '#';

	// Active Menu
	public static $active = 'droplets';

	private $trend;

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		$id = (int) $this->request->param('id', 0);

		// Is this a River or a Bucket?
		if ( strpos($this->request->uri(), 'river/') !== FALSE)
		{
			// Make sure River exists
			$river = ORM::factory('river')
				->where('id', '=', $id)
				->where('account_id', '=', $this->account->id)
				->find();
			if ( ! $river->loaded())
			{
				// It doesn't -- redirect back to dashboard
				$this->request->redirect('dashboard');
			}			

			$this->droplets = Model_Droplet::get_river($river->id);

			// Default template for river trends
			$this->template->content = View::factory('pages/trend/river')
				->bind('river', $river)
				->bind('droplets', $this->droplets)
				->bind('more_url', $this->more_url)
				->bind('active', self::$active)
				->bind('trend', $this->trend);

			$this->more_url = url::site().$this->account->account_path.'/river/more/'.$river->id;
		}

		if ( strpos($this->request->uri(), 'bucket/') !== FALSE)
		{
			// Make sure Bucket exists
			$bucket = ORM::factory('bucket')
				->where('id', '=', $id)
				->where('account_id', '=', $this->account->id)
				->find();
			if ( ! $bucket->loaded())
			{
				// It doesn't -- redirect back to dashboard
				$this->request->redirect('dashboard');
			}

			$this->droplets = Model_Droplet::get_bucket($bucket->id);
			
			// Default template for bucket trends
			$this->template->content = View::factory('pages/trend/river')
				->bind('bucket', $bucket)
				->bind('droplets', $this->droplets)
				->bind('more_url', $this->more_url)
				->bind('active', self::$active)
				->bind('trend', $this->trend);

			$this->more_url = url::site().$this->account->account_path.'/bucket/more/'.$river->id;
		}		
	}
}