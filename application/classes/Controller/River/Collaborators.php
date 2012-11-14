<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * River Collaborator Settings Controller
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
class Controller_River_Collaborators extends Controller_River_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->river->river_name.' ~ '.__('Collaborator Settings');
		$this->template->header->js .= HTML::script("themes/default/media/js/collaborators.js");
		
		$this->active = 'collaborators';
		$this->settings_content = View::factory('template/collaborators');
		$this->settings_content->fetch_url = $this->river_base_url.'/collaborators';
		$this->settings_content->collaborator_list = json_encode($this->river->get_collaborators());;
	}
	
}
