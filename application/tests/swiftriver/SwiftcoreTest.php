<?php defined('SYSPATH') or die('No direct script access'); 
/**
 * Unit test for the Swiftcore plugin
 *
 * @package    Swiftriver
 * @category   Tests
 * @author     Ushahidi Team
 * @copyright  (c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3)
 */
class Swiftriver_Swiftcore_Test extends Unittest_TestCase {
	
	/**
	 * Override default setUp
	 */
	public function setUp()
	{
		// Get the droplet template
		$this->droplet = Swiftriver_Dropletqueue::get_droplet_template();
		
		// Add the tags and places properties
		$this->droplet['tags'] = array();
		$this->droplet['places'] =  array();
		
		// Add some test content for extraction via the API
		$this->droplet['droplet_content'] = "The other day, presidential aspirant Raphael Tuju found it "
			. "necessary to invoke 'labelling' as an assault weapon against perceived sponsors of attacks on "
			. "him in Kisumu. He called them 'communists' and 'fascists.' Labelling is not new in history and "
			. "is not without purpose. Ask Moi what he thought reformers were and the answer will be similar.";
		
		// Run the event
		Swiftriver_Event::run('swiftriver.droplet.extract_metadata', $this->droplet);
	}
	
	/**
	 * Tests entity extraction via the Swiftcore API
	 * @covers Swiftcore_Init->extract_entities
	 */
	public function test_extract_entites()
	{
		$this->assertTrue(Swiftriver_Event::has_run('swiftriver.droplet.extract_metadata'), 'Event has not run');
		
		// Get the droplet
		$this->dropplet = Swiftriver_Event::$data;
		
		// Check that tags have been set
		$this->assertGreaterThan(0, count($this->droplet['tags']), "Entity extraction failed");
	}
	
	/**
	 * Overrides default tearDown
	 */
	public function tearDown()
	{
		// Garbage collection
		unset ($this->droplet);
	}
	
}

?>