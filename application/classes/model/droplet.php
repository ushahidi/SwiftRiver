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
class Model_Droplet extends ORM {
	
	/**
	 * Processing status flags
	 */
	const PROCESSING_STATUS_COMPLETE = 255;
	
	const PROCESSING_STATUS_NEW = 252;
	
	const PROCESSING_FLAG_SEMANTICS = 1;
	
	const PROCESSING_FLAG_LINKS = 2;

	/**
	 * A droplet has and belongs to many links, places, tags and attachments
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'buckets' => array(
			'model' => 'bucket',
			'through' => 'buckets_droplets'
			),
		'links' => array(
			'model' => 'link',
			'through' => 'droplets_links'
			),
		'media' => array(
			'model' => 'media',
			'through' => 'droplets_media'
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
		'account_droplet_media' => array(
 			'model' => 'account_droplet_media'
			),						
		'account_droplet_places' => array(
 			'model' => 'account_droplet_place'
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
	 * @return array Array of new droplets
	 */
	public static function get_droplet_by_hash($droplet_hash)
	{
		return ORM::factory('droplet')
		    ->where('droplet_hash', '=', $droplet_hash)
		    ->find();
	}
	
	/**
	 * Creates a droplets from the given array
	 *
	 * @param array $droplet
	 * @return array
	 */
	public static function create_from_array($droplets)
	{
		if ( ! count($droplets))
			return;
			
	    // Populate identities
		Model_Identity::get_identities($droplets);
		
		// Hash array with droplet_hash as key and index in droplets array that contain that hash
		$droplets_idx = array();
		foreach ($droplets as $key => & $droplet)
		{
			if ( ! isset($droplet['id']))
			{
				$hash = md5($droplet['identity_orig_id'].$droplet['channel'].$droplet['droplet_orig_id']);
				$droplet['droplet_hash'] = $hash;
				if (empty($droplets_idx[$hash]))
				{
					$droplets_idx[$hash] = array();
				}
				$droplets_idx[$hash][] = $key;
			}
		}
		
		// Insert new drops
		$new_droplets = array();
		if ( ! empty($droplets_idx))
		{		
			Swiftriver_Mutex::obtain(get_class(), 3600);
			
			// Find the drops that already exist by their droplet_hash
			$found_query = DB::select('droplet_hash', 'id')
						->from('droplets')
						->where('droplet_hash', 'IN', array_keys($droplets_idx));
			$found = $found_query->execute()->as_array();
			
			// Update the ids of existing drops found in the db and 
			// remove them from droplets_idx to leave new drops
			$new_droplet_count = count($droplets_idx);
			foreach ($found as $hash)
			{
				foreach ($droplets_idx[$hash['droplet_hash']] as $key)
				{
					$droplets[$key]['id'] = $hash['id'];
				}
				$new_droplet_count--;
				unset($droplets_idx[$hash['droplet_hash']]);
			}
			
			if ( ! empty($droplets_idx))
			{
				// Get a range of IDs to be used in inserting the new drops
				$base_id = Model_Droplet::get_ids($new_droplet_count);

				// Insert into the droplets table
				$query = DB::insert('droplets', array('id', 'channel', 'droplet_hash', 'droplet_orig_id', 'droplet_type', 'droplet_title', 'droplet_content', 'droplet_date_pub', 'droplet_date_add', 'identity_id', 'processing_status'));

				foreach ($droplets_idx as $hash => $keys) 
				{
					foreach ($keys as $key)
					{
						$droplets[$key]['id'] = $base_id;
					}
					// PHP has reference issues with array so 
					// we cannot copy the element we have but
					// refeference it in place as below $droplets[$keys[0]]
					// otherwise the element will be overwriten if we use a
					// copy. Sigh.
					$new_droplets[] = $droplets[$keys[0]];
					$query->values(array(
						'id' => $base_id++,
						'channel' => $droplets[$keys[0]]['channel'],
						'droplet_hash' => $droplets[$keys[0]]['droplet_hash'],
						'droplet_orig_id' => $droplets[$keys[0]]['droplet_orig_id'],
						'droplet_type' => $droplets[$keys[0]]['droplet_type'],
						'droplet_title' => $droplets[$keys[0]]['droplet_title'],
						'droplet_content' => $droplets[$keys[0]]['droplet_content'],
						'droplet_date_pub' => $droplets[$keys[0]]['droplet_date_pub'],
						'droplet_date_add' => gmdate("Y-m-d H:i:s", time()),
						'identity_id' => $droplets[$keys[0]]['identity_id'],
						'processing_status' => self::PROCESSING_STATUS_NEW
					));
				}
				$query->execute();
			}
			
			Swiftriver_Mutex::release(get_class());
		}
		
		// Populate metadata IDs into the drops array
		Model_Tag::get_tags($droplets);
		Model_Link::get_links($droplets);
		Model_Place::get_places($droplets);
		
		// Populate the drop's metadata tables
		self::add_metadata($droplets);
		
		return $new_droplets;
	}
	
