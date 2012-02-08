<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Droplets
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Droplet extends ORM
{

	/**
	 * A droplet has and belongs to many links, places, tags and attachments
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'attachments' => array(
			'model' => 'attachment',
			'through' => 'attachments_droplets'
			),
		'buckets' => array(
			'model' => 'bucket',
			'through' => 'buckets_droplets'
			),			
		'places' => array(
			'model' => 'place',
			'through' => 'droplets_places'
			),
		'stories' => array(
			'model' => 'story',
			'through' => 'droplets_stories'
			),
		'tags' => array(
			'model' => 'tag',
			'through' => 'droplets_tags'
			),
		'account_droplet_tags' => array(
 			'model' => 'account_droplet_tag'
			),			
		'account_droplet_links' => array(
 			'model' => 'account_droplet_link'
			),			
		'account_droplet_places' => array(
 			'model' => 'account_droplet_place'
			),			
		'links' => array(
			'model' => 'link',
			'through' => 'droplets_links'
			)			
		);
		
	/**
	 * Many-to-one relationship defintion
	 * A droplet belongs to an identity and a channel_filter
	 * @var array
	 */
	protected $_belongs_to = array(
		'identity' => array()
	);

	/**
	 * Overload saving to perform additional functions on the droplet
	 */
	public function save(Validation $validation = NULL)
	{
		// Ensure Channel Goes In as Lower Case
		$this->channel = strtolower($this->channel);

		// Do this for first time droplets only
		if ($this->loaded() === FALSE)
		{
			// Save the date the droplet was first added
			$this->droplet_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save($validation);
	}
	
	/**
	 * Checks if a droplet already exists based on its hash
	 *
	 * @param string $droplet_hash SHA2 Hash of the origin ID of the droplet
	 */
	public static function is_duplicate_droplet($droplet_hash)
	{
		return (bool) ORM::factory('droplet')
						->where('droplet_hash', '=', $droplet_hash)
						->count_all();
	}
	
	/**
	 * Retrives a droplet from the DB based on its droplet_hash. The hash is 
	 * unique for every droplet.
	 *
	 * @param string $droplet_hash Has value of the droplet to retrieve
	 * @return Model_Droplet
	 */
	public static function get_droplet_by_hash($droplet_hash)
	{
		return ORM::factory('droplet')
		    ->where('droplet_hash', '=', $droplet_hash)
		    ->find();
	}
	
	/**
	 * Creates a droplet from an array. 
	 * The array keys should correspond to the column names of the droplet 
	 * table. The method also checks for 3 other keys namely: links, tags 
	 * and places - These keys are populated when the droplet is taken through 
	 * the extraction phase
	 *
	 * @param array $droplet
	 * @return bool
	 */
	public static function create_from_array(array & $droplet)
	{
		if ( ! array_key_exists('id', $droplet))
		{
			try 
			{
				// Create the droplet
				$orm_droplet = new Model_Droplet;
				$orm_droplet->channel = $droplet['channel'];
				$orm_droplet->identity_id = $droplet['identity_id'];
				$orm_droplet->droplet_hash = $droplet['droplet_hash'];
				$orm_droplet->droplet_orig_id = $droplet['droplet_orig_id'];
				$orm_droplet->droplet_type = $droplet['droplet_type'];
				$orm_droplet->droplet_title = $droplet['droplet_title'];
				$orm_droplet->droplet_content = $droplet['droplet_content'];
				$orm_droplet->droplet_raw = $droplet['droplet_raw'];
				$orm_droplet->droplet_locale = $droplet['droplet_locale'];
				$orm_droplet->droplet_date_pub = $droplet['droplet_date_pub'];
				$orm_droplet->droplet_processed = 0;
			
				// Check if the parent id has been set
				if (isset($droplet['parent_id']))
				{
					$orm_droplet->parent_id = $droplet['parent_id'];
				}
			
				// Save the droplet
				$orm_droplet->save();
			
				// Check if the 'river_id' OR 'bucket_id' key is set
				if (isset($droplet['river_id']))
				{
					// Add the droplet to the river
					Model_River::add_droplet($droplet['river_id'], $orm_droplet);
				}
				elseif (isset($droplet['bucket_id']))
				{
					Model_Bucket::add_droplet($droplet['bucket_id'], $orm_droplet);
				}
				
				// Set the 'id' key to the newly saved droplet
				$droplet['id'] = $orm_droplet->id;
				
				return TRUE;
			}
			catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, $e->getMessage());
				return FALSE;
			}
		}
		else
		{
			try
			{
				// Get the droplet id
				$droplet_id = $droplet['id'];
			
				// Get the droplet ORM reference
				$orm_droplet = ORM::factory('droplet', $droplet_id);
			
				// Save the tags, links and places
				self::add_tags($orm_droplet, $droplet['tags']);
				self::add_links($orm_droplet, $droplet['links']);
				self::add_places($orm_droplet, $droplet['places']);
			
				// Mark the droplet as processed
				$orm_droplet->droplet_processed = 1;
				$orm_droplet->save();
				
				return TRUE;
				
			} catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, $e->getMessage());
				return FALSE;
			}
			
		}
		
		return FALSE;
	}
	
	/**
	 * Adds tags to a droplet
	 *
	 * @param Model_Droplet $orm_droplet Droplet ORM reference
	 * @param Mixed $tags Tag/list of tags to be linked to the droplet
	 */
	public static function add_tags($orm_droplet, $tags)
	{
		foreach ($tags as $tag)
		{
			// Check if the tag already exists
			$orm_tag = Model_Tag::get_tag_by_name($tag, TRUE);
			if ($orm_tag AND ! $orm_droplet->has('tags', $orm_tag))
			{
				$orm_droplet->add('tags', $orm_tag);
			}
		}
	}
	
	
	/**
	 * Adds links to a droplet
	 *
	 * @param Model_Droplet $orm_droplet Droplet ORM reference
	 * @param array $links
	 */
	public static function add_links($orm_droplet, $links)
	{
		foreach ($links as $url)
		{
			try
			{
				// Add the link
				$orm_link = Model_Link::get_link_by_url($url, TRUE);
				if ($orm_link AND ! $orm_droplet->has('links', $orm_link))
				{
					$orm_droplet->add('links', $orm_link);
				}
			}
			catch (Database_Exception $e)
			{
				// Log the error and proceed to process the next item on the list
				Kohana::$log->add(Log::ERROR, 'A database error has occurred: '.$e->getMessage());
				continue;
			}
		}
	}
	
	/**
	 * Adds the list of place names associated with a droplet. The list of places
	 * should be an array of place names, latitudes and longitudes.
	 *
	 * Eg:
	 * $places  = array(
	 * 		array('name' => 'Nairobi', 'latitude => '-1.2857', 'longitude' => '36.820174', 'source' => 'placemaker'),
	 *	    array('name' => 'Mombasa', 'latitude'=> '-4.050063', 'longitude' => '39.666653', 'source' => 'placemaker')
	 * ));
	 * @param Model_Droplet $orm_droplet Droplet ORM reference
	 * @param array $places List of place names associated with the droplet
	 */
	public static function add_places($orm_droplet, $places)
	{
		foreach ($places as $place)
		{
			// Get the place record
			$orm_place = Model_Place::get_place_by_lat_lon($place, TRUE);
			if ($orm_place AND !$orm_droplet->has('places', $orm_place))
			{
				$orm_droplet->add('places', $orm_place);
			}
		}
	}
	
	/**
	 * Gets the list of droplets that are yet to be processed. This method
	 * is called by Swiftriver_Dropletqueue::process
	 *
	 * @param int    $limit The no. of droplets to fetch
	 * @param string $channel The origin channel of the droplets
	 * @return array
	 */
	public static function get_unprocessed_droplets($limit = 1, $channel = NULL)
	{
		// Return value
		$droplets = array();
		
		// Predicate for filtering droplets by channel
		$predicates = empty($channel)
			? array('id', '>', 0) 
			: array('channel', '=', $channel);
		
		// Get the droplets ordered by pub_date in DESC order
		$result = ORM::factory('droplet')
					->where('droplet_processed', '=', 0)
					->where($predicates[0], $predicates[1], $predicates[2])
					->order_by('id', 'DESC')
					->limit($limit)
					->find_all();
		
		foreach ($result as $droplet)
		{
			$droplets[] = array(
				'id' => $droplet->id,
				'droplet_content' => $droplet->droplet_content,
				'channel' => $droplet->channel,
				'tags' => array(),
				'links' => array(),
				'places' => array()
			);
		}
		// Return items
		return $droplets;
	}
	
	/**
	 * Gets a droplet's buckets as an array
	 *
	 * @return array
	 */
	public function get_buckets()
	{
		$buckets = array();
		foreach ($this->buckets->find_all() as $bucket)
		{
			$buckets[] = array('id' => $bucket->id, 'bucket_name' => $bucket->bucket_name);
		}
		
		return $buckets;
    }

	/**
	 * Gets a droplet's tags as an array
	 *
	 * @return array
	 */
	public function get_tags($account_id = NULL)
	{
		$tags = array();
	    
		foreach ($this->tags->find_all() as $tag)
		{
			$tags[] = array('id' => $tag->id, 'tag' => $tag->tag);
		}
		
		// User defined tags
		if ($account_id)
		{
			foreach ($this->account_droplet_tags->where('account_id', '=', $account_id)->find_all() as $account_tag)	
			{
				$tags[] = array('id' => $account_tag->tag->id, 'tag' => $account_tag->tag->tag);
			}
		}

		
		return $tags;
    }

	/**
	 * Gets a droplet's links as an array
	 *
	 * @return array
	 */
	public function get_links()
	{
		$links = array();
	    
		foreach ($this->links->find_all() as $link)
		{
			$links[] = array('id' => $link->id, 'link_full' => $link->link_full);
		}
		
		return $links;
    }

	/**
	 * Gets a droplet's places as an array
	 *
	 * @return array
	 */
	public function get_places()
	{
		$places = array();
	    
		foreach ($this->places->find_all() as $place)
		{
			$places[] = array('id' => $place->id, 'place_name' => $place->place_name);
		}
		
		return $places;
    }

	/**
	 * Given an array of droplets, populates a buckets array element
	 *
	 * @param array $droplets
	*/
	public static function populate_buckets(& $droplets)
	{
		foreach($droplets as & $droplet) {
			$droplet_orm = ORM::factory('droplet', $droplet['id']);
			
			$droplet['buckets'] = $droplet_orm->get_buckets();
		}	    
	}
	
	/**
	 * Given an array of droplets, populates a tags array element
	 *
	 * @param array $droplets
	*/
	public static function populate_tags(& $droplets, $account_id)
	{
		foreach($droplets as & $droplet)
		{
			$droplet_orm = ORM::factory('droplet', $droplet['id']);
			
			$droplet['tags'] = $droplet_orm->get_tags($account_id);
		}	    
	}

	/**
	 * Given an array of droplets, populates a links array element
	 *
	 * @param array $droplets
	*/
	public static function populate_links(& $droplets)
	{
		foreach ($droplets as & $droplet)
		{
			$droplet_orm = ORM::factory('droplet', $droplet['id']);
			
			$droplet['links'] = $droplet_orm->get_links();
		}	    
	}

	/**
	 * Given an array of droplets, populates a places array element
	 *
	 * @param array $droplets
	*/
	public static function populate_places(& $droplets)
	{
		foreach ($droplets as & $droplet)
		{
			$droplet_orm = ORM::factory('droplet', $droplet['id']);
			
			$droplet['places'] = $droplet_orm->get_places();
		}	    
	}
	
	/**
	 * Given an array of droplets, populates the discussions
	 */
	public static function populate_discussions(array & $droplets)
	{
		foreach ($droplets as & $droplet)
		{
			$droplet['discussions'] = self::get_discussions($droplet['id']);
		}
	}
    
	/**
	 * Updates a droplet from an array. 
	 *
	 * @param array
	 * @return void
	 */
	public function update_from_array($droplet_array) 
	{
		$this->__update_buckets($droplet_array);
	}

	/**
	 * Updates a droplet's buckets from an array. 
	 *
	 * @param array
	 * @return void
	 */	
	private function __update_buckets($droplet_array)
	{

		// Function to xxtract the bucket ids from the array
		function id($bucket)
		{
			return $bucket['id'];
		}
		
		// Determine the delta
		$current_buckets = array_map("id", $this->get_buckets());
		$change_buckets = array_map("id", $droplet_array['buckets']);
		
		$new_buckets = array_diff($change_buckets, $current_buckets);
		$delete_buckets = array_diff($current_buckets, $change_buckets);
		
		// Add droplet to the new buckets
		foreach ($new_buckets as $new_bucket_id)
		{
			$bucket_orm = ORM::factory('bucket', $new_bucket_id);
			
			if ($bucket_orm->loaded())
			{
				$this->add('buckets', $bucket_orm);
			}
		}
		
		// Remove droplet for the delete buckets
		foreach ($delete_buckets as $delete_bucket_id)
		{
			$bucket_orm = ORM::factory('bucket', $delete_bucket_id);
			
			if ($this->has('buckets', $bucket_orm))
			{
				$this->remove('buckets', $bucket_orm);
			}
		}

	}
	
	/**
	 * Returns the list of droplets that have $droplet_id as the parent
	 * @param int $droplet_id ID of the droplet
	 * @return array
	 */
	public static function get_discussions($droplet_id)
	{
		// Get the discussions
		$discussions = DB::select(array('droplets.id', 'id'), 'droplet_title', 
		    array('droplets.parent_id', 'parent_id'), 
		    array('droplets.channel', 'channel'), 'droplet_content', 
		    'identity_name', 'identity_avatar', 'droplet_date_pub')
		    ->from('droplets')
		    ->join('identities', 'INNER')
		    ->on('droplets.identity_id', '=', 'identities.id')
		    ->where('droplets.parent_id', '=', $droplet_id)
		    ->order_by('droplets.id', 'ASC');
		
		return $discussions->execute()->as_array();
	}
	
	/**
	 * Removes the given tags from a droplet
	 * @param int $droplet_id ID of the droplet
	 * @param int $tag_id ID of the tag	
	 * @return boolean
	 */	
	public static function delete_tag($droplet_id, $tag_id, $account_id)
	{
		$droplet_orm = ORM::factory('droplet', $droplet_id);		
		if ( ! $droplet_orm->loaded())
			return FALSE;
			
		$tag_orm = ORM::factory('tag', $tag_id);
		if ( ! $tag_orm->loaded())
			return FALSE;
			
		if ($droplet_orm->has('tags', $tag_orm)) 
		{
			$droplet_orm->remove('tags', $tag_orm);
		}
		
		// User tag
		$user_tag_orm = $droplet_orm->account_droplet_tags
		                            ->where('account_id', '=', $account_id)
		                            ->where('tag_id', '=', $tag_id)
		                            ->find();
		
		if ($user_tag_orm->loaded())
		{
			$user_tag_orm->delete();
		}
		
		return TRUE;
	}
}

?>
