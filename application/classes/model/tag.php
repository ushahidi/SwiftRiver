<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Tag extends ORM
{
	/**
	 * A tag has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplets',
			'through' => 'droplets_tags'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_tags'
			),
		);

	/**
	 * Overload saving to perform additional functions on the tag
	 */
	public function save(Validation $validation = NULL)
	{
		$this->id = self::get_ids(1);
		$this->tag = trim($this->tag);
		$this->tag_canonical = strtolower($this->tag);
		$this->tag_type = strtolower(trim($this->tag_type));
		$this->hash = md5($this->tag.$this->tag_type);

		return parent::save();
	}
	
	/**
	 * Checks if a given tag already exists. 
	 * The parameter $tag is a hash containing the tag name and type as below
	 * E.g: $tag = array('tag_name' => 'bubba', tag_type => 'junk');
	 *
	 * @param string $tag Has containing the tag name and tag type
	 * @param bool $save Optionally saves the tag if does not exist
	 * @return mixed Model_Tag if the tag exists, FALSE otherwise
	 */
	public static function get_tag_by_name($tag, $save = FALSE)
	{
		$result = ORM::factory('tag')
		            ->where('tag', '=', $tag['tag_name'])
		            ->where('tag_type', '=', $tag['tag_type'])
		            ->find();
		
		if ($result->loaded())
		{
			return $result;
		}
		elseif ( ! $result->loaded() AND $save == TRUE)
		{
			try
			{
				// Save the tag
				$orm_tag = new Model_Tag;
				$orm_tag->tag  = $tag['tag_name'];
				$orm_tag->tag_type = $tag['tag_type'];
			
				return $orm_tag->save();
			}
			catch (Database_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, $e->getMessage());
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Populate IDs into the droplets' tags' arrays creating those that are missing
	 * The tags array is an array of hashes containing the 
	 * tag name and type as below
	 * E.g: $tag = array('tag_name' => 'bubba', tag_type => 'junk');
	 *
	 * @param string $tags Array of hashes described above
	 */
	public static function get_tags(& $drops)
	{
		if (empty($drops))
			return;
			
		// Generate the tag hashes and create a index hash array of the given tag
		// linking a drop to a tag
		$tags_idx = array();
		foreach ($drops as $drop_key => & $drop)
		{
			if (isset($drop['tags']))
			{
				foreach ($drop['tags'] as $tag_key => & $tag)
				{
					if ( ! isset($tag['id']) )
					{
						$tag['tag_name'] = trim($tag['tag_name']);
						$tag['tag_type'] = strtolower(trim($tag['tag_type']));
						$hash = md5($tag['tag_name'].$tag['tag_type']);
						if (empty($tags_idx[$hash]))
						{
							$tags_idx[$hash]['tag_name'] = $tag['tag_name'];
							$tags_idx[$hash]['tag_type'] = $tag['tag_type'];
							$tags_idx[$hash]['keys'] = array();
						}
						$tags_idx[$hash]['keys'][] = array($drop_key, $tag_key);
					}
				}
			}
		}
		
		if (empty($tags_idx))
			return;
		
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Find those that exist
		$found = DB::select('hash', 'id')
					->from('tags')
					->where('hash', 'IN', array_keys($tags_idx))
					->execute()
					->as_array();
					
		// Update the found entries
		$new_tag_count = count($tags_idx);
		foreach ($found as $hash)
		{
			foreach ($tags_idx[$hash['hash']]['keys'] as $keys)
			{
				list($drop_key, $tag_key) = $keys;
				$drops[$drop_key]['tags'][$tag_key]['id'] = $hash['id'];
			}
			$new_tag_count--;
			unset($tags_idx[$hash['hash']]);
		}
		
		if ( ! empty($tags_idx))
		{
			// Get a range of IDs to be used in inserting the new tags
			$base_id = self::get_ids($new_tag_count);
			
			$query = DB::insert('tags', array('id', 'hash', 'tag', 'tag_canonical', 'tag_type'));
			foreach ($tags_idx as $hash => $value)
			{
				foreach ($value['keys'] as $key)
				{
					list($drop_key, $tag_key) = $key;
					$drops[$drop_key]['tags'][$tag_key]['id'] = $base_id;
				}
				$query->values(array(
					'id' => $base_id++,
					'hash' => $hash,
					'tag' => $value['tag_name'],
					'tag_canonical' => strtolower($value['tag_name']),
					'tag_type' => $value['tag_type']
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
	    // Build River Query
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('tags',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
}
