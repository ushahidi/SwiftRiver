<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Swiftriver Base controller
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
class Controller_Error_Handler extends Controller_Swiftriver {

	public function before()
	{
		parent::before();
		
		$this->template->content = View::factory('pages/error');
		$this->template->content->page = URL::site(rawurldecode(Request::$initial->uri()));

		// Internal request only!
		if (Request::$initial !== Request::$current)
		{
			if ($message = rawurldecode($this->request->param('message')))
			{
				$this->template->content->message = $message;
			}
		}
		else
		{
			$this->request->action(404);
		}

		$this->response->status((int) $this->request->action());
	}

	public function action_404()
	{
		$this->template->header->title = '404 Not Found';
	}

	public function action_503()
	{
		$this->template->header->title = 'Maintenance Mode';
	}
    
	public function action_500()
	{
		$this->template->header->title = 'Internal Server Error';
	}
}