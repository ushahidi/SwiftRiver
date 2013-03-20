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
class Controller_River_Rules extends Controller_River_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->content->active = "rules";
		$this->template->content->settings_content = View::factory('pages/river/settings/rules')
			->bind('condition_fields', $condition_fields)
			->bind('condition_operators', $condition_operators)
			->bind('rules_js', $rules_js);

		$rules_js = View::factory('pages/river/settings/js/rules')
			->bind('action_url', $action_url)
			->bind('rules', $rules);
		
		$rules = json_encode($this->river_service->get_rules($this->river['id']));
		$action_url = $this->river_base_url.'/settings/rules/manage';

		// Fields for the rule condition
		$condition_fields = array(
			'title' => 'Title',
			'content' => 'Content',
			'source' => 'Source'
		);
		
		// Condition operators
		$condition_operators = array(
			'contains' => 'contains',
			'is' => 'is',
			'does_not_contain' => 'does not contain'
		);
	}
	
	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{
			case "POST":
				$rules_data = json_decode($this->request->body(), TRUE);
				$rule = $this->river_service->add_rule($this->river['id'], $rules_data);
				echo json_encode($rule);
			break;
			
			case "PUT":
				$rule_id = $this->request->param('id', 0);
				$rules_data = json_decode($this->request->body(), TRUE);
				$this->river_service->modify_rule($this->river['id'], $rule_id, $rules_data);
			break;
			
			case "DELETE":
				$rule_id = $this->request->param('id', 0);
				$this->river_service->delete_rule($this->river['id'], $rule_id);
			break;
		}
	}
	
}
