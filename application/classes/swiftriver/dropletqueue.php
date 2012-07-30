<?php defined('SYSPATH') or die('No direct script access');

/**
 * Droplet procesing library. Handles queueing of droplets for processing,
 * initiating processing (semantic tagging of droplets) and monitoring status
 * of the queue on a per channel basis.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package     Swiftriver http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   (c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Swiftriver_Dropletqueue {
	
	/**
	 * Maintains the list of droplets to be passed on for processing
	 * @var array
	 */
	private static $_queue = array();
	
	/**
	 * List of processed droplets
	 * @var array
	 */
	private static $_processed = array();
	
	public static function isempty()
	{
	    return empty(self::$_queue);
	}
	
	/**
	 * Processes the droplet queue.
	 * The queue processing involves extracting metadata from the droplets
	 * saving these to the database. The extracted metadata could be links,
	 * named entities, places
	 *
	 * @param string $channel Name of the channel whose droplets are to be processed
	 */
	public static function process($channel = NULL)
	{
		Kohana::$log->add(Log::INFO, "Post processing started");
		Kohana::$log->write();
		
		// If the queue is empty, fetch the unprocessed items from the DB
		self::$_queue = empty(self::$_queue)
			? Model_Droplet::get_unprocessed_droplets(1000, $channel)
			// Reverse the ordering of items in the array - FIFO queue
			: array_reverse(self::$_queue);
		
		// Process the items in the queue
		while ( ! empty(self::$_queue))
		{
			// Pop that droplet!
			$droplet = array_pop(self::$_queue);
			
			// Submit the droplet to an extraction plugin
			Swiftriver_Event::run('swiftriver.droplet.extract_metadata', $droplet);
			
			// Add the droplet to the list of processed items
			self::$_processed[] =  $droplet;

			// Mark the droplet as processed
			Model_Droplet::create_from_array(array($droplet));
		}
		
		Kohana::$log->add(Log::INFO, "Post processing completed");
	}
	
	
	/**
	 * Adds drops to the processing queue.
	 *
	 * @param array $droplet Array of Droplets to be queued for processing
	 * @param bool $queue_droplet When TRUE, adds the droplet to self::$_queue
	 */
	public static function add($droplet, $queue_droplet = TRUE)
	{
		if (list(,$new_drops) = Model_Droplet::create_from_array(array($droplet)))
		{
			$drop = array_pop($new_drops);

			if ($queue_droplet AND ! empty($drop))
			{
				self::$_queue[] = $drop;
			}
			
			return $drop;
		}
		
		return NULL;
	}
	
	/**
	 * Generates and returns the template for a droplet. The template
	 * is a key=>value array which the crawlers use for constructing 
	 * an actual droplet
	 *
	 * @return array
	 */
	public static function get_droplet_template()
	{
		return array(
			'channel' => '',
			'river_id' => '',
			'identity_orig_id' => '',
			'identity_username' => '',
			'identity_name' => '',
			'droplet_orig_id' => '',
			'droplet_type' => '',
			'droplet_title' => '',
			'droplet_content' => '',
			'droplet_locale' => '',
			'droplet_date_pub' => '',
		);
	}
	
	/**
	 * Gets the list of droplets that have already undergone processing
	 * This method should be called by the controller that is responsible
	 * for rendering the processed droplets on the UI
	 *
	 * @return array
	 */
	public static function get_processed_droplets()
	{
		// Fetch the processed droplets
		$result = self::$_processed;
		
		// Reset the processed queue
		self::$_processed = array();
		
		return $result;
	}
	
	
	/**
	 * Create a single new drop and publish new ones for meta extraction.
	 *
	 * @return array
	 */
	public static function create_drop($drop)
	{
		list($drops, $new_drops) = Model_Droplet::create_from_array(array($drop));
		
		if ( ! empty($new_drops))
		{
			Swiftriver_Event::run('swiftriver.droplet.extract_metadata', $new_drops[0]);
			Model_Droplet::create_from_array(array($new_drops[0]));
		}
		
		return $drops[0];
	}
}
?>
