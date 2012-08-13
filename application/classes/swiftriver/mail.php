<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mail Helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   (c) 2012 Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Mail {
		
	/**
	 * Sends an email with from address determined from site configuration
	 *
	 * @param	string	 $email
	 * @param	string	 $subject
	 * @param	string	 $mail_body	 	 
	 * @return	null
	 */	   
	public static function send($email, $subject, $mail_body)
	{
		$site_email = Kohana::$config->load('site.email_address');
		$headers = 'From: "'.Model_Setting::get_setting('site_name').
										'" <'.$site_email.'>'."\r\n".
		           'Reply-To: '.$site_email."\r\n";
		mail($email, $subject, $mail_body, $headers);
	}

}
?>