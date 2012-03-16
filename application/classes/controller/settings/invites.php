<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Invites Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Invites extends Controller_Settings_Main {
	
	
	/**
	 * List all the Plugins
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = __('Invites');
		$this->settings_content = View::factory('pages/settings/invites');
		$this->active = 'invites';			
	}
}