	/**
	 * Populate the droplet metadata tables.
	 *
	 * @param array $drops Drop array
	 */
	public static function add_metadata(& $drops)
	{
		// Build queries for creating entries in the meta tables droplet_tags, droplet_links and so on
		$river_values = NULL;
		$tag_values = NULL;
		$semantics_complete = array();
		$link_values = NULL;
		$links_complete = array();
		$place_values = NULL;
		foreach ($drops as $drop)
		{
			// Place drops into rivers
			if (isset($drop['river_id']))
			{
				foreach ($drop['river_id'] as $river_id)
				{
					if ($river_values)
					{
						$river_values .= ',';
					}
					$river_values .= '('.$drop['id'].','.$river_id.')';
				}
			}
			
			// Create a query to insert tags into droplets_tags
			if (isset($drop['tags']))
			{
				foreach ($drop['tags'] as $tag)
				{
					if ($tag_values)
					{
						$tag_values .= ',';
					}
					$tag_values .= '('.$drop['id'].','.$tag['id'].')';
				}
			}

			// Find drops that have complete semantic processing
			if (isset($drop['semantics_complete']))
			{
				$semantics_complete[] = $drop['id'];
			}
			
			if (isset($drop['links']))
			{
				foreach ($drop['links'] as $link)
				{
					if ($link_values)
					{
						$link_values .= ',';
					}
					$link_values .= '('.$drop['id'].','.$link['id'].')';
				}
			}
			
			// Find drops that have complete semantic processing
			if (isset($drop['links_complete']))
			{
				$links_complete[] = $drop['id'];
			}
			
			if (isset($drop['places']))
			{
				foreach ($drop['places'] as $place)
				{
					if ($place_values)
					{
						$place_values .= ',';
					}
					$place_values .= '('.$drop['id'].','.$place['id'].')';
				}
			}
		}
		
		
		// Execute the queries created in the last step
		if ($river_values)
		{
			DB::query(Database::INSERT, "INSERT IGNORE INTO `rivers_droplets` (`droplet_id`, `river_id`) VALUES ".$river_values)->execute();
		}
		
		// Update droplets tags
		if ($tag_values)
		{
			DB::query(Database::INSERT, "INSERT IGNORE INTO `droplets_tags` (`droplet_id`, `tag_id`) VALUES ".$tag_values)->execute();
		}
		
		// Update drops that completed semantic processing
		if ( ! empty($semantics_complete))
		{
			DB::update('droplets')
			   ->set(array('processing_status' => DB::expr('processing_status | '.self::PROCESSING_FLAG_SEMANTICS)))
			   ->where("id", "IN", $semantics_complete)
			   ->execute();
		}
		
		// Update droplet links
		if ($link_values)
		{
			DB::query(Database::INSERT, "INSERT IGNORE INTO `droplets_links` (`droplet_id`, `link_id`) VALUES ".$link_values)->execute();
		}
		
		// Update drops that completed link processing
		if ( ! empty($links_complete))
		{
			DB::update('droplets')
			   ->set(array('processing_status' => DB::expr('processing_status | '.self::PROCESSING_FLAG_LINKS)))
			   ->where("id", "IN", $links_complete)
			   ->execute();
		}
		
		// Update droplet places
		if ($place_values)
		{
			DB::query(Database::INSERT, "INSERT IGNORE INTO `droplets_places` (`droplet_id`, `place_id`) VALUES ".$place_values)->execute();
		}
	}


