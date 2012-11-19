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
	public static function send($email, $subject, $mail_body, $mail_body_html = NULL)
	{
		$site_email = Kohana::$config->load('site.email_address');
		$from_address = '"'.Model_Setting::get_setting('site_name').'" <'.$site_email.'>';
		
		// Send multipart message
		require_once ("Mail/mime.php");
			
		$mime = new Mail_mime(array('eol' => "\n"));
		$mime->setTXTBody($mail_body);
		
		if ( ! empty($mail_body_html))
		{
			$mime->setHTMLBody($mail_body_html);
		}

		$headers = $mime->headers(array('From' => $from_address));
		$headers_str = '';
		foreach ($headers as $key => $value)
		{
			$headers_str .= $key.': '.$value."\r\n";
		}
		
		$body = $mime->get();
		mail($email, $subject, $body, $headers_str);
	}
}
?>