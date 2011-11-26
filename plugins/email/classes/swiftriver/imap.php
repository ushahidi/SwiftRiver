<?php defined('SYSPATH') or die('No direct script access'); 
/**
 * Provides a facility for fetching email from an IMAP server
 *
 * @package SwiftRiver
 * @category Plugins
 * @author  Ushahidi Team
 * @copyright (c) Ushahidi Inc 2008-2011 >http://www.ushahidi.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Imap {
	
	/**
	 * IMAP Stream reference
	 * @var mixed
	 */
	private $imap_stream;
	
	/**
	 * Timeout for opening and reading an IMAP stream
	 * @var int
	 */
	const IMAP_OPEN_READ_TIMEOUT = 90;
	
	/**
	 * Initiates a connection to the IMAP server using the specified parameters
	 * @param   string  $server Name or IP address of the IMAP server
	 * @param   string  $port Port to connect on
	 * @param   string  $username The username
	 * @param   string  $password The passowrd associated with the username
	 * @param   string  $server_type Type of mail server POP3/IMAP
	 * @param   bool    $ssl When TRUE, uses SSL to to encrypt the IMAP session
	 * @param   string  $mailbox_name Name of the mailbox to use - default is INBOX
	 */
	public function __construct($server, $port, $username, $password, $server_type = 'imap', 
		$ssl = FALSE, $mailbox_name = NULL)
	{
		// SSL Flag
		$ssl_flag = $ssl? '/ssl' : '';
		
		// Service flag
		$service = ($server_type == 'imap')? '/imap' : '/pop3';
		
		// Generate the string for accessing the mailbox
		$mailbox_str = '{'
				. $server.':'.$port
				. $service
				. $ssl_flag
				. '/novalidate-cert'
				.'}';
		
		// Append the mailbox_name parameter; Default is INBOX
		$mailbox_str .= (empty($mailbox_name) OR strtoupper($mailbox_name) == 'INBOX')? '': $mailbox_name;
		
		// Set the timeouts for open and reading
		imap_timeout(IMAP_OPENTIMEOUT, self::IMAP_OPEN_READ_TIMEOUT);
		imap_timeout(IMAP_READTIMEOUT, self::IMAP_OPEN_READ_TIMEOUT);
		
		// Open stream to the mailbox
		if ($imap_stream = @imap_open($mailbox_str, $username, $password))
		{
			$this->imap_stream = $imap_stream;
		}
		else
		{
			// Log the error
			Kohana::$log->add(Log::ERROR, "Could not open IMAP stream: :error", array(":error" => imap_last_error()));
			
			$this->imap_stream = FALSE;
		}
	}
	
	/**
	 * Gets all the unread messages
	 *
	 * @param   int $limit The number of messages to fetch
	 * @param   string $criteria Criteria for searching the mailbox
	 * @return  bool TRUE if successful, FALSE otherwise
	 */
	public function get_messages($limit = 50, $criteria = NULL)
	{
		if ( ! $this->imap_stream)
			return FALSE;
		
		// Set the search criteria
		$criteria = empty($criteria)? "UNSEEN" : '';
			
		// Get all unread messages
		if ($unread = @imap_search($this->imap_stream, $criteria))
		{
			// Get the no. fo messages specifed by the limit
			if (count($unread) > $limit AND $limit > 0)
			{
				$unread = array_splice($unread, 0, $limit);
			}
			
			// Get each messag and create it as a droplet
			foreach ($unread as $msg_no)
			{
				// Get the droplet template
				$droplet = Swiftriver_Dropletqueue::get_droplet_template();
				
				// Get the header and message body
				$header_info = imap_headerinfo($this->imap_stream, $msg_no);
				$message_body = imap_body($this->imap_stream, $msg_no);
				
				// Pre-processing - Convert to UTF-8
				$encoding = mb_detect_encoding($message_body, "auto");
				$encoding = ($encoding == 'ASCII')? 'iso-8859-1' : $encoding;
				$message_body = strip_tags($message_body);
				
				$from_header = $header_info->from;
				$sender_address = '';
				$sender_name = '';
				
				// Build out the sender's email address and name
				foreach ($from_header as $id => $object)
				{
					$sender_name = isset($object->personal)? $object->personal : '';
					$sender_address = (isset($object->mailbox) AND isset($object->host))
						? $object->mailbox.'@'.$object->host
						: '';
				}
				
				// Normalize email into droplet
				$droplet['identity_username'] = $sender_address;
				$droplet['identity_orig_id'] = $droplet['identity_username'];
				$droplet['identity_name'] = $sender_name;
				$droplet['droplet_orig_id'] = $header_info->message_id;
				$droplet['droplet_type'] = 'email';
				$droplet['droplet_title'] = $this->_get_subject_text($header_info->subject);
				$droplet['droplet_content'] = $message_body;
				$droplet['droplet_date_pub'] = date('Y-m-d H:i:s', $header_info->udate);
				
				// Kohana::$log->add(Log::DEBUG, "Message id: :id\nContent: :content", 
				// 	array(":id"=>$header_info->message_id, ":content" => $message_body));
					
				// TODO - Figure out how to place the droplet in a user account
				// This the user account should be set at this stage
				
				// TODO - Handle attachments
				
				// Add the droplet to the dropletqueue
				Swiftriver_Dropletqueue::add($droplet, FALSE);
				
				// Mark the message as seen
				imap_setflag_full($this->imap_stream, $msg_no, '\\Seen');
			}
			
			// Close the IMAP stream
			imap_close($this->imap_stream);
			
			return TRUE;
		}
		else
		{
			// Log errors
			Kohana::$log->add(Log::ERROR, "IMAP error: :error", array(":error" => imap_last_error()));
			imap_close($this->imap_stream);
			return FALSE;
		}	
	}
	
	/**
	 * Decodes a MIME header elements present in the "subject" header
	 * of the email
	 *
	 * @param string $str The MIME text
	 * @return string
	 */
	private function _get_subject_text($str)
	{
		$elements = imap_mime_header_decode($str);
		$text = "";
		for ($i = 0; $i < count($elements);  $i++)
		{
			// TODO: Check for character set
			$text .= $elements[$i]->text;
		}
		
		return strip_tags($text);
	}
}
?>