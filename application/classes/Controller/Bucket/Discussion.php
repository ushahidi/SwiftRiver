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
			->bind('settings_url', $settings_url)
			->bind('fetch_url', $fetch_url)
			->bind('bucket_url', $this->bucket_base_url)
			->bind('page_title', $this->page_title)
			->bind('owner', $this->owner)
			->bind('user', $this->user)
			->bind('user_avatar', $user_avatar)
			->bind('anonymous', $this->anonymous);
		$this->template->content->collaborators = $this->bucket->get_collaborators(TRUE);
		$comments = $this->bucket->get_comments($this->user->id);
		foreach ($comments as &$comment)
		{
			$comment['comment_content'] = Markdown::instance()->transform($comment['comment_content']);
		}
		$this->template->content->comments = json_encode($comments);

		$user_avatar = Swiftriver_Users::gravatar($this->user->email, 80);

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
				$comment = ORM::factory('Bucket_Comment');
				$valid = $comment->validate($post);
				if ($valid->check())
  				{
  					$comment->bucket_id = $this->bucket->id;
					$comment->user_id = $this->user->id;
					$comment->comment_content = $post['comment_content'];
					$comment->comment_date_add = gmdate("Y-m-d H:i:s", time());
					$comment->save();
					Swiftriver_Mail::notify_new_bucket_comment($comment, $this->bucket);
					echo json_encode(array(
						'id' => $comment->id, 
						'name' => $comment->user->name,
						'user_id' => $comment->user->id,
						'comment_content' => Markdown::instance()->transform($comment->comment_content),
						'date' => $comment->comment_date_add,
						'avatar' => Swiftriver_Users::gravatar($comment->user->email, 40),
						'score' => 0
					));
				}
  				else
  				{
  					throw new HTTP_Exception_400();
  				}

			break;

			case "PUT":
				$post = json_decode($this->request->body(), TRUE);
				$comment_id = intval($this->request->param('id', 0));
				$score = ( isset($post['score']) AND in_array($post['score'], array(1, -1) ) )
					 ? $post['score'] : 0;
				$comment = ORM::factory('Bucket_Comment', $comment_id);
				
				if ( ! $comment->loaded())
				{
					throw new HTTP_Exception_404();
				}
				
				if ($comment->user_id == $this->user->id)
				{
					// User can't vote on their own comments
					throw new HTTP_Exception_400();
				}

				$comment_score = ORM::factory('Bucket_Comment_Score')
					->where('bucket_comment_id', '=', $comment->id)
					->where('user_id', '=', $this->user->id)
					->find();
				$comment_score->bucket_comment_id = $comment->id;
				$comment_score->user_id = $this->user->id;
				$comment_score->score = $score;
				$comment_score->save();		
			break;
		}
	}
}