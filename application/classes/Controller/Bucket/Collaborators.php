<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Collaborator Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftBucket - https://github.com/ushahidi/SwiftBucket
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Controller_Bucket_Collaborators extends Controller_Bucket_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->bucket['name'].' ~ '.__('Collaborator Settings');
		$this->template->header->js .= HTML::script("themes/default/media/js/collaborators.js");
		$this->template->content->active = "collaborators";
		$this->template->content->settings_content = View::factory('template/collaborators')
							->bind('fetch_url', $fetch_url)
							->bind('collaborator_list', $collaborators);

		$fetch_url = $this->bucket_base_url.'/collaborators';		
		$collaborators = json_encode($this->bucket_service->get_collaborators($this->bucket['id']));
		
	}
	
}