	/**
	 * Adds media to a droplet
	 *
	 * @param Model_Droplet $orm_droplet Droplet ORM reference
	 * @param array $media
	 * @param string $type media type
	 */
	public static function add_media($orm_droplet, $media, $type = 'image')
	{
		$media_reduce = function($media)
		{
			return $media->media;
		};
		
		$new_media = array_diff($media, array_map($media_reduce, $orm_droplet->media->find_all()->as_array()));
		
		$media_map = function($media) use($type)
		{
			return array (
				'media' => $media,
				'media_hash' => hash('md5', $media),
				'media_type' => $type
			);
		};
		
		$media = array_map($media_map, $new_media);

		// Get the media IDs
		$media_ids = Model_Media::get_media($media);
		
		// Add the media to the droplet
		if ($media_ids)
		{
			$query = DB::insert('droplets_media', array('droplet_id', 'media_id'));
			foreach ($media_ids as $media_id)
			{
			    $query->values(array($orm_droplet->id, $media_id['id']));
			}

			try
			{
			    $result = $query->execute();
			}
			catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Database error adding media: '.$e->getMessage());
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
					->where('processing_status', '!=', self::PROCESSING_STATUS_COMPLETE)
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
				'droplet_raw' => $droplet->droplet_content,
				'channel' => $droplet->channel,
				'identity_orig_id' => $droplet->identity->identity_orig_id,
				'identity_username' => $droplet->identity->identity_username,
				'identity_name' => $droplet->identity->identity_name,
				'droplet_orig_id' => $droplet->droplet_orig_id,
				'droplet_type' => $droplet->droplet_type,
				'droplet_title' => $droplet->droplet_title,
				'droplet_content' => $droplet->droplet_content,
				'droplet_locale' => $droplet->droplet_locale,
				'droplet_date_pub' => $droplet->droplet_locale,
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
	 * Given an array of droplets, populates the buckets, tags, links,
	 * media, discussions and locations array elements
	 *
	 * @param array $droplets List of droplets to populate with metadata
	 */
	public static function populate_metadata(& $droplets, $account_id)
	{
		// Populate the buckets array
		Model_Droplet::populate_buckets($droplets);

		// Populate tags array			
		Model_Droplet::populate_tags($droplets, $account_id);
		
		// Populate links array			
		Model_Droplet::populate_links($droplets, $account_id);

		// Populate media array			
		Model_Droplet::populate_media($droplets, $account_id);		
    	
		// Populate places array			
		Model_Droplet::populate_places($droplets, $account_id);
		
		// Populate the discussions array
		Model_Droplet::populate_discussions($droplets);
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
					->where('droplet_id', 'IN', $droplet_ids)
					->where('tags.id', 'NOT IN', $query_deleted_tags);
		
				
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
	public static function populate_links(& $droplets, $account_id)
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
		
		//Query account links belonging to the selected droplet IDs
		$query_account = DB::select('droplet_id', array('link_id', 'id'), 'url')		            
					->from('account_droplet_links')
					->join('links', 'INNER')
					->on('links.id', '=', 'link_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('account_id', '=', $account_id)
					->where('deleted', '=', 0);
		
		// Get all deleted droplet links for the current account
		$query_deleted_links = DB::select('link_id')
		    ->from('account_droplet_links')
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', 'IN', $droplet_ids)
		    ->where('deleted', '=', 1);
		
		//Query all links belonging to the selected droplet IDs
		$query_links = DB::select('droplet_id', array('link_id', 'id'), 'url')
					->union($query_account, TRUE)
					->from('droplets_links')
					->join('links', 'INNER')
					->on('links.id', '=', 'link_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('links.id', 'NOT IN', $query_deleted_links);
				
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
	 * Given an array of droplets, populates a media array element
	 *
	 * @param array $droplets
	*/
	public static function populate_media(& $droplets, $account_id)
	{		
		if (empty($droplets))
			return;
		
		// Collect droplet IDs into a single array
		$droplet_ids = array();
		foreach ($droplets as & $droplet)
		{
			$droplet['media'] = array();
			$droplet_ids[] = $droplet['id'];
		}
		
		//Query account media belonging to the selected droplet IDs
		$query_account = DB::select('droplet_id', array('media_id', 'id'), 'media')		            
					->from('account_droplet_media')
					->join('media', 'INNER')
					->on('media.id', '=', 'media_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('account_id', '=', $account_id)
					->where('deleted', '=', 0);
		
		// Get all deleted droplet media for the current account
		$query_deleted_media = DB::select('media_id')
		    ->from('account_droplet_media')
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', 'IN', $droplet_ids)
		    ->where('deleted', '=', 1);
		
		//Query all media belonging to the selected droplet IDs
		$query_media = DB::select('droplet_id', array('media_id', 'id'), 'media')
					->union($query_account, TRUE)
					->from('droplets_media')
					->join('media', 'INNER')
					->on('media.id', '=', 'media_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('media.id', 'NOT IN', $query_deleted_media);
				
		// Group the media per droplet
		$droplet_media = array();
		foreach ($query_media->execute()->as_array() as $media)
		{
			$droplet_id = $media['droplet_id'];
			if ( ! isset($droplet_media[$droplet_id]))
			{
				$droplet_media[$droplet_id] = array();
			}
			unset($media['droplet_id']);
			$droplet_media[$droplet_id][] = $media;
		}
		
		// Assign the media to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_media[$droplet_id]))
			{
				$droplet['media'] = $droplet_media[$droplet_id];
			}
		}    
	}	

	/**
	 * Given an array of droplets, populates a places array element
	 *
	 * @param array $droplets
	*/
	public static function populate_places(& $droplets, $account_id)
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
		
		//Query account links belonging to the selected droplet IDs
		$query_account = DB::select('droplet_id', array('place_id', 'id'), 'place_name')
					->from('account_droplet_places')
					->join('places', 'INNER')
					->on('places.id', '=', 'place_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('account_id', '=', $account_id)
					->where('deleted', '=', 0);
		
		// Get all deleted droplet links for the current account
		$query_deleted_places = DB::select('place_id')
		    ->from('account_droplet_places')
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', 'IN', $droplet_ids)
		    ->where('deleted', '=', 1);

		//Query all places belonging to the selected droplet IDs
		$query_places = DB::select('droplet_id', array('place_id', 'id'), 'place_name')
					->union($query_account, TRUE)
					->from('droplets_places')
					->join('places', 'INNER')
					->on('places.id', '=', 'place_id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('places.id', 'NOT IN', $query_deleted_places);
				
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
	public static function populate_discussions(& $droplets)
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
		        'identity_name', 'identity_avatar', 
				array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub')
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
		$this->_update_buckets($droplet_array);
		$this->_update_score($droplet_array);
	}
	
	/**
	 * Updates a droplet's buckets from an array. 
	 *
	 * @param array
	 * @return void
	 */	
	private function _update_score($droplet_array)
	{
		if ( ! isset($droplet_array['droplet_score']))
			return;
		
		$droplet_score = $droplet_array['droplet_score'];
		
		$droplet_score_orm = ORM::factory('droplet_score')
							->where('droplet_id', '=', $this->id)
							->where('user_id', '=', $droplet_score['user_id'])
							->find();
		
		// Set the values, if a score already exists... change it.
		$droplet_score_orm->droplet_id = $this->id;
		$droplet_score_orm->user_id = $droplet_score['user_id'];
		$droplet_score_orm->score = $droplet_score['user_score'];
		$droplet_score_orm->save();
	}

	/**
	 * Updates a droplet's buckets from an array. 
	 *
	 * @param array
	 * @return void
	 */	
	private function _update_buckets($droplet_array)
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
		$account_droplet_tag_orm = $droplet_orm->account_droplet_tags
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', '=', $droplet_id)
		    ->where('tag_id', '=', $tag_id)
		    ->where('deleted', '=', 0)
		    ->find();

		if ($account_droplet_tag_orm->loaded())
		{
			$account_droplet_tag_orm->delete();
		}
		
		return TRUE;
	}
	
	/**
	 * Removes the given link from a droplet
	 * @param int $droplet_id ID of the droplet
	 * @param int $link_id ID of the link	
	 * @return boolean
	 */	
	public static function delete_link($droplet_id, $link_id, $account_id)
	{
		$droplet_orm = ORM::factory('droplet', $droplet_id);		
		if ( ! $droplet_orm->loaded())
			return FALSE;
			
		$link_orm = ORM::factory('link', $link_id);
		if ( ! $link_orm->loaded())
			return FALSE;
			
		if ($droplet_orm->has('links', $link_orm)) 
		{
			// System-generated tag; Do not delete from the DB
			$deleted_orm = ORM::factory('account_droplet_link');
			$deleted_orm->account_id = $account_id;
			$deleted_orm->droplet_id = $droplet_id;
			$deleted_orm->link_id = $link_id;
			$deleted_orm->deleted = 1;

			$deleted_orm->save();
		}
		
		// Check for user-defined link
		$account_droplet_link = $droplet_orm->account_droplet_links
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', '=', $droplet_id)
		    ->where('link_id', '=', $link_id)
		    ->where('deleted', '=', 0)
		    ->find();

		if ($account_droplet_link->loaded())
		{
			$account_droplet_link->delete();
		}
		
		return TRUE;
	}
	
	/**
	 * Removes the given link from a droplet
	 * @param int $droplet_id ID of the droplet
	 * @param int $place_id ID of the place
	 * @return boolean
	 */	
	public static function delete_place($droplet_id, $place_id, $account_id)
	{
		$droplet_orm = ORM::factory('droplet', $droplet_id);		
		if ( ! $droplet_orm->loaded())
			return FALSE;
			
		$place_orm = ORM::factory('place', $place_id);
		if ( ! $place_orm->loaded())
			return FALSE;
			
		if ($droplet_orm->has('places', $place_orm)) 
		{
			// System-generated tag; Do not delete from the DB
			$deleted_orm = ORM::factory('account_droplet_place');
			$deleted_orm->account_id = $account_id;
			$deleted_orm->droplet_id = $droplet_id;
			$deleted_orm->place_id = $place_id;
			$deleted_orm->deleted = 1;

			$deleted_orm->save();
		}
		
		// Check for user-defined link
		$account_droplet_place = $droplet_orm->account_droplet_places
		    ->where('account_id', '=', $account_id)
		    ->where('droplet_id', '=', $droplet_id)
		    ->where('place_id', '=', $place_id)
		    ->where('deleted', '=', 0)
		    ->find();

		if ($account_droplet_place->loaded())
		{
			$account_droplet_place->delete();
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
		$identity_name = $droplet['identity_name'];

		$droplet['droplet_content'] = mb_check_encoding($content, 'UTF-8') 
		    ? $content 
		    : utf8_encode($content);
		
		$droplet['droplet_title'] = mb_check_encoding($title, 'UTF-8') 
		    ? $title 
		    : utf8_encode($title);
		
		$droplet['identity_name'] = mb_check_encoding($identity_name, 'UTF-8') 
		    ? $identity_name 
		    : utf8_encode($identity_name);

	}

	/**
	 * Retrieves the list of droplets that matche the specified filters
	 *
	 * @param array $filters Set of filters to apply to the droplets list
	 * @param int $user_id ID of the user initiating the search
	 * @param int $page Page number - for calculating the offset of the resultest
	 */
	public static function search($filters, $user_id, $page = 1)
	{
		$user_orm = ORM::factory('user', $user_id);
		$droplets = array();

		if ($user_orm->loaded())
		{
			// Sanity check for the page number
			$page = (empty($page)) ? 1 : $page;

			// Build Buckets Query
			$query = DB::select(array('droplets.id', 'id'), 
				    array(DB::expr('UNIX_TIMESTAMP(droplets.droplet_date_add)'), 'sort_id'),
				    'droplet_title', 'droplet_content', 
				    'droplets.channel','identity_name', 'identity_avatar', 
				    array(DB::expr('DATE_FORMAT(droplet_date_pub, "%b %e, %Y %H:%i UTC")'),'droplet_date_pub'),
				    array(DB::expr('SUM(all_scores.score)'),'scores'), array('user_scores.score','user_score'))
				->from('droplets')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')
				->join(array('droplet_scores', 'all_scores'), 'LEFT')
			    ->on('all_scores.droplet_id', '=', 'droplets.id')
			    ->join(array('droplet_scores', 'user_scores'), 'LEFT')
			    ->on('user_scores.droplet_id', '=', DB::expr('droplets.id AND user_scores.user_id = '.$user_id))
			    ->where('droplets.droplet_processed', '=', 1);

			self::apply_droplets_filter($query, $filters);

			$query->group_by('droplets.id')
			    ->order_by('droplets.droplet_date_add', 'DESC')
			    ->limit(20)
			    ->offset(20 * ($page - 1));

			$droplets = $query->execute()->as_array();

			Model_Droplet::populate_metadata($droplets, $user_orm->account->id);
		}

		return $droplets;
	}


	/**
	 * Applies a set of filters to the specified Database_Query_Select object
	 *
	 * @param Database_Query_Select $query Object to which the filtering predicates shall be added
	 * @param array $filters Set of filters to apply
	 */
	public static function apply_droplets_filter(& $query, $filters)
	{
		 // Check if the filter are empty
		if (empty($filters))
			return;

		if ( ! empty($filters['channel']))
		{
			$query->where('droplets.channel', 'IN', $filters['channel']);
		}
		
		if ( ! empty($filters['tags']))
		{
			$query->join('droplets_tags', 'INNER')
				->on('droplets_tags.droplet_id', '=', 'droplets.id')
				->join('tags', 'INNER')
				->on('droplets_tags.tag_id', '=', 'tags.id')
				->where('tag_canonical', 'IN', $filters['tags']);
		}

		if ( ! empty($filters['places']))
		{
			$query->join('droplets_places', 'INNER')
				->on('droplets_places.droplet_id', '=', 'droplets.id')
				->join('places', 'INNER')
				->on('droplets_places.place_id', '=', 'places.id')
				->where('place_name_canonical', 'IN', $filters['places']);
		}
		
		if ( ! empty($filters['start_date']))
		{
			$start_date = array_shift($filters['start_date']);
			$start_date = new DateTime($start_date);
			$query->where('droplets.droplet_date_pub', '>=', $start_date->format('Y-m-d'));
		}

		if ( ! empty($filters['end_date']))
		{
			$end_date = array_shift($filters['end_date']);
			$end_date = new DateTime($end_date);
			$query->where('droplets.droplet_date_pub', '<=', $end_date->format('Y-m-d'));
		}
	}
	
	/**
	 * Get a range of IDs to be used for inserting drops
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lowe limit of the range requested
	 */
	public static function get_ids($num)
	{
	    // Build River Query
		$query = DB::select(array(DB::expr("NEXTVAL('droplets',$num)"), 'id'));
		    
		return intval($query->execute()->get('id', 0));
	}
}

?>
