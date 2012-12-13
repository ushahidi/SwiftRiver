<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Drop Controller
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
class Controller_Drop extends Controller_Swiftriver {
	
	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 */
	public $auto_render = FALSE;
	
	 /**
	  * Single drop restful api
	  */ 
	 public function action_index()
	 {
		$this->template = "";
		
		if ( ! $this->admin)
		{
			throw new HTTP_Exception_403();
		}

		switch ($this->request->method())
		{
			case "POST":
				// Get the POST data
				$drops = json_decode($this->request->body(), TRUE);
				Kohana::$log->add(Log::DEBUG, ":count Drops received", array(':count' => count($drops)));
				Model_Droplet::create_from_array($drops);
			break;
		}
	 }
	 
	 
	 /**
	  * Comments
	  */ 
	 public function action_comment()
	 {
 		$this->template = "";
		
 		if ( ! $this->admin)
 			throw new HTTP_Exception_403();
		
		if ($this->request->method() != "POST")
			throw HTTP_Exception::factory(405)->allowed('POST');

 		// Get the POST data
 		$data = json_decode($this->request->body(), TRUE);
		$user = Model_User::get_user_by_email($data['from_email']);
		$drop_id = intval($this->request->param('id', 0));
		
		if ( ! $user->loaded())
			throw HTTP_Exception::factory(404);		
		
		$comment = Model_Droplet_Comment::create_new(
			$data['comment_text'],
			$drop_id,
			$user->id
		);
		
		$context_obj = ORM::factory(ucfirst($data['context']), $data['context_obj_id']);
		if ($context_obj->loaded())
		{
			Swiftriver_Mail::notify_new_drop_comment($comment, $context_obj);
		}
		
 		Kohana::$log->add(Log::DEBUG, "New comment for drop id :id from :email", 
			array(
 				':id' => $drop_id, 
				':email' => $data['from_email']
 			)
		);
	 }
}