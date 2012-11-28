<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Swiftriver Base controller
*
* PHP version 5
* LICENSE: This source file is subject to the AGPL license 
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/licenses/agpl.html
* @author     Ushahidi Team <team@ushahidi.com> 
* @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
* @category   Controllers
* @copyright  Ushahidi - http://www.ushahidi.com
* @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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