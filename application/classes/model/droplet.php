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
	 * Number of droplets to show per page
	 */
	const DROPLETS_PER_PAGE = 20;

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
			'through' => 'attachments_buckets'
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
	 *
	 */
	public function mark_as_processed()
	{
		$this->droplet_processed = 1;
		$this->save();
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
			$orm_droplet->save();
			
			// Check if the 'river_id' key is set
			if ($droplet['river_id'])
			{
				// Add the droplet to the river
				Model_River::add_droplet($droplet['river_id'], $orm_droplet);
			}
			
			$droplet['id'] = $orm_droplet->id;
		}
		else
		{
			// Get the droplet id
			$droplet_id = $droplet['id'];
			
			// Update the droplet entry in the DB
			$orm_droplet = ORM::factory('droplet', $droplet_id);
			$orm_droplet->droplet_processed = 1;
			$orm_droplet->save();
			
			// Save the tags, links and places
			self::add_tags($orm_droplet, $droplet['tags']);
			self::add_links($orm_droplet, $droplet['links']);
			self::add_places($orm_droplet, $droplet['places']);
		}
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
	 * Get Droplets by River
	 *
	 * @param int $id ID of the River
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_river($id = 0, $page = Null)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);

		if ($id)
		{
			// Build River Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
			                    'droplet_title', 'droplet_content', 
			                    'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
			    ->from('droplets')
			    ->join('rivers_droplets', 'INNER')
			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			    ->join('identities')
			    ->on('droplets.identity_id', '=', 'identities.id')
			    ->where('rivers_droplets.river_id', '=', $id);

			// Clone query before any filters have been applied
			$pre_filter = clone $query;
			$droplets['total'] = (int) $pre_filter->execute()->count();

			// SwiftRiver Plugin Hook -- Hook into River Droplet Query
			//++ Allows for adding for more filters via Plugin
			Swiftriver_Event::run('swiftriver.river.filter', $query);
			
			//Check if we have max droplet id stored from a previous
			//request. If so, use it to prevent pagination from getting
			//screwed by new droplets coming in...
			$session = Session::instance();
			$max_droplet_id = $session->get('river_pagination_max_droplet');
			if ($max_droplet_id)
			{
			   $query->where('droplets.id', '<', $max_droplet_id);	   
			}
			
			// Order & Pagination offset
			$query->order_by('droplets.id', 'DESC');
			if($page)
			{
			    $query->limit(self::DROPLETS_PER_PAGE);	
		        $query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
	        }
		    	    
			// Get our droplets as an Array
			$droplets['droplets'] = $query->execute()->as_array();

			//Get the max id form the session if already set
			//and use this in our query to stop new droplets
			//coming in from messing up the pagination
			if ((int) count($droplets['droplets']) AND ! $max_droplet_id) 
			{
			    $session->set('river_pagination_max_droplet', $droplets['droplets'][0]['id']);
			}			
		}

		return $droplets;
	}

	/**
	 * Get Droplets by Bucket
	 *
	 * @param int $id ID of the Bucket
	 * @return array $droplets Total and Array of Droplets
	 */
	public static function get_bucket($id = NULL, $page = NULL)
	{
		$droplets = array(
			'total' => 0,
			'droplets' => array()
			);
		
		if ($id)
		{
			// Build Buckets Query
			$query = DB::select(array(DB::expr('DISTINCT droplets.id'), 'id'), 
			                    'droplet_title', 'droplet_content', 
			                    'droplets.channel','identity_name', 'identity_avatar', 'droplet_date_pub')
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
			    ->on('droplets.identity_id', '=', 'identities.id')				
				->where('buckets_droplets.bucket_id', '=', $id)
				->order_by('droplets.id', 'DESC');
				
			// Order & Pagination offset
			$query->order_by('droplets.id', 'DESC');
			if($page)
			{
			    $query->limit(self::DROPLETS_PER_PAGE);	
		        $query->offset(self::DROPLETS_PER_PAGE * ($page - 1));
	        }				

			// Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			$droplets['total'] = (int) count($droplets['droplets']);
		}

		return $droplets;
	}
	
	/**
	 * Get geotagged droplets from a River
	 *
	 * @param int $id ID of the river	
	 */
	 public static function get_geo_river($id = NULL) {
	     $droplets = array(
 			'total' => 0,
 			'droplets' => array()
 			);
 			
	     if ($id) 
	     {
 			$query = DB::select('droplets.id', 'droplet_title', 
 			                    'droplet_content', 'droplets.channel',
 			                    'identity_name', 'identity_avatar', 
 			                    'droplet_date_pub', 
 			                    array(DB::expr('X(place_point)'), 'longitude'), 
 			                    array(DB::expr('Y(place_point)'), 'latitude'))
 			    ->from('droplets')
 			    ->join('rivers_droplets', 'INNER')
 			    ->on('rivers_droplets.droplet_id', '=', 'droplets.id')
 			    ->join('identities')
 			    ->on('droplets.identity_id', '=', 'identities.id')
 			    ->join('droplets_places')
 			    ->on('droplets_places.droplet_id', '=', 'droplets.id')
 			    ->join('places')
 			    ->on('droplets_places.place_id', '=', 'places.id')
 			    ->where('rivers_droplets.river_id', '=', $id);
	         
	         // Get our droplets as an Array		
 			$droplets['droplets'] = $query->execute()->as_array();
 			$droplets['total'] = (int) count($droplets['droplets']);
	     }
	     
	     return $droplets;
	 }
}
?>
