<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Sources Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Sources extends Controller_Swiftriver {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * List all the Sources
	 *
	 * @param   string $page - page uri
	 * @return  void
	 */
	public function action_index($page = NULL)
	{
		$this->template->header->page_title = __('Sources');
		$this->template->content = View::factory('pages/sources');
	}
}
