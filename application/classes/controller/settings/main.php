<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main Settings Controller
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
class Controller_Settings_Main extends Controller_Swiftriver {
	
	/**
	 * Access privileges for this controller and its children
	 */
	public $auth_required = 'admin';
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->page_title = __('Settings');
		$this->template->header->tab_menu = View::factory('pages/settings/menu');
		$this->template->header->tab_menu->active = "";
		$this->template->header->tab_menu->plugin_settings = $this->_plugin_settings();
	}
	
	/**
	 * List all the available settings
	 *
	 * @return  void
	 */
	public function action_index()
	{
		
	}
	
	/**
	 * Find out which plugins have settings
	 *
	 * @return  array $menu - array of plugins that have settings
	 */
	private function _plugin_settings()
	{
		$menu = array();
		
		$plugin_configs = Kohana::$config->load('plugin');
		foreach ($plugin_configs as $key => $config)
		{
			if ( is_array($config) 
				AND isset($config['settings']) 
				AND $config['settings'] == TRUE )
			{
				$menu[] = $key;
			}
		}
		
		return $menu;
	}
}