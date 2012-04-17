<?php defined('SYSPATH') or die('No direct script access');

/**
 * Welcome controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Welcome extends Controller_Swiftriver {
	
	/**
	 * Whether to automatically render the view
	 * @var bool
	 */
	public $auto_render = TRUE;
	
	
	public function before()
	{
		parent::before();
		
		// TODO
	}
	
	public function action_index()
	{
		$this->template->header->css = Html::style("themes/default/media/css/home.css");
		$this->template->header->title = __('Welcome');
		$this->template->content = View::factory('pages/welcome/main');
		$this->template->content->set(array(
			'public_registration_enabled' => (bool) Model_Setting::get_setting('public_registration_enabled'),
			'anonymous' => $this->anonymous
		));
	}
}
?>
