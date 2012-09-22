<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Model_River_Tag_Trend
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Model_River_Tag_Trend extends ORM {
	
	/**
	 * Get trend for a specific period
	 *
	 * @return array
	 */
	public static function get_trend($river_id, $start_time, $tag_type)
	{
		$query = DB::select('tag', array(DB::expr('SUM(`count`)'), 'count'))
					->from('river_tag_trends')
					->where('river_id', '=', $river_id)
					->where('tag_type', '=', $tag_type)
					->group_by('tag', 'tag_type')
					->order_by(DB::expr('SUM(`count`)'), 'DESC')
					->limit(10);
		
		if (isset($start_time))
		{
			$query->where('date_pub', '>=', $start_time);
		}
		
		return $query->execute()->as_array();
	}
	
	/**
	 * Creates trends from the given array
	 *
	 * @param array $trends
	 * @return array
	 */
	public static function create_from_array($trends)
	{
		if (empty($trends))
			return;
		
		Swiftriver_Mutex::obtain(get_class(), 3600);
		
		// Hash array with droplet_hash as key and index in droplets array that contain that hash
		$trends_idx = array();
		foreach ($trends as $key => & $trend)
		{
			$hash = md5($trend['river_id'].$trend['date_pub'].$trend['tag'].$trend['tag_type']);
			$trend['hash'] = $hash;
			$trends_idx[$hash] = $key;
		}
		
		// Find the drops that already exist by their droplet_hash
		$found_query = DB::select('hash', 'id')
					->from('river_tag_trends')
					->where('hash', 'IN', array_keys($trends_idx));
		$found = $found_query->execute()->as_array();
		
		// Create a query to update existing trends and 
		// remove them from the trends_idx
		$update_query = NULL;
		foreach ($found as $hash)
		{
			if ($update_query)
			{
				$update_query .= ' union all ';
			}
			$count = $trends[$trends_idx[$hash['hash']]]['count'];
			$update_query .= 'select '.$hash['id'].' id, '.$count.' count';
			unset($trends_idx[$hash['hash']]);
		}
		
		if ($update_query)
		{
			$query = "UPDATE `river_tag_trends` JOIN (".$update_query.") a "
			    ."USING (`id`) SET `river_tag_trends`.`count` = `river_tag_trends`.`count` + `a`.`count`";			
			DB::query(Database::UPDATE, $query)->execute();
		}
		
		if ( ! empty($trends_idx))
		{
			// Get a range of IDs to be used in inserting the new drops
			$base_id = Model_River_Tag_Trend::get_ids(count($trends_idx));

			// Insert into the droplets table
			$query = DB::insert('river_tag_trends', 
				array('id', 'hash', 'river_id', 'date_pub',
				    'tag', 'tag_type', 'count'
				));
			
			foreach ($trends_idx as $hash => $key) 
			{
				$query->values(array(
					'id' => $base_id++,
					'hash' => $trends[$key]['hash'],
					'river_id' => $trends[$key]['river_id'],
					'date_pub' => $trends[$key]['date_pub'],
					'tag' => $trends[$key]['tag'],
					'tag_type' => $trends[$key]['tag_type'],
					'count' => $trends[$key]['count']
				));
			}
			$query->execute();
		}		
		Swiftriver_Mutex::release(get_class());
	}
	
	/**
	 * Get a range of IDs to be used for inserting trends
	 *
	 * @param int $num Number of IDs to be generated.
	 * @return int The lower limit of the range requested
	 */
	public static function get_ids($num)
	{
		$query = DB::query(Database::SELECT, "/*ms=master*/SELECT NEXTVAL('river_tag_trends',$num) AS id");
		    
		return intval($query->execute()->get('id', 0));
	}
}
?>