<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Drops Base Controller
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
class Controller_Drop_Base extends Controller_Swiftriver {
	
	 /**
	  * Tags restful api
	  */ 
	 public function action_tags()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$tag_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				$tag_array = json_decode($this->request->body(), true);
				$tag_name = $tag_array['tag'];
				$account_id = $this->visited_account->id;
				$tag_orm = Model_Account_Droplet_Tag::get_tag($tag_name, $droplet_id, $account_id);
				echo json_encode(array('id' => $tag_orm->tag->id, 
										'tag' => $tag_orm->tag->tag, 
										'tag_canonical' => $tag_orm->tag->tag_canonical));
			break;

			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				Model_Droplet::delete_tag($droplet_id, $tag_id, $this->visited_account->id);
			break;
		}
	}
	
	/**
	  * Links restful api
	  */ 
	 public function action_links()
	{
		// Is the logged in user an owner?
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$link_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				$link_array = json_decode($this->request->body(), TRUE);
				$url = $link_array['url'];
				if ( ! Valid::url($url))
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array(__("Invalid url"));
					echo json_encode(array('errors' => $errors));
					return;
				}
				$account_id = $this->visited_account->id;
				$link_orm = Model_Account_Droplet_Link::get_link($url, $droplet_id, $account_id);
				echo json_encode(array('id' => $link_orm->link->id, 'tag' => $link_orm->link->url));
			break;

			case "DELETE":
				Model_Droplet::delete_link($droplet_id, $link_id, $this->visited_account->id);
			break;
		}
	}
	
	/**
	  * Links restful api
	  */ 
	 public function action_places()
	{
		// Is the logged in user an owner?
		if ( ! $this->owner)
		{
			throw new HTTP_Exception_403();
		}
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		$place_id = intval($this->request->param('id2', 0));
		
		switch ($this->request->method())
		{
			case "POST":
				$places_array = json_decode($this->request->body(), true);
				$place_name = $places_array['place_name'];
				if ( ! Valid::not_empty($place_name))
				{
					$this->response->status(400);
					$this->response->headers('Content-Type', 'application/json');
					$errors = array(__("Invalid location"));
					echo json_encode(array('errors' => $errors));
					return;
				}
				$account_id = $this->visited_account->id;
				$place_orm = Model_Account_Droplet_Place::get_place($place_name, $droplet_id, $account_id);
				echo json_encode(array(
					'id' => $place_orm->place->id, 
					'place_name' => $place_orm->place->place_name,
					'place_name_canonical' => $place_orm->place->place_name_canonical));
			break;

			case "DELETE":
				Model_Droplet::delete_place($droplet_id, $place_id, $this->visited_account->id);
			break;
		}
	}
	
	 /**
	  * Replies restful api
	  */ 
	public function action_reply()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		
		switch ($this->request->method())
		{
			case "GET":
				$params = $this->request->query();
				
				if (isset($params['since_id']))
				{
					$since_id = intval($this->request->query('since_id')); 
					$comments = Model_Droplet::get_comments($droplet_id, $since_id, TRUE);
				}
				else
				{
					$last_id = $this->request->query('last_id') ? intval($this->request->query('last_id')) : PHP_INT_MAX;
					$comments = Model_Droplet::get_comments($droplet_id, $last_id);
					
					if (empty($comments))
					{
					    throw new HTTP_Exception_404('The requested page was not found on this server.');
					}
				}
				
				foreach ($comments as &$comment)
				{
					$comment['comment_text'] = Markdown::instance()->transform($comment['comment_text']);
				}
				echo json_encode($comments);
			break;
			case "POST":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				// Get the POST data
				$body = json_decode($this->request->body(), TRUE);
				
				$comment = ORM::factory('Droplet_Comment');
				$comment->comment_text = $body['comment_text'];
				$comment->droplet_id = intval($this->request->param('id', 0));
				$comment->user_id = $this->user->id;
				$comment->save();
								
				if ( ! $comment->loaded()) 
					throw new HTTP_Exception_400();
				
				$this->notify_new_comment($comment);
				
				echo json_encode(array(
					'id' => $comment->id,
					'droplet_id' => $comment->droplet_id,
					'comment_text' => Markdown::instance()->transform($comment->comment_text),
					'identity_user_id' => $this->user->id,
					'identity_name' => $this->user->name,
					'identity_avatar' => Swiftriver_Users::gravatar($this->user->email, 80),
					'deleted' => FALSE,
					'date_added' => date_format(date_create($comment->date_added), 'M d, Y H:i').' UTC'
				));
			break;
			case "PUT":
				$comment_id = intval($this->request->param('id2', 0));
				$comment = ORM::factory('Droplet_Comment', $comment_id);
				
				// Does the comment exist?
				if ( ! $comment->loaded())
					throw new HTTP_Exception_404();
					
				// Is owner of the comment logged in?
				if ($comment->user->id != $this->user->id)
					throw new HTTP_Exception_403();
				
				$comment->deleted = TRUE;
				$comment->save();
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
		$html->is_drop = $text->is_drop = TRUE;
		$html->from_name = $text->from_name = $this->user->name;
		$html->avatar = Swiftriver_Users::gravatar($this->user->email, 80);
		$html->from_link = URL::site($this->user->account->account_path, TRUE);
		$text->comment = $comment->comment_text;
		$html->comment = Markdown::instance()->transform($comment->comment_text);
		$subject = "";
		$asset = NULL;
		
		if ($this instanceof Controller_River)
		{
			$asset = $this->river;
			$html->asset = $text->asset = "river";
			$html->asset_name = $text->asset_name = $this->river->river_name;
			$html->asset_link = $text->asset_link = URL::site($this->river->get_base_url(), TRUE);
			$html->link = $text->link = URL::site($this->river->get_base_url().'/drop/'.$comment->droplet_id, TRUE);
			$subject = __(':from commented on a drop in the ":asset_name" river.',
						  array( ":from" => $this->user->name,
						         ":asset_name" => $this->river->river_name
						  ));
		} 
		else
		{
			$asset = $this->bucket;
			$html->asset = $text->asset = "bucket";
			$html->asset_name = $text->asset_name = $this->bucket->bucket_name;
			$html->asset_link = $text->asset_link = URL::site($this->bucket->get_base_url(), TRUE);
			$html->link = $text->link = URL::site($this->bucket->get_base_url().'/drop/'.$comment->droplet_id, TRUE);
			$subject = __(':from commented on a drop in the ":asset_name" bucket.',
						  array( ":from" => $this->user->name,
						         ":asset_name" => $this->bucket->bucket_name
						  ));
		}
		
		// Add owner of the bucket first 
		$emails = Array($asset->account->user->email);		
		
		// Then collaborators
		foreach ($asset->get_collaborators(TRUE) as $collaborator)
		{
			$emails[] = $collaborator['email'];
		}
		
		// Then followers
		foreach ($asset->subscriptions->find_all() as $follower)
		{
			$emails[] = $follower->email;
		}
		
		$text_body = $text->render();
		$html_body = $html->render();
		foreach ($emails as $email)
		{
			if ($email != $this->user->email) 
			{
				Kohana::$log->add(Log::DEBUG, "Sending email to :email", array(':email' => $email));
				SwiftRiver_Mail::send($email, $subject, $text_body, $html_body);
			}
		}
	}
	
	/**
	 * REST endpoint for sharing droplets via email
	 */
	public function action_share()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		if ($_POST)
		{
			// Extract the input data to be used for sending the email
			$post = Arr::extract($_POST, array('recipient', 
				'drop_title', 'drop_url', 'security_code'));
			
			$csrf_token = $this->request->headers('x-csrf-token');

			// Setup validation
			$validation = Validation::factory($post)
			    ->rule('recipient', 'not_empty')
			    ->rule('recipient', 'email')
			    ->rule('security_code', 'Captcha::valid')
			    ->rule('drop_title', 'not_empty')
			    ->rule('drop_url', 'url');

			// Validate
			if ( ! CSRF::valid($csrf_token) OR ! $validation->check())
			{
				Kohana::$log->add(Log::DEBUG, "CSRF token or form validation failure");
				$this->response->status(400);
			}
			else
			{
				list($recipient, $subject) = array($post['recipient'], $post['drop_title']);

				// Modify the mail body to include the email address of the
				// use sharing content
				$mail_body = __(":user has shared a drop with you via SwiftRiver\n\n:url",
				    array(':user' => $this->user->username, ':url' => $post['drop_url']));

				// Send the email
				Swiftriver_Mail::send($recipient, $subject, $mail_body);
			}
		}
		else
		{
			throw new HTTP_Exception_405("Only HTTP POST requests are allowed");
		}
	}
}