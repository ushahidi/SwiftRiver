<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Plugins Controller
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
class Controller_Settings_Plugins extends Controller_Settings_Main {
	
	
	/**
	 * List all the Plugins
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = __('Plugins');
		$this->settings_content = View::factory('pages/settings/plugins')
		    ->bind('fetch_url', $fetch_url)
		    ->bind('plugins_list', $plugins_list);

		$this->active = 'plugins';
		
		// Process Plugins
		$this->_process_plugins();
		
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'plugin_name'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$plugins = ORM::factory('plugin')
			->order_by($sort, $dir)
			->find_all();

		$entries =  array();
		foreach ($plugins as $plugin)
		{
			$entries[] = array(
				'id' => $plugin->id, 
				'plugin_name' => $plugin->plugin_name, 
				'plugin_description' => $plugin->plugin_description,
				'plugin_enabled' => ($plugin->plugin_enabled == 1),
				'plugin_path' => $plugin->plugin_path, 
				'plugin_settings' => Swiftriver_Plugins::has_settings($plugin->plugin_path)
			);
		}
		$plugins_list = json_encode($entries);
		$fetch_url = URL::site().'settings/plugins/manage';
		unset ($entries);

	}

	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			// Update the plugin
			case "PUT":
				$plugin_id = $this->request->param('id');
				$item_array = json_decode($this->request->body(), TRUE);
				$plugin_orm = ORM::factory('plugin', $plugin_id);

				if ( ! $plugin_orm->loaded())
				{
					throw new HTTP_Exception_404("The requested plugin could not be found");
				}

				$plugin_orm->plugin_enabled = $item_array['plugin_enabled'];
				$plugin_orm->save();

				// Run the plugin installer script if it hasn't been run before
				if ($plugin_orm->plugin_enabled AND $plugin_orm->plugin_installed != 1)
				{
					if ( Swiftriver_Plugins::install($plugin_orm->plugin_path) )
					{
						$plugin_orm->plugin_installed = 1;
						$plugin_orm->save();
					}
				}

			break;
		}
	}
	
	/**
	 * Private function to go through plugin directory and extract
	 * plugins in the system, then save them in the database
	 * so that they're available for activation in admin
	 *
	 * @return	void
	 */
	private function _process_plugins()
	{
		$configs = Swiftriver_Plugins::load_configs();
		
		// Sync the folder with the database
		foreach ($configs as $key => $value)
		{
			if ( ORM::factory('plugin')
				->where('plugin_path', '=', $key)
				->count_all() == 0 )
			{
				$plugin = ORM::factory('plugin');
				$plugin->plugin_path = $key;
				$plugin->plugin_name = $value['name'];
				$plugin->plugin_description = $value['description'];
				$plugin->save();
			}
		}
		
		// Remove Any Plugins not found in the plugins folder from the database
		foreach (ORM::factory('plugin')->find_all() as $plugin)
		{
			if ( ! array_key_exists($plugin->plugin_path, $configs))
			{
				$plugin->delete();
			}
		}
	}
}
