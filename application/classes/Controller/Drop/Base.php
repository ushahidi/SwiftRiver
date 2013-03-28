<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base Controller for the drop views
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
abstract class Controller_Drop_Base extends Controller_Swiftriver {
	
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
	
	
	public function before()
	{
		parent::before();
	}

	/**
	 * xHR endpoint for fetching drops from a drop container
	 */
	abstract public function action_droplets();

	/**
	 * XHR endpoint for adding/removing tags to a drop container
	 */
	abstract public function action_tags();
	
	/**
	 * xHR endpoint for adding/removing links to a drop container
	 */
	abstract public function action_links();
	
	/**
	 * xHR endpoint for adding/removing places
	 */
	abstract public function action_places();
	
	/**
	 * xHR endpoint for adding/removing comments
	 */
	abstract public function action_comments();
	
	/**
	 * XHR endpoint for adding/removing custom drop fields
	 */
	abstract public function action_forms();
		
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