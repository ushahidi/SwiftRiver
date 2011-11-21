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
		// Instantiate IMAP library
		// Substitute username and password with the real values
		$this->imap_email = new Swiftriver_Imap("imap.example.com", 993, 
			"username@example.com", "password", TRUE);
	}
	
	
	/**
	 * Tests the fetching of messages from the IMAP server
	 * @covers Swiftriver_Imap->get_messages
	 */
	public function test_get_messages()
	{
		// Get messages
		$result = $this->imap_email->get_messages(20, "ALL");
		
		$this->assertTrue($result, "Could not get email messages");
	}
	
	public function tearDown()
	{
		unset($this->imap_email);
	}
	
}
?>
