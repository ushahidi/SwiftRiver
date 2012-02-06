<?php defined('SYSPATH') or die('No direct script access');

/**
 * DropletQueue
 *
 * @author      Ushahidi Team
 * @package     Swiftriver http://github.com/ushahidi/Swiftriver_v2
 * @category    Helpers
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
			Model_Droplet::create_from_array($droplet);
		}
		
		Kohana::$log->write();
	}
	
	/**
	 * Adds a droplet to the processing queue.
	 * Crawlers that siphon content from the various channels should call
	 * this method once the content has been "atomized" i.e. converted into a droplet
	 * The template for the droplets may be obtained by invoking get_droplet_template
	 * from within the crawler.
	 *
	 * @param array $droplet Droplet to be queued for processing
	 * @param bool $queue_droplet When TRUE, adds the droplet to self::$_queue
	 */
	public static function add(array & $droplet, $queue_droplet = TRUE)
	{
		// Set the SHA-256 hash value for the droplet
		$droplet['droplet_hash'] = hash('sha256', $droplet['droplet_content']);
		
		// Strip the tags from to get droplet_raw
		$droplet['droplet_raw'] = strip_tags($droplet['droplet_content']);
		
		
		// Check if the droplet has already been added to the queue
		if (Model_Droplet::is_duplicate_droplet($droplet['droplet_hash']))
		{
			// Get the droplet ORM based on the hash
			$droplet_orm = Model_Droplet::get_droplet_by_hash($droplet['droplet_hash']);
			
			// Add the droplet to the current river
			Model_River::add_droplet($droplet['river_id'], $droplet_orm);
			
			// Delete the droplet from memory
			unset ($droplet, $droplet_orm);
			return FALSE;
		}
		
		// Validate the droplet
		$validation = Validation::factory($droplet)
					->rule('channel', 'not_empty')
					->rule('droplet_content', 'not_empty')
					->rule('droplet_raw', 'not_empty')
					->rule('droplet_date_pub', 'not_empty')
					->rule('identity_orig_id', 'not_empty')
					->rule('identity_username', 'not_empty');
		
		if ($validation->check())
		{
			// Create the identity
			Model_Identity::create_from_droplet($droplet);
		
			// Create droplet from the array
			Model_Droplet::create_from_array($droplet);
		
			// Check if queueing mode is enabled
			if ($queue_droplet)
			{
				// Add new proprties to the droplet
				$droplet['tags'] = array();
				$droplet['links'] = array();
				$droplet['places'] = array();
	
				// Add the droplet to the queue
				self::$_queue[] = $droplet;
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
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
}
?>
