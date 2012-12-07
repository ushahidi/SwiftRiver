<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Links
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
class Model_Link extends ORM
{
	/**
	 * A link has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'Droplet',
			'through' => 'droplets_links'
			),
		'accounts' => array(
			'model' => 'Account',
			'through' => 'droplets_links'
			),				
		);

	
	/**
	 * Overload saving to perform additional functions on the tag
	 */
	public function save(Validation $validation = NULL)
	{
		$this->id = self::get_ids(1);
		$this->hash = md5($this->url);
		return parent::save();
	}
	
	/**
	 * Retrives a Model_Link item from the DB and optionally creates the 
	 * save if the retrieval is unsuccessful
	 *
	 * @param string $url URL to be looked up
	 * @param bool $save Optionally save the URL if it's not found
	 * @return mixed Model_Link if a record is found, FALSE otherwise
	 */
	public static function get_link_by_url($url, $save = FALSE)
	{
		$orm_link = ORM::factory('Link')
					->where('url', '=', $url)
					->find();
		
		if ( ! $orm_link->loaded() AND $save)
		{
			// Get the full URL
			$orm_link->url = $url;
			$orm_link->save();
		}
		
		return $orm_link;
	}
	
	/**
	 * Populate IDs into the droplets' links arrays creating those that are missing
	 * The links array is an array of urls
	 *
	 * @param string $links Array of hashes described above
	 * @return mixed array of links ids if the links exists, FALSE otherwise
	 */
	public static function get_links(& $drops)
	{
		if (empty($drops))
			return;
			
		// Generate the url hashes and create a index hash array of the given link
		// linking a drop to a link
		$links_idx = array();
		foreach ($drops as $drop_key => & $drop)
		{
			if (isset($drop['links']))
			{
				foreach ($drop['links'] as $link_key => $link)
				{
					if ( ! isset($link['id']) )
					{
						$hash = md5($link['url']);
						if (empty($links_idx[$hash]))
						{
							$links_idx[$hash]['url'] = $link['url'];
							$links_idx[$hash]['keys'] = array();
						}
						$links_idx[$hash]['keys'][] = array($drop_key, $link_key);
					}
				}
			}
		}
		
		if (empty($links_idx))
			return;
		
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Find those that exist
		$found = DB::select('hash', 'id')
					->from('links')
					->where('hash', 'IN', array_keys($links_idx))
					->execute()
					->as_array();
					
		// Update the found entries
		$new_link_count = count($links_idx);
		foreach ($found as $hash)
		{
			foreach ($links_idx[$hash['hash']]['keys'] as $keys)
			{
				list($drop_key, $link_key) = $keys;
				$drops[$drop_key]['links'][$link_key]['id'] = $hash['id'];
			}
			$new_link_count--;
			unset($links_idx[$hash['hash']]);
		}
		
		if ( ! empty($links_idx))
		{
			// Get a range of IDs to be used in inserting the new links
			$base_id = self::get_ids($new_link_count);
			
			$query = DB::insert('links', array('id', 'hash', 'url'));
			foreach ($links_idx as $hash => $value)
			{
				foreach ($value['keys'] as $key)
				{
					list($drop_key, $link_key) = $key;
					$drops[$drop_key]['links'][$link_key]['id'] = $base_id;

				}
				$query->values(array(
					'id' => $base_id++,
					'hash' => $hash,
					'url' => $value['url']
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
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('links',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}	
}
