<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Settings Controller
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
class Controller_Bucket_Settings extends Controller_Bucket {
	
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
		
		$this->template->content = View::factory('pages/bucket/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content)
			->bind('bucket_base_url', $this->bucket_base_url)
			->bind('bucket', $this->bucket);
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		// Default view is collaborator settings
		$this->request->redirect($this->bucket_base_url.'/settings/collaborators');
	}
	
}