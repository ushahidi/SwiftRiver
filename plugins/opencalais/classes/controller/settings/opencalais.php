<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Open Calais Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Opencalais extends Controller_Settings_Main {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		$this->template->header->tab_menu->active = 'opencalais';
	}
	
	/**
	 * List all the available settings
	 *
	 * @param   string $page - page uri
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('opencalais/settings')
			->bind('post', $post)
			->bind('errors', $errors);
		
		// save the data
		if ($_POST)
		{
			$settings = ORM::factory('opencalais_setting');
			$post = $settings->validate($_POST);
			if ($post->check())
			{
				$settings = ORM::factory('opencalais_setting')
					->where('key', '=', 'service_key')
					->find();
				$settings->key = 'service_key';
				$settings->value = $post['service_key'];
				$settings->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('settings/opencalais');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('settings');
			}
		}
		else
		{
			$settings = ORM::factory('opencalais_setting')->find_all();
			foreach ($settings as $setting)
			{
				$post[$setting->key] = $setting->value;
			}
		}
	}
}