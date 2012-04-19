<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Media
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
class Model_Media extends ORM {
	/**
	 * Media has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'droplets_media'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_media'
			),				
		);
		
	/**
	 * Overload saving to perform additional functions on the media
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time media only
		if ($this->loaded() === FALSE)
		{
			// Save the date the media was first added
			$this->media_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}


	/**
	 * Checks if a given media item already exists. 
	 * The parameter $media is an array of hashes containing the 
	 * media, media_hash and media_type
	 * 
	 * @param string $media Array of hashes described above
	 * @return mixed array of media ids if the media exists, FALSE otherwise
	 */
	public static function get_media($media)
	{
		$query = DB::select()->distinct(TRUE);
		$media_subquery = NULL;
		foreach ($media as $link)
		{
			$union_query = DB::select(
							array(DB::expr("'".addslashes($link['media'])."'"), 'media'), 		
							array(DB::expr("'".addslashes($link['media_hash'])."'"), 'media_hash'),
							array(DB::expr("'".addslashes($link['media_type'])."'"), 'media_type'));
			if ( ! $media_subquery)
			{
				$media_subquery = $union_query;
			}
			else
			{
				$media_subquery = $union_query->union($media_subquery, TRUE);
			}
		}
		if ($media_subquery)
		{
			$query->from(array($media_subquery,'a'));
			$sub = DB::select('media', 'media_hash', 'media_type')
			           ->from('media')
			           ->where(DB::expr('(media, media_hash, media_type)'), 'IN', $media);
			$query->where(DB::expr('(media, media_hash, media_type)'), 'NOT IN', $sub);
			DB::insert('media', array('media', 'media_hash', 'media_type'))->select($query)->execute();
		}
		
		// Get the media IDs
		if ($media)
		{
			$query = DB::select('id')
			           ->from('media')
			           ->where(DB::expr('(media, media_hash, media_type)'), 'IN', $media);

			return $query->execute()->as_array();
		}
		
		return FALSE;
	}	
}
