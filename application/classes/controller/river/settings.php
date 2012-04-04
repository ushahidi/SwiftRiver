<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Settings Controller
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
class Controller_River_Settings extends Controller_River {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		// Only owners allowed here
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template->content = View::factory('pages/river/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content)
			->bind('river_base_url', $this->river_base_url)
			->bind('river', $this->river);
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		// Default view is channel settings
		$this->request->redirect($this->river_base_url.'/settings/channels');
	}
	
}