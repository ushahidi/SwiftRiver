<?php defined('SYSPATH') or die('No direct script access');

/**
 * Trends helper
 *
 * PHP Version 5.3+
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 *
 * @author     Ushahidi Team
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Helpers
 * @copyright  Ushahidi Inc - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */
class Swiftriver_Trends {

	/**
	 * For each of a user's active rivers, gets the no. of drops added to a river
	 * for each day that the river has been active. If a river is specified,
	 * on the trend data for that river is returned
	 *
	 * @param ORM $user
	 * @param int $river_id When specified, generates the histogram for a single river
	 * @return array
	 */
	public static function get_river_growth_trend($user, $river_id = NULL)
	{
		// Query to fetch the trend data
		$query = DB::select('rivers_droplets.river_id', 'rivers.river_name',
				array(DB::expr('COUNT(rivers_droplets.droplet_id)'), 'drop_count'),
				array(DB::expr('DATE_FORMAT(droplets.droplet_date_add, "%Y-%m-%d")'),'activity_date'))
			->from('rivers_droplets')
			->join('droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			->join('rivers', 'INNER')
			->on('rivers_droplets.river_id', '=', 'rivers.id');

		// If the river_id is not specified, get the all the user's rivers
		if (empty($river_id))
		{
			$query->join('accounts', 'INNER')
				->on('rivers.account_id', '=', 'accounts.id')
				->where('accounts.user_id', '=', $user->id);
		}

		// If river_id is specified, fetch data for a single river
		if ( ! empty($river_id))
		{
			$query->where('rivers_droplets.river_id', '=', $river_id);
		}

		// Group the results
		$query->group_by('rivers_droplets.river_id', 'activity_date');

		// Histogram data
		$histogram = array();

		// Generate the histogram
		if  ( ! empty($river_id))
		{
			// Build the histogram for a single river
			foreach ($query->execute()->as_array() as $row)
			{
				$histogram[] = array(
					'activity_date' => $row['activity_date'],
					'drop_count' => $row['drop_count']
				);
			}
		}
		else
		{
			// Build histogram for all the rivers
			foreach ($query->execute()->as_array() as $row)
			{
				$river_name = $row['river_name'];
				if ( ! array_key_exists($river_name, $histogram))
				{
					$histogram[$river_name] = array();
				}
				$entry = array(
					'activity_date' => $row['activity_date'],
					'drop_count' => $row['drop_count']
				);

				$histogram[$river_name][] = $entry;
			}
		}

		return $histogram;
	}

	/**
	 * Gets the (10) most active sources for the specified river. The
	 * sources are grouped per channel
	 *
	 * @param int $river_id ID of the river
	 * @param int $count No. of sources to fetch per channel
	 * @return array
	 */
	public static function get_sources_trend($river_id, $count = 10)
	{
		$sources_trend = array();

		$query = DB::select('identities.channel', 'identities.identity_name',
				array(DB::expr('COUNT(rivers_droplets.droplet_id)'), 'drop_count'))
			->from('identities')
			->join('droplets', 'INNER')
			->on('droplets.identity_id', '=', 'identities.id')
			->join('rivers_droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			->where('rivers_droplets.river_id', '=', $river_id)
			->group_by('identities.channel', 'identities.identity_name')
			->order_by('drop_count', 'DESC')
			->having('drop_count', '>', 1);

		// Load the channel plugins
		Swiftriver_Plugins::channels();

		foreach ($query->execute()->as_array() as $row)
		{
			$channel_config = Swiftriver_Plugins::get_channel_config($row['channel']);
			$channel = $channel_config['name'];
			if ( ! array_key_exists($channel, $sources_trend))
			{
				$sources_trend[$channel] = array();
			}

			if (count($sources_trend[$channel]) >= $count)
				continue;

			$entry = array(
				'identity_name' => $row['identity_name'],
				'drop_count' => $row['drop_count'],
			);

			$sources_trend[$channel][] = $entry;
		}

		return $sources_trend;
	}

	/**
	 * Gets the number of drops fetched for each the river's channels for the
	 * last x days i.e.
	 *
	 * -------------------------------------
	 * channel | drop_count | activity_date|
	 * -------------------------------------
	 *         |            |              |
	 *         |            |              |
	 *
	 * @param int $river_id
	 * @param int $duration
	 * @return array
	 */
	public static function get_channels_trend($river_id, $duration = 30)
	{
		$channels_trend = array();

		// Get the last date when the river was updated
		$last_update_date = Model_River::get_last_update_date($river_id);

		// Compute the start date for fetching the trend data i.e.
		// $last_update_date - $duration
		$trend_start_date = strtotime(__("-:days day", array(":days" => $duration)),
			strtotime($last_update_date));

		$trend_query = DB::select('droplets.channel',
				array(DB::expr('COUNT(rivers_droplets.droplet_id)'), 'drop_count'),
				array(DB::expr('DATE_FORMAT(droplets.droplet_date_add, "%Y-%m-%d")'), 'activity_date'))
			->from('droplets')
			->join('rivers_droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			->where('rivers_droplets.river_id', '=', $river_id);

		if ( ! empty($duration) AND $duration > 0)
		{
			$trend_query->where('droplets.droplet_date_add', '>=', date('Y-m-d H:i:s', $trend_start_date));
		}

		$trend_query->group_by('droplets.channel', 'activity_date');
		
		// Load the channel plugins
		Swiftriver_Plugins::channels();

		// Organize the data per channel
		foreach ($trend_query->execute()->as_array() as $row)
		{
			$channel_config = Swiftriver_Plugins::get_channel_config($row['channel']);
			$channel = $channel_config['name'];
			if ( ! array_key_exists($channel, $channels_trend))
			{
				$channels_trend[$channel] = array();
			}

			$entry = array(
				'drop_count' => $row['drop_count'],
				'activity_date' => $row['activity_date']
			);

			$channels_trend[$channel][] = $entry;
		}

		return $channels_trend;
	}

	/**
	 * Gets a breakdown of the tag volume by type
	 *
	 * @param int $river_id 
	 * @return array
	 */
	public static function get_tag_count_by_type($river_id)
	{
		$tags_trend = array();

		// Get the tag trends
		$query = DB::select('river_tag_trends.tag_type',
				array(DB::expr('SUM(river_tag_trends.count)'), 'tag_count'))
			->from('river_tag_trends')
			->join('rivers', 'INNER')
			->on('river_tag_trends.river_id', '=', 'rivers.id')
			->where('river_tag_trends.river_id', '=', $river_id)
			->where('river_tag_trends.date_pub', '>=', DB::expr('rivers.river_date_add'))
			->group_by('river_tag_trends.tag_type');

		// Group the data by tag type
		foreach ($query->execute()->as_array() as $row)
		{
			$tags_trend[] = array(
				'tag_type' => ucfirst($row['tag_type']),
				'tag_count' => $row['tag_count']
			);
		}

		return $tags_trend;
	}

	/**
	 * Gets a breakdown - by channel -of the drops within the river
	 *
	 * @param  int $river_id ID of the river
	 * @return array
	 */
	public static function get_river_channels_breakdown($river_id)
	{
		$query = DB::select('droplets.channel',
			array(DB::expr('COUNT(rivers_droplets.id)'), 'drop_count'))
			->from('droplets')
			->join('rivers_droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets.id')
			->where('rivers_droplets.river_id', '=', $river_id)
			->group_by('droplets.channel');
		
		$breakdown = array();
		Swiftriver_Plugins::channels();
		foreach ($query->execute()->as_array() as $row)
		{
			$channel_config = Swiftriver_Plugins::get_channel_config($row['channel']);
			$channel = $channel_config ? $channel_config['name'] : $row['channel'];

			$breakdown[] = array(
				'channel' => $channel,
				'drop_count' => $row['drop_count']
			);
		}

		return $breakdown;
	}

	/**
	 * Gets the no. of drops that have been curated (placed in buckets)
	 * TODO: Move this to the river model; the same as the no. of read drops
	 *
	 * @param int $river_id ID of the river 
	 * @return int
	 */
	public static function curated_drop_count($river_id)
	{
		$drop_count = DB::select(array(DB::expr('COUNT(rivers_droplets.id)'), 'drop_count'))
			->from('rivers_droplets')
			->join('buckets_droplets', 'INNER')
			->on('buckets_droplets.droplet_id', '=', 'rivers_droplets.droplet_id')
			->where('rivers_droplets.river_id', '=', $river_id)
			->execute()
			->get(0, 'drop_count');

		return intval($drop_count);
	}
	
	/**
	 * Gets the breakdown of all the media types within the river
	 *
	 * @param  int  $river_id
	 * @return array
	 */
	public static function get_media_types_breakdown($river_id)
	{
		$media_breakdown = array();
		
		// Links
		$links_query = DB::select(array(DB::expr("'link'"), 'media_type'),
				array(DB::expr('COUNT(droplets_links.id)'), 'media_count'))
			->from('droplets_links')
			->join('rivers_droplets', 'INNER')
			->on('droplets_links.droplet_id', '=', 'rivers_droplets.droplet_id')
			->where('rivers_droplets.river_id', '=', $river_id)
			->group_by('media_type');
		
		$query = DB::select(array('media.type', 'media_type'),
				array(DB::expr('COUNT(droplets_media.id)'), 'media_count'))
			->union($links_query, TRUE)
			->from('droplets_media')
			->join('media' , 'INNER')
			->on('droplets_media.media_id', '=', 'media.id')
			->join('rivers_droplets', 'INNER')
			->on('rivers_droplets.droplet_id', '=', 'droplets_media.droplet_id')
			->where('rivers_droplets.river_id', '=', $river_id)
			->group_by('media_type');

		foreach ($query->execute()->as_array() as $row)
		{
			$media_breakdown[] = array(
				'media_type' => ucfirst($row['media_type']).'s',
				'media_count' => $row['media_count']
			);
		}
		
		return $media_breakdown;
	}
	
	/**
	 * Gets the following analytics for the drops within the river
	 *    % of drops with links
	 *    % of drops with media (photos and video)
	 *    % of grops with place tags
	 *    % of drops with people and organization tags
	 *
	 * @param   Model_River  $river_orm
	 * @return  array
	 */
	public static function get_content_analysis($river_orm)
	{
		$analysis = array();
		
		// No. of drops with links
		$links_query = DB::select(array(DB::expr('COUNT(DISTINCT rivers_droplets.id)'), 'item_count'),
			array(DB::expr("'links_count'"), 'analytics_param'))
			->from('droplets_links')
			->join('rivers_droplets', 'INNER')
			->on('droplets_links.droplet_id', '=', 'rivers_droplets.droplet_id')
			->where('rivers_droplets.river_id', '=', $river_orm->id)
			->group_by('analytics_param');
		
		// No. of drops with links and media
		$links_media_query = DB::select(array(DB::expr('COUNT(DISTINCT rivers_droplets.id)'), 'item_count'),
			array(DB::expr("CONCAT_WS('_', media.type, 'count')"), 'analytics_param'))
				->union($links_query, TRUE)
				->from('droplets_media')
				->join('media', 'INNER')
				->on('droplets_media.media_id', '=', 'media.id')
				->join('rivers_droplets', 'INNER')
				->on('rivers_droplets.droplet_id', '=', 'droplets_media.droplet_id')
				->where('rivers_droplets.river_id', '=', $river_orm->id)
				->group_by('analytics_param');
		
			// No. of drops with links, media, place, people and organization tags
		$query = DB::select(array(DB::expr('SUM(river_tag_trends.count)'), 'item_count'),
				array(DB::expr("CONCAT_WS('_', river_tag_trends.tag_type, 'count')"), 'analytics_param'))
				->union($links_media_query, TRUE)
				->from('river_tag_trends')
				->where('river_tag_trends.river_id', '=', $river_orm->id)
				->group_by('analytics_param');
		
		foreach ($query->execute()->as_array() as $row)
		{
			$item_count = intval($row['item_count']);
			$percentage = round(($item_count/$river_orm->drop_count) * 100, 2);

			$analysis[$row['analytics_param']] = $percentage."%";
		}
		
		return $analysis;
	}
	
}
