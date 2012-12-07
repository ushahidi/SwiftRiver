<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Media
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
class Model_Media extends ORM {
	/**
	 * Media has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'Droplet',
			'through' => 'droplets_media'
			),
		'accounts' => array(
			'model' => 'Account',
			'through' => 'droplets_media'
			),
		'thumbnails' => array(
			'model' => 'Media_Thumbnail'
			),
	);
	
	/**
	 * Auto-update columns for creation
	 * @var string
	 */
	 protected $_created_column = array('column' => 'media_date_add', 'format' => 'Y-m-d H:i:s');
	 
	/**
	 * Checks if a given media item already exists. 
	 * The parameter $media is an array of hashes containing the 
	 * media, media_hash and media_type
	 * 
	 * @param string $media Array of hashes described above
	 * @return mixed array of media ids if the media exists, FALSE otherwise
	 */
	public static function get_media(& $drops)
	{
		if (empty($drops))
			return;
			
		// Generate the media hashes and create a index hash array of the given media
		// linking a drop to a media
		$media_idx = array();
		foreach ($drops as $drop_key => & $drop)
		{
			if (isset($drop['media']))
			{
				foreach ($drop['media'] as $media_key => & $media)
				{
					if ( ! isset($media['id']) )
					{
						$hash = md5($media['url']);
						if (empty($media_idx[$hash]))
						{
							$media_idx[$hash]['url'] = $media['url'];
							$media_idx[$hash]['type'] = $media['type'];
							$media_idx[$hash]['keys'] = array();
						}
						$media_idx[$hash]['keys'][] = array($drop_key, $media_key);
					}
				}
			}
		}
		
		if (empty($media_idx))
			return;
		
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Find those that exist
		$found = DB::select('hash', 'id')
					->from('media')
					->where('hash', 'IN', array_keys($media_idx))
					->execute()
					->as_array();
					
		// Update the found entries
		$new_media_count = count($media_idx);
		foreach ($found as $hash)
		{
			foreach ($media_idx[$hash['hash']]['keys'] as $keys)
			{
				list($drop_key, $media_key) = $keys;
				$drops[$drop_key]['media'][$media_key]['id'] = $hash['id'];
			}
			$new_media_count--;
			unset($media_idx[$hash['hash']]);
		}
		
		if ( ! empty($media_idx))
		{
			// Get a range of IDs to be used in inserting the new media
			$base_id = self::get_ids($new_media_count);
			
			$query = DB::insert('media', array('id', 'hash', 'url', 'type'));
			foreach ($media_idx as $hash => $value)
			{
				foreach ($value['keys'] as $key)
				{
					list($drop_key, $media_key) = $key;
					$drops[$drop_key]['media'][$media_key]['id'] = $base_id;
				}
				$query->values(array(
					'id' => $base_id++,
					'hash' => $hash,
					'url' => $value['url'],
					'type' => $value['type']
				));
			}
			$query->execute();
		}
		
		Swiftriver_Mutex::release(get_class());
	}
	
	/**
	 * Get a range of IDs to be used for inserting drops
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lowe limit of the range requested
	 */
	public static function get_ids($num)
	{
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('media',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
}
