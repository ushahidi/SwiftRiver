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
 * @subpackage Models
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

			// Swiftriver Plugin Hook -- execute before saving new droplet
			Event::run('sweeper.droplet.pre_save_new', $this);

			$droplet = parent::save();

			// Swiftriver Plugin Hook -- post_save new droplet
			Event::run('sweeper.droplet.post_save_new', $droplet);
		}
		else
		{
			// Swiftriver Plugin Hook -- pre_save existing droplet
			Event::run('sweeper.droplet.pre_save', $this);

			$droplet = parent::save();

			// Swiftriver Plugin Hook -- post_save existing droplet
			Event::run('sweeper.droplet.post_save', $droplet);
		}

		return $droplet;
	}
	
	/**
	 * Checks if a droplet already exists based on its channel filter and origin id
	 *
	 * @param string $channel_filter_id ID of the channel
	 * @param string $droplet_orig_id Origin ID of the droplet
	 */
	public static function is_duplicate_droplet($channel_filter_id, $droplet_orig_id)
	{
		return (bool) ORM::factory('droplet')
						->where('channel_filter_id', '=', $channel_filter_id)
						->and_where('droplet_orig_id', '=', $droplet_orig_id)
						->count_all();
	}
}