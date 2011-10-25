<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Droplets
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
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
	 * A droplet belongs to an identity and a channel_filter
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'channel_filter' => array(),
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
	 * Checks if a droplet already exists based on its channel filter and origin id
	 *
	 * @param string $channel_filter_id ID of the channel
	 * @param string $droplet_hash SHA2 Hash of the origin ID of the droplet
	 */
	public static function is_duplicate_droplet($channel_filter_id, $droplet_hash)
	{
		return (bool) ORM::factory('droplet')
						->where('channel_filter_id', '=', $channel_filter_id)
						->where('droplet_hash', '=', $droplet_hash)
						->count_all();
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
			$orm_droplet->channel_filter_id = $droplet['channel_filter_id'];
			$orm_droplet->identity_id = $droplet['identity_id'];
			$orm_droplet->droplet_hash = $droplet['droplet_hash'];
			$orm_droplet->droplet_orig_id = $droplet['droplet_orig_id'];
			$orm_droplet->droplet_type = $droplet['droplet_type'];
			$orm_droplet->droplet_title = $droplet['droplet_title'];
			$orm_droplet->droplet_content = $droplet['droplet_content'];
			$orm_droplet->droplet_raw = $droplet['droplet_raw'];
			$orm_droplet->droplet_locale = $dropplet['droplet_locale'];
			$orm_droplet->droplet_date_add = date('Y-m-d H:i:s', time());
			$orm_droplet->droplet_date_pub = $droplet['droplet_date_pub'];
			$orm_droplet->droplet_processed = 0;
			$orm_droplet->save();
			
			$droplet['id'] = $orm_droplet->id;
		}
		else
		{
			// Get the droplet id
			$droplet_id = $droplet['id'];
			
			// Update the droplet entry in the DB
			$orm_droplet = ORM::factory('droplet', $droplet_id);
			$orm_droplet->droplet_processed = 1;
			$orm_droplet->droplet_date_processed = date('Y-m-d H:i:s', time());
			$orm_droplet->save();
			
			// Save the tags, links and places
			self::add_tags($droplet_id, $droplet['tags']);
			self::add_links($droplet_id, $droplet['links']);
			// self::add_places($droplet_id, $droplet['places']);
		}
	}
	
	/**
	 * Adds tags to a droplet
	 *
	 * @param int $droplet_id Database id of the droplet
	 * @param Mixed $tags Tag/list of tags to be linked to the droplet
	 */
	public static function add_tags($droplet_id, $tags)
	{
		if ( ! is_array($tags))
		{
			// Check if the tag already exists
			$orm_tag = Model_Tag::get_tag_by_name($tags, TRUE);
			if ($orm_tag)
			{
				// Associate the droplet with the tag
				DB::insert('droplet_tags')
					->columns(array('droplet_id', 'tag_id'))
					->values(array($droplet_id, $orm_tag->id))
					->execute();
			}
		}
		else
		{
			foreach ($tags as $tag_name)
			{
				self::add_tags($droplet_id, $tag_name);
			}
		}
	}
	
	/**
	 * Adds links to a droplet
	 *
	 * @param int $droplet_id
	 * @param array $links
	 */
	public static function add_links($droplet_id, $links)
	{
		if ( ! is_array($links))
		{
			// Add the link
			$orm_link = Model_Link::get_link_by_url($links, TRUE);
			if ($orm_link)
			{
				DB::insert('droplet_links')
					->columns(array('droplet_id', 'link_id'))
					->values(array($droplet_id, $orm_link->id))
					->execute();
			}
		}
		else
		{
			foreach ($links as $url)
			{
				self::add_links($droplet_id, $url);
			}
		}
	}
	
	/**
	 * Adds the list of place names associated with a droplet
	 *
	 * @param int $droplet_id
	 * @param array $places List of place names associated with the droplet
	 */
	public static function add_places($droplet_id, $places)
	{
		// TODO Add the places
	}
}
