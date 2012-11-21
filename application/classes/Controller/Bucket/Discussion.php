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
					$this->notify_new_comment($comment);
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
	
	/**
	 * Notify bucket owners and followers of a new comment
	 * 
	 * @return	void
	 */
	private function notify_new_comment($comment)
	{
		$html = View::factory('emails/html/comment');
		$text = View::factory('emails/text/comment');
		$html->is_drop = $text->is_drop = FALSE;
		$html->from_name = $text->from_name = $this->user->name;
		$html->avatar = Swiftriver_Users::gravatar($this->user->email, 80);
		$html->from_link = URL::site($this->user->account->account_path, TRUE);
		$html->asset = $text->asset = 'bucket';
		$html->asset_name = $text->asset_name = $this->bucket->bucket_name;
		$html->asset_link = $text->asset_link = URL::site($this->bucket->get_base_url(), TRUE);
		$html->link = $text->link = URL::site($this->bucket->get_base_url().'/discussion#comment-'.$comment->id, TRUE);
		$text->comment = $comment->comment_content;
		$html->comment = Markdown::instance()->transform($comment->comment_content);
		$subject = __(':from commented on the ":name" bucket.',
						array( ":from" => $this->user->name,
						":name" => $this->bucket->bucket_name
						));
		
		// Add owner of the bucket first 
		$emails = Array($this->bucket->user->email);		
		
		// Then collaborators
		foreach ($this->bucket->get_collaborators(TRUE) as $collaborator)
		{
			$emails[] = $collaborator['email'];
		}
		
		// Then followers
		foreach ($this->bucket->subscriptions->find_all() as $follower)
		{
			$emails[] = $follower->email;
		}
		
		$text_body = $text->render();
		$html_body = $html->render();
		foreach ($emails as $email)
		{
			if ($email != $this->user->email) 
			{
				SwiftRiver_Mail::send($email, $subject, $text_body, $html_body);
			}
		}
	}
	
}