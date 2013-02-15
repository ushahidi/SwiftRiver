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
	 * Boolean indicating whether the logged in user owns the river
	 * @var bool
	 */
	protected $owner = FALSE; 
	
	/**
	 * Boolean indicating whether the logged in user owns the river
	 * @var bool
	 */
	protected $public = FALSE; 
	
	/**
	 * @var Service_Drop
	 */
	private $drop_service;
	
	
	public function before()
	{
		parent::before();
		
		$this->drop_service = new Service_Drop($this->api);
	}
	
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
				$this->drop_service->add_drop_tag($droplet_id, $tag_name);
			break;

			case "DELETE":
				// Is the logged in user an owner?
				if ( ! $this->owner)
				{
					throw new HTTP_Exception_403();
				}
				
				// Delete the drop from the list of tags for the currently logged in user
				$this->drop_service->delete_drop_tag($droplet_id, $tag_id);
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

				$link = $this->drop_service->add_drop_link($droplet_id, $url);
				echo json_encode($link);
			break;

			case "DELETE":
				$this->drop_service->delete_drop_link($droplet_id, $link_id);
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
				$places_array = json_decode($this->request->body(), TRUE);
				$place_name = $places_array['place_name'];
				$this->response->headers('Content-Type');
				if ( ! Valid::not_empty($place_name))
				{
					$this->response->status(400);
					$errors = array(__("Invalid location"));
					echo json_encode(array('errors' => $errors));
					return;
				}

				// TODO Use geocoder to determine the latitude & longitude
				// $place = $this->drop_service->add_drop_place($droplet_id, $place_name, $latitude, $longitude);
				// echo json_encode($place);
			break;

			case "DELETE":
				$this->drop_service->delete_drop_place($droplet_id, $place_id);
			break;
		}
	}
	
	 /**
	  * Comments restful api
	  */ 
	public function action_comments()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$droplet_id = intval($this->request->param('id', 0));
		
		// Set the response headers		
		$this->response->headers('Content-Type', 'application/json');

		switch ($this->request->method())
		{
			case "GET":
				$params = $this->request->query();
				
				$since_id = isset($params['since_id']) ? intval($this->request->query('since_id')) : NULL;
				$comments = $this->drop_service->get_drop_comments($droplet_id, $since_id);
					
				if (empty($comments))
				{
				    throw new HTTP_Exception_404('The requested page was not found on this server.');
				}
				
				echo json_encode($comments);
			break;

			case "POST":
				// Is the logged in user an owner?
				if ( ! $this->owner AND ! $this->collaborator AND ! $this->public)
				{
					throw new HTTP_Exception_403();
				}
				
				// Get the POST data
				$body = json_decode($this->request->body(), TRUE);
				
				$comment = $this->drop_service->add_drop_comment($droplet_id, $body['comment_text']);
								
				if (empty($comment)) 
					throw new HTTP_Exception_400();
				
				// NOTES: ekala
				// Disabling email notification for now
				// $context_obj = ($this instanceof Controller_River) ? $this->river : $this->bucket;
				// Swiftriver_Mail::notify_new_drop_comment($comment, $context_obj);
				
				echo json_encode($comment);
			break;

			case "PUT":
				$comment_id = intval($this->request->param('id2', 0));
				$this->drop_service->delete_drop_comment($droplet_id, $comment_id);
			break;
		}
	}
	
	/**
	 * REST endpoint for sharing droplets via email
	 */
	public function action_share()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		if ($this->request->method() != "POST")
			throw HTTP_Exception::factory(405)->allowed('POST');

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
			throw HTTP_Exception::factory(400);
		}
		else
		{
			list($recipient, $subject) = array($post['recipient'], $post['drop_title']);

			// Modify the mail body to include the email address of the
			// use sharing content
			$mail_body = __(":user has shared a drop with you via SwiftRiver\n\n:url",
			    array(':user' => $this->user['owner']['username'], ':url' => $post['drop_url']));

			// Send the email
			Swiftriver_Mail::send($recipient, $subject, $mail_body);
		}
	}
}