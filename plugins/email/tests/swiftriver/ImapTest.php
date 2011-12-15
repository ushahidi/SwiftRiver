<?php defined('SYSPATH') or die('No direct script access allowed'); 
/**
 * Unit test for the Imap library
 *
 * @package    Swiftriver
 * @category   Tests
 * @author     Ushahidi Team
 * @copyright  (c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3)
 */
class Swiftriver_Imap_Test extends Unittest_TestCase {
	
	public function setUp()
	{
		// Get a configured account
		$account = ORM::factory('email_setting')->where('user_id', '=', 1)->find();
		
		// Instantiate IMAP library
		// Substitute username and password with the real values
		$ssl = ($account->server_ssl == 1); 
		$this->imap_email = new Swiftriver_Imap($account->server_host, $account->server_port, 
			$account->username, $account->password, $account->server_type, $ssl, $account->mailbox_name);
		
		unset ($account);
	}
	
	
	/**
	 * Tests the fetching of messages from the IMAP server
	 * @covers Swiftriver_Imap->get_messages
	 */
	public function test_get_messages()
	{
		// Get messages
		$result = $this->imap_email->get_messages(20);
		
		$this->assertTrue($result, "Could not get email messages");
	}
	
	public function tearDown()
	{
		unset($this->imap_email);
	}
	
}
?>
