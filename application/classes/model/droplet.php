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
			),
		'droplet_scores' => array()
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
			// Save the date the droplet was first added in UTC
			$this->droplet_date_add = gmdate("Y-m-d H:i:s", time());
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
					// Add the droplet to the river(s)
					if (is_array($droplet['river_id']))
					{
						foreach ($droplet['river_id'] as $river_id)
						{
							Model_River::add_droplet($river_id, $orm_droplet);
						}
					}
					else
					{						
						Model_River::add_droplet($droplet['river_id'], $orm_droplet);
					}
				}
				elseif (isset($droplet['bucket_id']))
				{
					Model_Bucket::add_droplet($droplet['bucket_id'], $orm_droplet);
				}
				
				// Set the 'id' key to the newly saved droplet
				$droplet['id'] = $orm_droplet->id;

				// If the droplet has a parent id, set the droplet_orig_id
				// to the ID of the droplet
				if ($orm_droplet->parent_id != 0)
				{
					$orm_droplet->droplet_orig_id = $orm_droplet->id;
					$orm_droplet->save();
				}
				
				return $orm_droplet;
			}
			catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, "Database Error creating droplet".$e->getMessage());
				return NULL;
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
				Kohana::$log->add(Log::ERROR, "Database error updating droplet".$e->getMessage());
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
		// Function to map a tags array into an array of tag!!>--<!!tag_type strings
		// since php array comparison is a bit limited
		$tag_merge = function($tag)
		{
			if (is_array($tag))
			{
				return $tag['tag_name'].'!!>--<!!'.$tag['tag_type'];
			}
			else
			{
				return $tag->tag.'!!>--<!!'.$tag->tag_type;
			}
		};
		
		// Determine the new tags
		$current_tags = array_map($tag_merge, $orm_droplet->tags->find_all()->as_array());
		$change_tags = array_map($tag_merge, $tags);
		
		// Function to split the  tag!!>--<!!tag_type from above into a tag and tag_type
		$tag_split = function($tag)
		{
			$tag_parts = explode('!!>--<!!', $tag);
			return array('tag_name' => $tag_parts[0], 
						 'tag_type' => $tag_parts[1]);
		};
		$new_tags = array_map($tag_split, array_diff($change_tags, $current_tags));
		
		//Get the tag IDs en batch
		$tag_ids = Model_Tag::get_tags($new_tags);
		
		// Add the tags in one big batch
		if ($tag_ids)
		{
			$query = DB::insert('droplets_tags', array('droplet_id', 'tag_id'));
			foreach ($tag_ids as $tag_id) {
			    $query->values(array($orm_droplet->id, $tag_id['id']));
			}
			try {
			    $result = $query->execute();
			} catch ( Database_Exception $e ) {   
					Kohana::$log->add(Log::ERROR, 'Database error adding tags: '.$e->getMessage());
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
			if ($orm_place AND ! $orm_droplet->has('places', $orm_place))
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
		
		// Flatten the predicates array into a string
		$predicate_str = implode(" ", $predicates);

		// Get the droplets ordered by pub_date in DESC order
		// Limit to only parent items - not comments
		$result = ORM::factory('droplet')
					->where('droplet_processed', '=', 0)
					->where('parent_id', '=', 0)
					->where($predicates[0], $predicates[1], 
						DB::expr($predicates[2].' AND EXISTS (SELECT river_id FROM channel_filters WHERE filter_enabled = 1 AND '.$predicate_str.')'))
					->order_by('id', 'DESC')
					->limit($limit)
					->find_all();
		
		foreach ($result as $droplet)
		{
			$droplets[] = array(
				'id' => $droplet->id,
				'droplet_raw' => $droplet->droplet_raw,
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
	 * Given an array of droplets, populates a buckets array element
	 *
	 * @param array $droplets
	*/
	public static function populate_buckets(& $droplets)
	{		
		if (empty($droplets))
			return;
		
		// Collect droplet IDs into a single array
		$droplet_ids = array();
		foreach ($droplets as & $droplet)
		{
			$droplet['buckets'] = array();
			$droplet_ids[] = $droplet['id'];
		}

		//Query all buckets belonging to the selected droplet IDs
		$query_buckets = DB::select('droplet_id', array('bucket_id', 'id'), 'bucket_name')
					->from('buckets_droplets')
					->join('buckets', 'INNER')
					->on('buckets.id', '=', 'bucket_id')
					->where('droplet_id', 'IN', $droplet_ids);
				
		// Group the buckets per droplet
		$droplet_buckets = array();
		foreach ($query_buckets->execute()->as_array() as $bucket)
		{
			$droplet_id = $bucket['droplet_id'];
			if ( ! isset($droplet_buckets[$droplet_id]))
			{
				$droplet_buckets[$droplet_id] = array();
			}
			unset($bucket['droplet_id']);
			$droplet_buckets[$droplet_id][] = $bucket;
		}
		
		// Assign the buckets to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_buckets[$droplet_id]))
			{
				$droplet['buckets'] = $droplet_buckets[$droplet_id];
			}
		}	    
	}
	
	/**
	 * Given an array of droplets, populates a tags array element
	 *
	 * @param array $droplets
	*/
	public static function populate_tags(& $droplets, $account_id)
	{		
		if (empty($droplets))
			return;
		
		// Collect droplet IDs into a single array
		$droplet_ids = array();
		foreach ($droplets as & $droplet)
		{
			$droplet['tags'] = array();
			$droplet_ids[] = $droplet['id'];
		}

		//Query all tags belonging to the selected droplet IDs
		$query_account = DB::select('droplet_id', array('tag_id', 'id'), 'tag')		            
					->from('account_droplet_tags')
					->join('tags', 'INNER')
					->on('tags.id', '=', 'tag_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('account_id', '=', $account_id)
					->where('deleted', '=', 0);
		
		// Get all deleted droplet tags for the current account
		$query_deleted_tags = DB::select('tag_id')
		    ->from('account_droplet_tags')
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', 'IN', $droplet_ids)
		    ->where('deleted', '=', 1);
		
		$deleted_tag_ids = array();
		foreach ($query_deleted_tags->execute()->as_array() as $deleted)
		{
			$deleted_tag_ids[] = $deleted['tag_id'];
		}

		//Query all tags belonging to the selected droplet IDs
		$query_tags = DB::select('droplet_id', array('tag_id', 'id'), 'tag')
					->union($query_account, TRUE)
					->from('droplets_tags')
					->join('tags', 'INNER')
					->on('tags.id', '=', 'tag_id')
					->where('droplet_id', 'IN', $droplet_ids);
		
		if (count($deleted_tag_ids) > 0)
		{
			$query_tags->where('tags.id', 'NOT IN', $deleted_tag_ids);
		}
					
				
		// Group the tags per droplet
		$droplet_tags = array();
		foreach ($query_tags->execute()->as_array() as $tag)
		{
			$droplet_id = $tag['droplet_id'];
			if ( ! isset($droplet_tags[$droplet_id]))
			{
				$droplet_tags[$droplet_id] = array();
			}
			unset($tag['droplet_id']);
			$droplet_tags[$droplet_id][] = $tag;
		}
		
		// Assign the tags to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_tags[$droplet_id]))
			{
				$droplet['tags'] = $droplet_tags[$droplet_id];
			}
		}
	}

	/**
	 * Given an array of droplets, populates a links array element
	 *
	 * @param array $droplets
	*/
	public static function populate_links(& $droplets)
	{		
		if (empty($droplets))
			return;
		
		// Collect droplet IDs into a single array
		$droplet_ids = array();
		foreach ($droplets as & $droplet)
		{
			$droplet['links'] = array();
			$droplet_ids[] = $droplet['id'];
		}

		//Query all links belonging to the selected droplet IDs
		$query_links = DB::select('droplet_id', array('link_id', 'id'), 'link_full')
					->from('droplets_links')
					->join('links', 'INNER')
					->on('links.id', '=', 'link_id')
					->where('droplet_id', 'IN', $droplet_ids);
				
		// Group the links per droplet
		$droplet_links = array();
		foreach ($query_links->execute()->as_array() as $link)
		{
			$droplet_id = $link['droplet_id'];
			if ( ! isset($droplet_links[$droplet_id]))
			{
				$droplet_links[$droplet_id] = array();
			}
			unset($link['droplet_id']);
			$droplet_links[$droplet_id][] = $link;
		}
		
		// Assign the links to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_links[$droplet_id]))
			{
				$droplet['links'] = $droplet_links[$droplet_id];
			}
		}    
	}

	/**
	 * Given an array of droplets, populates a places array element
	 *
	 * @param array $droplets
	*/
	public static function populate_places(& $droplets)
	{		
		if (empty($droplets))
			return;
		
		// Collect droplet IDs into a single array
		$droplet_ids = array();
		foreach($droplets as & $droplet)
		{
			$droplet['places'] = array();
			$droplet_ids[] = $droplet['id'];
		}

		//Query all places belonging to the selected droplet IDs
		$query_places = DB::select('droplet_id', array('place_id', 'id'), 'place_name')
					->from('droplets_places')
					->join('places', 'INNER')
					->on('places.id', '=', 'place_id')
					->where('droplet_id', 'IN', $droplet_ids);
				
		// Group the places per droplet
		$droplet_places = array();
		foreach ($query_places->execute()->as_array() as $place)
		{
			$droplet_id = $place['droplet_id'];
			if ( ! isset($droplet_places[$droplet_id]))
			{
				$droplet_places[$droplet_id] = array();
			}
			unset($place['droplet_id']);
			$droplet_places[$droplet_id][] = $place;
		}
		
		// Assign the places to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_places[$droplet_id]))
			{
				$droplet['places'] = $droplet_places[$droplet_id];
			}
		}    
	}
	
	/**
	 * Given an array of droplets, populates the discussions
	 *
	 * @param array $droplets Droplets to be populated with comments/discussions
	 */
	public static function populate_discussions(array & $droplets)
	{
		if (empty($droplets))
			return;
		
		$droplet_ids = array();
		foreach ($droplets as & $droplet)
		{
			$droplet['discussions'] = array();
			$droplet_ids[] = $droplet['id'];
		}

		// Query to fetch the comments
		$query = DB::select(array('droplets.id', 'id'), 'droplet_title', 
		        array('droplets.parent_id', 'parent_id'), 
		        array('droplets.channel', 'channel'), 'droplet_content', 
		        'identity_name', 'identity_avatar', 'droplet_date_pub'
		    )
		    ->from('droplets')
		    ->join('identities', 'INNER')
		    ->on('droplets.identity_id', '=', 'identities.id')
		    ->where('droplets.parent_id', 'IN', $droplet_ids)
		    ->order_by('droplet_date_pub', 'ASC');
		 

		 // Group the comments per droplet
		 $comments = array();
		 foreach ($query->execute()->as_array() as $comment)
		 {
		 	$parent_id = $comment['parent_id'];
		 	if ( ! isset($discussions[$parent_id]))
		 	{
		 		$discussions[$parent_id] = array();
		 	}

		 	$comments[$parent_id][] = $comment;
		 }

		 // Assign the comments to the droplets
		 foreach ($droplets as & $droplet)
		 {
		 	$droplet_id = $droplet['id'];
		 	if (isset($comments[$droplet_id]))
		 	{
		 		$droplet['discussions'] = $comments[$droplet_id];
		 	}
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
		$this->__update_score($droplet_array);
	}
	
	/**
	 * Updates a droplet's buckets from an array. 
	 *
	 * @param array
	 * @return void
	 */	
	private function __update_score($droplet_array)
	{
		if ( ! isset($droplet_array['droplet_score']))
			return;
		
		$droplet_score_arr = $droplet_array['droplet_score'];
		
		$droplet_score_orm = ORM::factory('droplet_score')
							->where('droplet_id', '=', $droplet_score_arr['droplet_id'])
							->where('user_id', '=', $droplet_score_arr['user_id'])
							->find();
		
		// Set the values, if a score already exists... change it.
		$droplet_score_orm->droplet_id = $droplet_score_arr['droplet_id'];
		$droplet_score_orm->user_id = $droplet_score_arr['user_id'];
		$droplet_score_orm->score = $droplet_score_arr['score'];
		$droplet_score_orm->save();
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
			// System-generated tag; Do not delete from the DB
			$deleted_orm = ORM::factory('account_droplet_tag');
			$deleted_orm->account_id = $account_id;
			$deleted_orm->droplet_id = $droplet_id;
			$deleted_orm->tag_id = $tag_id;
			$deleted_orm->deleted = 1;

			$deleted_orm->save();
		}
		
		// Checkf for user-defined tag
		$user_tag_orm = $droplet_orm->account_droplet_tags
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', '=', $droplet_id)
		    ->where('tag_id', '=', $tag_id)
		    ->where('deleted', '=', 0)
		    ->find();

		if ($user_tag_orm->loaded())
		{
			$user_tag_orm->delete();
		}
		
		return TRUE;
	}

	/**
	 * Given an array performs UTF-8 encoding of droplet title and droplet content
	 *
	 * @param array $droplet Array representation of the droplet
	 */
	public static function utf8_encode(& $droplet)
	{
		// Exempted encodings
		$exempt_charsets = array('UTF-8', 'ASCII');

		// Encode content and title as utf8 in case they aren't
		$content = $droplet['droplet_content'];
		$title = $droplet['droplet_title'];

		$droplet['droplet_content'] = mb_check_encoding($content, 'UTF-8') 
		    ? $content 
		    : utf8_encode($content);
		
		$droplet['droplet_title'] = mb_check_encoding($title, 'UTF-8') 
		    ? $title 
		    : utf8_encode($title);

	}
}

?>
