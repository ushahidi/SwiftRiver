<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Droplets
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_Droplet extends ORM {
	
	/**
	 * Processing status flags
	 */
	const PROCESSING_STATUS_COMPLETE = 255;
	
	const PROCESSING_STATUS_NEW = 252;
	
	const PROCESSING_FLAG_SEMANTICS = 1;
	
	const PROCESSING_FLAG_MEDIA = 2;

	/**
	 * A droplet has and belongs to many links, places, tags and attachments
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'rivers' => array(
			'model' => 'river',
			'through' => 'rivers_droplets'
			),
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
		'droplet_scores' => array(),
		'droplet_comments' => array()
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
				$query = DB::insert('droplets', 
					array('id', 'channel', 'droplet_hash', 'droplet_orig_id',
					    'droplet_type', 'droplet_title', 'droplet_content',
					    'droplet_date_pub', 'droplet_date_add', 'identity_id',
					    'processing_status'
					));

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
		Model_Media::get_media($droplets);
		
		// Populate the drop's metadata tables
		self::add_metadata($droplets);
		
		return array($droplets, $new_droplets); 
	}
	
	/**
	 * Populate the droplet metadata tables.
	 *
	 * @param array $drops Drop array
	 */
	public static function add_metadata(& $drops)
	{
		// Build queries for creating entries in the meta tables droplet_tags, droplet_links and so on
		$drops_idx = array();
		$river_check_query = NULL;
		$tags_ref = array();
		$tag_values = NULL;
		$tag_check_query = NULL;
		$semantics_complete = array();
		$link_values = NULL;
		$drop_links = NULL;
		$media_values = NULL;
		$drop_images = NULL;
		$media_thumbnail_values = NULL;
		$media_complete = array();
		$place_values = NULL;
		$place_check_query = NULL;
		foreach ($drops as $key => $drop)
		{
			// Create an in memory drop reference
			$drops_idx[$drop['id']] = $key;
			
			// Place drops into rivers
			if (isset($drop['river_id']))
			{
				foreach ($drop['river_id'] as $river_id)
				{
					// Subquery to find new river drops
					$subquery = DB::select(array(DB::expr($drop['id']), 'droplet_id'), 
										   array(DB::expr($river_id), 'river_id'));
					if ( ! $river_check_query)
					{
						$river_check_query = $subquery;
					}
					else
					{
						$river_check_query = $subquery->union($river_check_query, TRUE);
					}
				}
			}
			
			// Create a query to insert tags into droplets_tags
			if (isset($drop['tags']))
			{
				foreach ($drop['tags'] as $tag)
				{
					// Create an in memory tag id -> tag detail reference
					$tags_ref[$tag['id']] = array(
						'tag_name' => $tag['tag_name'],
						'tag_type' => $tag['tag_type']
					);
					
					// Values for the drop tags insert query
					if ($tag_values)
					{
						$tag_values .= ',';
					}
					$tag_values .= '('.$drop['id'].','.$tag['id'].')';
					
					// Subquery to find new tags
					$subquery = DB::select(array(DB::expr($drop['id']), 'droplet_id'), 
										   array(DB::expr($tag['id']), 'tag_id'));
					if ( ! $tag_check_query)
					{
						$tag_check_query = $subquery;
					}
					else
					{
						$tag_check_query = $subquery->union($tag_check_query, TRUE);
					}
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
					
					// Store drop original link for updating the droplets table
					if (isset($link['id']) AND isset($link['original_url']) AND $link['original_url'])
					{
						if ($drop_links)
						{
							$drop_links .= ' union all ';
						}
						$drop_links .= 'select '.$link['id'].' drop_link, '.$drop['id'].' id';
					}
				}
			}
			
			if (isset($drop['media']))
			{
				foreach ($drop['media'] as $media)
				{
					if ($media_values)
					{
						$media_values .= ',';
					}
					$media_values .= '('.$drop['id'].','.$media['id'].')';
					
					if ($media['droplet_image'])
					{
						if ($drop_images)
						{
							$drop_images .= ' union all ';
						}
						$drop_images .= 'select '.$media['id'].' droplet_image, '.$drop['id'].' id';
					}
					
					if (isset($media['thumbnails']))
					{
						foreach ($media['thumbnails'] as $thumbnail)
						{
							if ($media_thumbnail_values)
							{
								$media_thumbnail_values .= ',';
							}
							$media_thumbnail_values .= '('.$media['id']
													   .','.$thumbnail['size']
													   .",'".addslashes($thumbnail['url'])
													   ."')";
						}
					}
				}
			}
			
			// Find drops that have completed media processing
			if (isset($drop['media_complete']))
			{
				$media_complete[] = $drop['id'];
			}
			
			if (isset($drop['places']))
			{
				foreach ($drop['places'] as $place)
				{
					// Create an in memory tag id -> tag detail reference
					$places_ref[$place['id']] = array(
						'place_name' => $place['place_name']
					);
					
					if ($place_values)
					{
						$place_values .= ',';
					}
					$place_values .= '('.$drop['id'].','.$place['id'].')';
					
					// Subquery to find new places
					$subquery = DB::select(array(DB::expr($drop['id']), 'droplet_id'), 
										   array(DB::expr($place['id']), 'place_id'));
					if ( ! $place_check_query)
					{
						$place_check_query = $subquery;
					}
					else
					{
						$place_check_query = $subquery->union($place_check_query, TRUE);
					}
				}
			}
		}
		
		// Find river drops already in the DB
		$existing_river_drops = DB::select('droplet_id', 'river_id')
									->from('rivers_droplets')
									->where('droplet_id', 'IN', array_keys($drops_idx))
									->execute()
									->as_array();
		
		// Find the new river drops we are just about to add
		$new_river_drops = NULL;
		$max_river_drop_ids = array();
		if ($river_check_query)
		{
			Swiftriver_Mutex::obtain('rivers_droplets', 3600);
			
			$sub = DB::select('droplet_id', 'river_id')
						->from('rivers_droplets')
						->where('droplet_id', 'IN', array_keys($drops_idx));
			$new_river_drops = DB::select('droplet_id', 'river_id')
						->distinct(TRUE)
						->from(array($river_check_query, 'a'))
						->where(DB::expr('(`droplet_id`, `river_id`)'), 'NOT IN', $sub)
						->execute()
						->as_array();
			
			if ( ! empty($new_river_drops))
			{
				$base_id = Model_Droplet::get_ids(count($new_river_drops), 'rivers_droplets');
				
				// Insert into the rivers_droplets table
				$query = DB::insert('rivers_droplets', 
					array('id', 'river_id', 'droplet_id', 'droplet_date_pub'));

				foreach ($new_river_drops as $new_river_drop)
				{
					$drops_key = $drops_idx[$new_river_drop['droplet_id']];
					$date_pub = $drops[$drops_key]['droplet_date_pub'];
					$river_id = $new_river_drop['river_id'];
					$id = $base_id++;
					$query->values(array(
						'id' => $id,
						'river_id' => $river_id,
						'droplet_id' => $new_river_drop['droplet_id'],
						'droplet_date_pub' => $date_pub
					));
					
					if ( ! isset($max_river_drop_ids[$river_id]))
					{
						$max_river_drop_ids[$river_id] = array(
							'max_id' => $id,
							'count' => 1
						);
					}
					else
					{
						$max_river_drop_ids[$river_id]['count'] += 1;
						if ($id > $max_river_drop_ids[$river_id]['max_id'])
						{
							$max_river_drop_ids[$river_id]['max_id'] = $id;
						}
					}
				}

				$query->execute();
								
				$max_river_drops_query = NULL;
				foreach ($max_river_drop_ids as $key => $value)
				{
					if ($max_river_drops_query)
					{
						$max_river_drops_query .= ' union all ';
					}
					$max_river_drops_query .= 'select '.$key.' id, '.$value['max_id'].' max_id, '.$value['count'].' cnt';
				}
				
				// Update river max_drop_id
				$update_rivers_sql = "UPDATE `rivers` JOIN (".$max_river_drops_query.") a "
				    ."USING (`id`) SET `rivers`.`max_drop_id` = `a`.`max_id` "
					."WHERE `rivers`.`max_drop_id` < `a`.`max_id`";
				DB::query(Database::UPDATE, $update_rivers_sql)->execute();
				
				// Update drop count
				$update_rivers_sql = "UPDATE `rivers` JOIN (".$max_river_drops_query.") a "
				    ."USING (`id`) SET `rivers`.`drop_count` = `rivers`.`drop_count` + `a`.`cnt` ";
				DB::query(Database::UPDATE, $update_rivers_sql)->execute();
			}
			
			Swiftriver_Mutex::release('rivers_droplets');
		}
				
		// Find the new drop tags
		$new_drop_tags = array();
		if ($tag_check_query)
		{
			$sub = DB::select('droplet_id', 'tag_id')
						->from('droplets_tags')
						->where('droplet_id', 'IN', array_keys($drops_idx));
			$new_tags = DB::select('droplet_id', 'tag_id')
						->from(array($tag_check_query, 'a'))
						->where(DB::expr('(`droplet_id`, `tag_id`)'), 'NOT IN', $sub)
						->execute()
						->as_array();
			foreach ($new_tags as $new_tag)
			{
				if ( ! isset($new_drop_tags[$new_tag['droplet_id']]))
				{
					$new_drop_tags[$new_tag['droplet_id']] = array();
				}
				$new_drop_tags[$new_tag['droplet_id']][] = array(
					'id' => $new_tag['tag_id'],
					'tag' => $tags_ref[$new_tag['tag_id']]['tag_name'],
					'tag_type' => $tags_ref[$new_tag['tag_id']]['tag_type']
				);;
			}
		}
		
		
		// Update droplets tags
		$all_drop_tags = array();
		if ($tag_values)
		{
			$insert_tags_sql = "INSERT IGNORE INTO `droplets_tags` (`droplet_id`, `tag_id`) "
			    ."VALUES ".$tag_values;

			DB::query(Database::INSERT, $insert_tags_sql)
			    ->execute();
			
			// Get all tags(new + existing) for drops that have just been added
			// to a river
			if ($new_river_drops)
			{
				$drop_tags = DB::select('droplet_id', 'tags.id', 'tag', 'tag_type')
											->from('droplets_tags')
											->join('tags', 'INNER')
										    ->on('droplets_tags.tag_id', '=', 'tags.id')
											->where('droplet_id', 'IN', array_keys($drops_idx))
											->execute()
											->as_array();
				foreach ($drop_tags as $drop_tag)
				{
					if ( ! isset($all_drop_tags[$drop_tag['droplet_id']]))
					{
						$all_drop_tags[$drop_tag['droplet_id']] = array();
					}
					$all_drop_tags[$drop_tag['droplet_id']][] = array(
						'id' => $drop_tag['id'],
						'tag' => $drop_tag['tag'],
						'tag_type' => $drop_tag['tag_type']
					);
				}
			}
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
			$insert_links_sql = "INSERT IGNORE INTO `droplets_links` (`droplet_id`, `link_id`) "
			    . "VALUES ".$link_values;

			DB::query(Database::INSERT, $insert_links_sql)
			    ->execute();
		}
		
		// Set the drop's original url
		if ($drop_links)
		{
			$update_orig_url_sql = "UPDATE `droplets` JOIN (".$drop_links.") a "
			    ."USING (`id`) SET `droplets`.`original_url` = `a`.`drop_link`";

			DB::query(Database::UPDATE, $update_orig_url_sql)->execute();
		}
		
		// Update droplet media
		if ($media_values)
		{
			$insert_media_sql = "INSERT IGNORE INTO `droplets_media` (`droplet_id`, `media_id`) "
			    ."VALUES ".$media_values;

			DB::query(Database::INSERT, $insert_media_sql)->execute();
		}
		
		// Insert thumbnails
		if ($media_thumbnail_values)
		{
			$insert_thumbnail_sql = "INSERT IGNORE INTO `media_thumbnails` (`media_id`, `size`, `url`) "
			    ."VALUES ".$media_thumbnail_values;

			DB::query(Database::INSERT, $insert_thumbnail_sql)->execute();
		}
		
		// Set drop image
		if ($drop_images)
		{
			$update_images_sql = "UPDATE `droplets` JOIN (".$drop_images.") a "
			    ."USING (`id`) SET `droplets`.`droplet_image` = `a`.`droplet_image`";

			DB::query(Database::UPDATE, $update_images_sql)->execute();
		}
		
		// Update drops that completed media processing
		if ( ! empty($media_complete))
		{
			DB::update('droplets')
			   ->set(array('processing_status' => DB::expr('processing_status | '.self::PROCESSING_FLAG_MEDIA)))
			   ->where("id", "IN", $media_complete)
			   ->execute();
		}
		
		// Find the new drop places
		$new_drop_places = array();
		if ($place_check_query)
		{
			$sub = DB::select('droplet_id', 'place_id')
						->from('droplets_places')
						->where('droplet_id', 'IN', array_keys($drops_idx));
			$new_places = DB::select('droplet_id', 'place_id')
						->from(array($place_check_query, 'a'))
						->where(DB::expr('(`droplet_id`, `place_id`)'), 'NOT IN', $sub)
						->execute()
						->as_array();
			foreach ($new_places as $new_place)
			{
				if ( ! isset($new_drop_places[$new_place['droplet_id']]))
				{
					$new_drop_places[$new_place['droplet_id']] = array();
				}
				$new_drop_places[$new_place['droplet_id']][] = array(
					'id' => $new_place['place_id'],
					'tag' => $places_ref[$new_place['place_id']]['place_name'],
					'tag_type' =>'place'
				);;
			}
		}
		
		// Update droplet places
		$all_drop_places = array();
		if ($place_values)
		{
			$insert_places_sql = "INSERT IGNORE INTO `droplets_places` (`droplet_id`, `place_id`) "
			    ."VALUES ".$place_values;

			DB::query(Database::INSERT, $insert_places_sql)
			    ->execute();
			
			// Get all places(new + existing) for drops that have just been added
			// to a river
			if ($new_river_drops)
			{
				$drop_places = DB::select('droplet_id', 'places.id', 'place_name')
											->from('droplets_places')
											->join('places', 'INNER')
										    ->on('droplets_places.place_id', '=', 'places.id')
											->where('droplet_id', 'IN', array_keys($drops_idx))
											->execute()
											->as_array();
				foreach ($drop_places as $drop_place) {
					if ( ! isset($all_drop_places[$drop_place['droplet_id']])) {
						$all_drop_places[$drop_place['droplet_id']] = array();
					}
					$all_drop_places[$drop_place['droplet_id']][] = array(
						'id' => $drop_place['id'],
						'tag' => $drop_place['place_name'],
						'tag_type' => 'place'
					);
				}
			}
		}
		
		// Update trends
		$trends = array();		
		// Update for drops already in the DB but we are adding new tags
		if ($existing_river_drops)
		{
			$trends = array_merge($trends, 
								  self::get_tag_trends($existing_river_drops, 
													   $new_drop_tags, 
													   $drops,
													   $drops_idx));
													
			$trends = array_merge($trends, 
								  self::get_tag_trends($existing_river_drops, 
													   $new_drop_places, 
													   $drops,
													   $drops_idx));
		}
		
		// Update for drops that already exist in the DB with tags but we are adding
		// the drop into a new river
		if ($new_river_drops)
		{
			$trends = array_merge($trends, 
								  self::get_tag_trends($new_river_drops, 
													   $all_drop_tags, 
													   $drops,
													   $drops_idx));
													

			$trends = array_merge($trends, 
								  self::get_tag_trends($new_river_drops, 
													   $all_drop_places, 
													   $drops,
													   $drops_idx));
		}
		
		if ( ! empty($trends))
		{
			Model_River_Tag_Trend::create_from_array($trends);
		}
	}
	
	/**
	 * Return a trend array for the given tags and river_drops
	 *
	 */
	private static function get_tag_trends($river_drops, $tags, $drops, $drops_idx)
	{
		$trends = array();
		foreach($river_drops as $river_drop)
		{
			if (isset($tags[$river_drop['droplet_id']]))
			{
				foreach ($tags[$river_drop['droplet_id']] as $tag)
				{
					$drops_key = $drops_idx[$river_drop['droplet_id']];
					$date_pub = $drops[$drops_key]['droplet_date_pub'];
					$date_pub = date_format(date_create($date_pub), 'Y-m-d H:00:00');						
					$hash = md5($river_drop['river_id'].
								$date_pub.
								$tag['tag'].
								$tag['tag_type']);
					if ( ! isset($trends[$hash]))
					{
						$trends[$hash] = array(
							'river_id' => $river_drop['river_id'],
							'date_pub' => $date_pub,
							'tag' => $tag['tag'],
							'tag_type' => $tag['tag_type'],
							'count' => 1
						);
					}
					else
					{
						$trends[$hash]['count'] = $trends[$hash]['count'] + 1;
					}
				}
			}
		}
		return $trends;
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
				'droplet_date_pub' => $droplet->droplet_date_pub,
				'river_id' => array_map (function($river) {
					return $river->id;
				} , $droplet->rivers->find_all()->as_array())
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
		$query_account = DB::select('droplet_id', array('tag_id', 'id'), 'tag', 'tag_canonical')		            
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
		$query_tags = DB::select('droplet_id', array('tag_id', 'id'), 'tag', 'tag_canonical')
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
		$query_account = DB::select('droplet_id', array('media.id', 'id'), 
										array('media.url', 'url'), 'type',
										array('media_thumbnails.size', 'thumbnail_size'), 
										array('media_thumbnails.url', 'thumbnail_url')
									)
					->from('account_droplet_media')
					->join('media', 'INNER')
					->on('media.id', '=', 'media_id')
					->join('media_thumbnails', 'LEFT')
					->on('media_thumbnails.media_id', '=', 'media.id')
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
		$query_media = DB::select('droplet_id', array('media.id', 'id'), 
										array('media.url', 'url'), 'type',
										array('media_thumbnails.size', 'thumbnail_size'), 
										array('media_thumbnails.url', 'thumbnail_url')
								)
					->union($query_account, TRUE)
					->from('droplets_media')
					->join('media', 'INNER')
					->on('media.id', '=', 'media_id')
					->join('media_thumbnails', 'LEFT')
					->on('media_thumbnails.media_id', '=', 'media.id')
					->where('droplet_id', 'IN', $droplet_ids)
					->where('media.id', 'NOT IN', $query_deleted_media);
				
		// Group the thumbnails per url
		$media = array();
		foreach ($query_media->execute()->as_array() as $m)
		{
			if ( ! isset($media[$m['url']]))
			{
				// Media can belong to multiple drops
				$media[$m['url']]['droplet_ids'] = array();
			}
			$media[$m['url']]['droplet_ids'][] = $m['droplet_id'];
			$media[$m['url']]['id'] = $m['id'];
			$media[$m['url']]['url'] = $m['url'];
			$media[$m['url']]['type'] = $m['type'];
			if ( ! empty($m['thumbnail_url']))
			{
				$media[$m['url']]['thumbnails'][$m['thumbnail_size']] = $m['thumbnail_url'];
			}
			unset($m['thumbnail_size']);
			unset($m['thumbnail_url']);
		}
		
		// Group the media per droplet
		$droplet_media = array();
		foreach ($media as $m)
		{
			
			$ids = $m['droplet_ids'];
			unset($m['droplet_ids']);
			foreach ($ids as $droplet_id)
			{
				if ( ! isset($droplet_media[$droplet_id]))
				{
					$droplet_media[$droplet_id]['media'] = array();
				}
				$droplet_media[$droplet_id]['media'][] = $m;
			}
		}
		
		// Get the droplet image
		//Query account media belonging to the selected droplet IDs
		$query_drop_image = DB::select(array('droplets.id', 'droplet_id'), array('media.id', 'id'), 
										array('media.url', 'url'),
										array('media_thumbnails.size', 'thumbnail_size'), 
										array('media_thumbnails.url', 'thumbnail_url')
										)		            
					->from('droplets')
					->join('media', 'INNER')
					->on('droplets.droplet_image', '=', 'media.id')
					->join('media_thumbnails', 'LEFT')
					->on('media_thumbnails.media_id', '=', 'media.id')
					->where('droplets.id', 'IN', $droplet_ids);
					
		foreach ($query_drop_image->execute()->as_array() as $drop_image)
		{
			$droplet_id = $drop_image['droplet_id'];
			$droplet_media[$droplet_id]['drop_image']['id'] = $drop_image['id'];
			$droplet_media[$droplet_id]['drop_image']['url'] = $drop_image['url'];
			if ( ! empty($drop_image['thumbnail_url']))
			{
				$droplet_media[$droplet_id]['drop_image']['thumbnails'][$drop_image['thumbnail_size']] = $drop_image['thumbnail_url'];
			}
		}
		
		// Assign the media to the droplets
		foreach ($droplets as & $droplet)
		{
			$droplet_id = $droplet['id'];
			if (isset($droplet_media[$droplet_id]))
			{
				if (isset($droplet_media[$droplet_id]['drop_image']))
				{
					$droplet['droplet_image'] =  $droplet_media[$droplet_id]['drop_image'];
				}
				$droplet['media'] = $droplet_media[$droplet_id]['media'];
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
		$query_account = DB::select('droplet_id', array('place_id', 'id'), 'place_name', 'place_name_canonical')
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
		$query_places = DB::select('droplet_id', array('place_id', 'id'), 'place_name', 'place_name_canonical')
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
	 * Updates a droplet from an array. 
	 *
	 * @param array
	 * @return void
	 */
	public function update_from_array($droplet_array, $logged_in_user_id) 
	{
		$this->_update_buckets($droplet_array, $logged_in_user_id);
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
	private function _update_buckets($droplet_array, $logged_in_user_id)
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
			
			if ( ! $bucket_orm->is_owner($logged_in_user_id))
			{
				throw new HTTP_Exception_403();
			}
			
			if ($bucket_orm->loaded())
			{
				$this->add('buckets', $bucket_orm);

				$event_data = array('droplet_id' => $this->id, 'bucket_id' => $bucket_orm->id);
				Swiftriver_Event::run('swiftriver.bucket.droplet.add', $event_data);
			}
		}
		
		// Remove droplet for the delete buckets
		foreach ($delete_buckets as $delete_bucket_id)
		{
			$bucket_orm = ORM::factory('bucket', $delete_bucket_id);
			
			if ( ! $bucket_orm->is_owner($logged_in_user_id))
			{
				throw new HTTP_Exception_403();
			}
			
			if ($this->has('buckets', $bucket_orm))
			{
				$this->remove('buckets', $bucket_orm);

				$event_data = array('droplet_id' => $this->id, 'bucket_id' => $bucket_orm->id);
				Swiftriver_Event::run('swiftriver.bucket.droplet.remove', $event_data);
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
	 * @param int $page Page number - for calculating the offset of the resultset
	 * @param bool $photos - When TRUE, filter out only those droplets with photos
	 */
	public static function search($filters, $user_id, $page = 1, $photos = FALSE)
	{
		$user_orm = ORM::factory('user', $user_id);
		$droplets = array();

		if ($user_orm->loaded())
		{
			// Get the current date
			$today = new DateTime(date('Y-m-d'));
			$start_date = $today->sub(new DateInterval('P14D'));

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
			    ->where('droplets.parent_id', '=', 0)
			    ->where('droplets.droplet_date_add', '>=', $start_date->format('Y-m-d H:i:s'));

			self::apply_droplets_filter($query, $filters, $user_id);

			if ($photos)
			{
				$query->where('droplets.droplet_image', '>', 0);
			}

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
	 * @param Database_Query_Select $query Object to which the filtering 
	 *        predicates shall be added
	 * @param array $filters Set of filters to apply
	 * @param int $user_id ID of the user. When specified, applies the user defined places and tags
	 *        as additional filters
	 * @param ORM $orm_instance When specified, filters the user defined places and tags
	 *        at the bucket/river level. This parameter is only used when the user_id parameter has
	 *        been specified
	 */
	public static function apply_droplets_filter(& $query, $filters, $user_id = NULL, $orm_instance = NULL)
	{
		 // Check if the filter are empty
		if (empty($filters))
			return;

		if ( ! empty($filters['channel']))
		{
			$query->where('droplets.channel', 'IN', $filters['channel']);
		}
		
		// Create a user object
		$user = ( ! empty($user_id)) ? ORM::factory('user', $user_id) : NULL;

		// Get drops manually tagged by the user
		$user_tagged_drops = array();
		if ( ! empty($filters['tags']))
		{
			$user_tags_query = NULL;
			if ( ! empty($user) AND $user->loaded())
			{
				$user_tags_query = DB::select('account_droplet_tags.droplet_id')
				    ->from('account_droplet_tags')
				    ->join('tags', 'INNER')
				    ->on('account_droplet_tags.tag_id', '=', 'tags.id')
				    ->join('droplets', 'INNER')
				    ->on('account_droplet_tags.droplet_id', '=', 'droplets.id');
				
				// ORM instance specified?
				if ($orm_instance instanceof Model_River AND $orm_instance->loaded())
				{
					$user_tags_query->join('rivers_droplets', 'INNER')
					    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
					    ->where('rivers_droplets.river_id', '=', $orm_instance->id);
				}
				elseif ($orm_instance instanceof Model_Bucket AND $orm_instance->loaded())
				{
					$user_tags_query->join('buckets_droplets', 'INNER')
					    ->on('buckets_droplets.droplet_id', '=', 'droplets.id')
					    ->where('buckets_droplets.bucket_id', '=', $orm_instance->id);
				}

				$user_tags_query->where('account_droplet_tags.account_id', '=', $user->account->id)
				    ->where('account_droplet_tags.deleted', '=', 0)
				    ->where('tags.tag_canonical', 'IN', $filters['tags']);

			}

			// Generate the final query
			$final_query = self::_build_final_filter_query($orm_instance, $user_tags_query, $filters, 'tags');
			$user_tagged_drops = $final_query->execute()->as_array();
		}


		// Places filter
		if ( ! empty($filters['places']))
		{
			$user_places_query = NULL;
			if ( ! empty($user) AND $user->loaded())
			{
				$user_places_query = DB::select('account_droplet_places.droplet_id')
				    ->from('account_droplet_places')
				    ->join('places', 'INNER')
				    ->on('account_droplet_places.place_id', '=', 'places.id')
				    ->join('droplets', 'INNER')
				    ->on('account_droplet_places.droplet_id', '=', 'droplets.id');

				// ORM instance specified?
				if ($orm_instance instanceof Model_River AND $orm_instance->loaded())
				{
					$user_places_query->join('rivers_droplets', 'INNER')
					    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
					    ->where('rivers_droplets.river_id', '=', $orm_instance->id);
				}
				elseif ($orm_instance instanceof Model_Bucket AND $orm_instance->loaded())
				{
					$user_tags_query->join('buckets_droplets', 'INNER')
					    ->on('buckets_droplets.droplet_id', '=', 'droplets.id')
					    ->where('buckets_droplets.bucket_id', '=', $orm_instance->id);
				}

				$user_places_query->where('account_droplet_places.account_id', '=', $user->account->id)
				    ->where('account_droplet_places.deleted', '=', 0)
				    ->where('places.place_name_canonical', 'IN', $filters['places']);
			}

			$final_query = self::_build_final_filter_query($orm_instance, $user_places_query, $filters, 'places');
			$user_tagged_drops = array_merge(
				$user_tagged_drops,
				$final_query->execute()->as_array()
			);
		}

		// Apply the drops filter
		if (count($user_tagged_drops) > 0)
		{
			$query->where('droplets.id', 'IN', $user_tagged_drops);
		}

		
		if ( ! empty($filters['start_date']))
		{
			$start_date = array_shift($filters['start_date']);
			$start_date = new DateTime($start_date);
			$query->where('droplets.droplet_date_pub', '>=', $start_date->format('Y-m-d %H:%i:%s'));
		}

		if ( ! empty($filters['end_date']))
		{
			$end_date = array_shift($filters['end_date']);
			$end_date = new DateTime($end_date);
			$query->where('droplets.droplet_date_pub', '<=', $end_date->format('Y-m-d %H:%i:%s'));
		}
	}


	/**
	 * Internal helper function for generating the final filtering query
	 *
	 * @param ORM                   $orm_instance
	 * @param Database_Query_Select $sub_query
	 * @param array                 $filters
	 * @param string                $filter_type
	 *
	 * @return Database_Query_Select
	 */
	private static function _build_final_filter_query($orm_instance, $sub_query, $filters, $filter_type)
	{
		$final_query = DB::select(array('droplets.id', 'droplet_id'));
		if ($sub_query)
		{
			$final_query->union($sub_query, TRUE);
		}

		$final_query->from('droplets');

		// Check the filter type
		if ($filter_type === 'tags')
		{
		    $final_query->join('droplets_tags', 'INNER')
		        ->on('droplets_tags.droplet_id', '=', 'droplets.id')
			    ->join('tags', 'INNER')
			    ->on('droplets_tags.tag_id', '=', 'tags.id');
		}
		elseif ($filter_type === 'places')
		{
			$final_query->join('droplets_places', 'INNER')
			    ->on('droplets_places.droplet_id', '=', 'droplets.id')
			    ->join('places', 'INNER')
			    ->on('droplets_places.place_id', '=', 'places.id');
		}


		// Check the ORM instance class
		if ($orm_instance instanceof Model_River AND $orm_instance->loaded())
		{
			$final_query->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->where('rivers_droplets.river_id', '=', $orm_instance->id);
		}
		elseif ($orm_instance instanceof Model_Bucket AND $orm_instance->loaded())
		{
			$final_query->join('buckets_droplets', 'INNER')
			    ->on('buckets_droplets.droplet_id', '=', 'droplets.id')
			    ->where('buckets_droplets.bucket_id', '=', $orm_instance->id);
		}

		if ($filter_type === 'tags')
		{
			$final_query->where('tags.tag_canonical', 'IN', $filters['tags']);
		}
		elseif ($filter_type === 'places')
		{
			$final_query->where('places.place_name_canonical', 'IN', $filters['places']);
		}

		return $final_query;

	}
	
	/**
	 *
	 * @param int $droplet_id Database ID of the drop
	 * @param int $last_id Pagination reference point
	 * @param boolean $newer Flag to get newer drops than last_id when true
	 * @return array
	 */
	public static function get_comments($drop_id, $last_id = PHP_INT_MAX, $newer = FALSE)
	{
		$query = DB::select(array('droplet_comments.id', 'id'), 
		        array('droplet_comments.droplet_id', 'droplet_id'), 'comment_text', 'deleted',
		        array('users.id', 'identity_user_id'),
		        array('users.name', 'identity_name'), array('users.email', 'identity_email'), 
		        array(DB::expr('DATE_FORMAT(date_added, "%b %e, %Y %H:%i UTC")'),'date_added')
		    )
		    ->from('droplet_comments')
		    ->join('users', 'INNER')
		    ->on('droplet_comments.user_id', '=', 'users.id')
		    ->where('droplet_comments.droplet_id', '=', $drop_id)
			->limit(20);
		
		if ($newer)
		{
			$query->where('droplet_comments.id', '>', $last_id);
			$query->order_by('droplet_comments.id', 'ASC');
		}
		else
		{
			$query->where('droplet_comments.id', '<', $last_id);
			$query->order_by('droplet_comments.id', 'DESC');
		}
		
		// Group the comments per droplet
		$comments = $query->execute()->as_array();
		foreach ($comments as & $comment)
		{
		   $comment['identity_avatar'] = Swiftriver_Users::gravatar($comment['identity_email'], 80);
		   $comment['deleted'] = (bool) $comment['deleted'];
		   
		   if ($comment['deleted'])
		   {
		   	$comment['comment_text'] = __('This comment has been removed.');
		   }
		}
		
		return $comments;
	}

	
	/**
	 * Get a range of IDs to be used for inserting drops
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lowe limit of the range requested
	 */
	public static function get_ids($num, $table="droplets")
	{
	    // Build River Query
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('".$table."',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
}

?>
