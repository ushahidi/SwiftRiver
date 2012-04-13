<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Bucket_Discussion extends Controller_Bucket {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();

		$this->template->header->title = $this->page_title.' | '.__('Discussion');
	}
	
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->page_title;
		$this->template->content = View::factory('pages/bucket/discussion')
			->bind('settings_url', $settings_url)
			->bind('fetch_url', $fetch_url)
			->bind('bucket_url', $this->bucket_base_url)
			->bind('page_title', $this->page_title)
			->bind('owner', $this->owner)
			->bind('user', $this->user);
		$this->template->content->collaborators = $this->bucket->get_collaborators();

		// Links to ajax rendered menus
		$settings_url = $this->bucket_base_url.'/settings';
		$fetch_url = $this->bucket_base_url.'/discussion/comments';
	}

	/**
	 * Discussion comments restful api
	 * 
	 * @return	void
	 */
	public function action_comments()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		switch ($this->request->method())
		{			
			case "POST":

				$post = json_decode($this->request->body(), TRUE);
				$comment = ORM::factory('comment');
				$valid = $comment->validate($post);
				if ($valid->check())
  				{
  					$comment->bucket_id = $this->bucket->id;
					$comment->user_id = $this->user->id;
					$comment->comment_content = $post['comment_content'];
					$comment->comment_date_add = date("Y-m-d H:i:s", time());
					$comment->save();
				}
  				else
  				{
  					throw new HTTP_Exception_400();
  				}

			break;

			default:
				echo json_encode($this->bucket->get_comments());
			break;
		}
	}	
	
}