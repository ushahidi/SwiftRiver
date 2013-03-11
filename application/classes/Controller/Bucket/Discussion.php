<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Settings Controller
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
			->bind('bucket', $this->bucket)
			->bind('bucket_base_url', $this->bucket_base_url)
			->bind('settings_url', $settings_url)
			->bind('fetch_url', $fetch_url)
			->bind('owner', $this->owner)
			->bind('user', $this->user)
			->bind('anonymous', $this->anonymous)
			->bind('comments', $comments);

		$comments = json_encode($this->bucket_service->get_bucket_comments($this->bucket['id']));
		
		$this->template->content->set('nav', $this->get_discussion_nav())
			->set('active', 'all');

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
		
		if ($this->anonymous)
			throw new HTTP_Exception_403();
		
		switch ($this->request->method())
		{			
			case "POST":
				$post = json_decode($this->request->body(), TRUE);
				$comment_text = $post['comment_text'];
				
				$comment = $this->bucket_service->add_bucket_comment($this->bucket['id'], $comment_text);
				
				echo json_encode($comment);
			break;

			case "DELETE":
				$comment_id = $this->request->param('id', 0);
				$this->bucket_service->delete_bucket_comment($this->bucket['id'], $comment_id);
			break;
		}
	}
	
	/**
	 * Gets the nagivation items for the discussions view
	 */
	private function get_discussion_nav()
	{
		return array(
			array(
				'id' => 'all-comments-link',
				'url'=> '/discussion#all',
				'active' => 'all',
				'label' => __('All comments')
			),
			array(
				'id' => 'my-comments-link',
				'url' => '/discussion#mine',
				'active' => 'mine',
				'label' => __("Yours")
			)
		);
	}
}