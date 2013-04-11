<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mail Helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Libraries
 * @copyright  (c) 2012 Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_Mail {
		
	/**
	 * Sends an email with from address determined from site configuration
	 *
	 * @param	string	 $email
	 * @param	string	 $subject
	 * @param	string	 $mail_body	 	 
	 * @param   string   $mail_body_html
	 * @return	null
	 */	   
	public static function send($email, $subject, $mail_body, $mail_body_html = NULL, $from = NULL, $extra_headers = array())
	{
		$site_email = Swiftriver_Mail::get_default_address();
		$default_from = '"'.Swiftriver::get_setting('site_name').'" <'.$site_email.'>';
		$from_address = isset($from) ? $from : $default_from;
		
		// Send multipart message
		require_once ("Mail/mime.php");
			
		$mime = new Mail_mime(array('eol' => "\n"));
		$mime->setTXTBody($mail_body);
		
		if ( ! empty($mail_body_html))
		{
			$mime->setHTMLBody($mail_body_html);
		}

		$headers = $mime->headers(array('From' => $from_address));
		$headers = array_merge($headers, $extra_headers);
		$headers_str = '';
		foreach ($headers as $key => $value)
		{
			$headers_str .= $key.': '.$value."\r\n";
		}
		
		$body = $mime->get();
		mail($email, $subject, $body, $headers_str);
	}
	
	/**
	 * Get the default sender address for emails
	 *
	 * @return	string
	 */
	public static function get_default_address()
	{
		return 'noreply@'.self::get_email_domain();
	}
	
	/**
	 * Get the site email domain
	 *
	 * @return	string
	 */
	public static function get_email_domain()
	{
		return Swiftriver::get_setting('email_domain');
	}
	
	/**
	 * Get the comments from email domain
	 *
	 * @return	string
	 */
	public static function get_comments_email_domain()
	{
		return Swiftriver::get_setting('comments_email_domain');
	}
	
	/**
	 * Notify bucket/river owners and followers of a new comment
	 * 
	 * @param Model_Droplet_Comment $comment The comment
	 * @param Object $context_obj River/Bucket object
	 * @return	void
	 */
	public static function notify_new_drop_comment($comment, $context_obj)
	{
		$html = View::factory('emails/html/comment');
		$text = View::factory('emails/text/comment');
		$html->is_drop = $text->is_drop = TRUE;
		$html->from_name = $text->from_name = $comment->user->name;
		$html->avatar = Swiftriver_Users::gravatar($comment->user->email, 80);
		$html->from_link = URL::site($comment->user->account->account_path, TRUE);
		$text->comment = $comment->comment_text;
		$html->comment = Markdown::instance()->transform($comment->comment_text);
		$subject = "";
		$context = "";
		
		if ($context_obj instanceof Model_River)
		{
			$html->asset = $text->asset = $context = "river";
			$html->asset_name = $text->asset_name = $context_obj->river_name;
			$html->asset_link = $text->asset_link = URL::site($context_obj->get_base_url(), TRUE);
			$html->link = $text->link = URL::site($context_obj->get_base_url().
												  '/drop/'.
												  $comment->droplet_id.
												  '#drop-comment-'.
												  $comment->id, TRUE);
			$subject = __(':from commented on a drop in the ":asset_name" river.',
						  array( ":from" => $comment->user->name,
						         ":asset_name" => $context_obj->river_name
						  ));
		} 
		else
		{
			$html->asset = $text->asset = $context = "bucket";
			$html->asset_name = $text->asset_name = $context_obj->bucket_name;
			$html->asset_link = $text->asset_link = URL::site($context_obj->get_base_url(), TRUE);
			$html->link = $text->link = URL::site($context_obj->get_base_url().
												  '/drop/'.
												  $comment->droplet_id.
												  '#drop-comment-'.
												  $comment->id, TRUE);
			$subject = __(':from commented on a drop in the ":asset_name" bucket.',
						  array( ":from" => $comment->user->name,
						         ":asset_name" => $context_obj->bucket_name
						  ));
		}
		
		// Add owner of the bucket first 
		$emails = array($context_obj->account->user->email);		
		
		// Then collaborators
		foreach ($context_obj->get_collaborators(TRUE) as $collaborator)
		{
			$emails[] = $collaborator['email'];
		}
		
		// Then followers
		foreach ($context_obj->subscriptions->find_all() as $follower)
		{
			$emails[] = $follower->email;
		}
		
		$text_body = $text->render();
		$html_body = $html->render();
		$site_email = Swiftriver_Mail::get_default_address();
		$from = '"'.$comment->user->name.'" <notifications@'.Swiftriver_Mail::get_email_domain().'>';
		$token_data = array(
			'drop_id' => $comment->droplet_id, 
			'context' => $context,
			'context_obj_id' => $context_obj->id,
		);
		$token = Model_Auth_Token::create_token('drop-comment', $token_data);
		$reply_to = 'drop-comment-'.$token->token.'@'.Swiftriver_Mail::get_comments_email_domain();
		foreach ($emails as $email)
		{
			if ($email != $comment->user->email) 
			{
				self::send($email, $subject, $text_body, $html_body, $from, array('Reply-To' => $reply_to));
			}
		}
	}
	
	/**
	 * Notify bucket owners and followers of a new comment
	 * 
	 * @return	void
	 */
	public static function notify_new_bucket_comment($comment, $bucket)
	{
		$html = View::factory('emails/html/comment');
		$text = View::factory('emails/text/comment');
		$html->is_drop = $text->is_drop = FALSE;
		$html->from_name = $text->from_name = $comment->user->name;
		$html->avatar = Swiftriver_Users::gravatar($comment->user->email, 80);
		$html->from_link = URL::site($comment->user->account->account_path, TRUE);
		$html->asset = $text->asset = 'bucket';
		$html->asset_name = $text->asset_name = $bucket->bucket_name;
		$html->asset_link = $text->asset_link = URL::site($bucket->get_base_url(), TRUE);
		$html->link = $text->link = URL::site($bucket->get_base_url().'/discussion#comment-'.$comment->id, TRUE);
		$text->comment = $comment->comment_content;
		$html->comment = Markdown::instance()->transform($comment->comment_content);
		$subject = __(':from commented on the ":name" bucket.',
						array( ":from" => $comment->user->name,
						":name" => $bucket->bucket_name
						));
		
		// Add owner of the bucket first 
		$emails = array($bucket->user->email);		
		
		// Then collaborators
		foreach ($bucket->get_collaborators(TRUE) as $collaborator)
		{
			$emails[] = $collaborator['email'];
		}
		
		// Then followers
		foreach ($bucket->subscriptions->find_all() as $follower)
		{
			$emails[] = $follower->email;
		}
		
		$text_body = $text->render();
		$html_body = $html->render();
		$site_email = Swiftriver_Mail::get_default_address();
		$from = '"'.$comment->user->name.'" <notifications@'.Swiftriver_Mail::get_email_domain().'>';
		$token_data = array('bucket_id' => $comment->bucket_id);
		$token = Model_Auth_Token::create_token('bucket-comment', $token_data);
		$reply_to = 'bucket-comment-'.$token->token.'@'.Swiftriver_Mail::get_comments_email_domain();
		foreach ($emails as $email)
		{
			if ($email != $comment->user->email) 
			{
				Swiftriver_Mail::send($email, $subject, $text_body, $html_body, $from, array('Reply-To' => $reply_to));
			}
		}
	}
}
?>