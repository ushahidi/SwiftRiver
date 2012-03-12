<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Links
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
class Model_Link extends ORM
{
	/**
	 * A link has and belongs to many droplets and accounts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'droplets' => array(
			'model' => 'droplet',
			'through' => 'droplets_links'
			),
		'accounts' => array(
			'model' => 'account',
			'through' => 'droplets_links'
			),				
		);

	
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
		$orm_link = ORM::factory('link')
					->where('url', '=', $url)
					->find();
		
		if ($orm_link->loaded())
		{
			return $orm_link;
		}
		elseif ( ! $orm_link->loaded() AND $save)
		{
			// Get the full URL
			$orm_link->url = $url;
			$orm_link->url_hash = hash('sha256', $url);
			$orm_link->domain = parse_url($url, PHP_URL_HOST);
			
			// Save and return
			return $orm_link->save();
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Checks if a given link already exists. 
	 * The parameter $links is an array of hashes containing the 
	 * link, link_full and domain as below
	 * E.g: $link = array('link' => 'http://t.co/mg9yqyFp', 'link_full' => 'http://ushahidi.com', 'link_domain' => 'ushahidi.com');
	 *
	 * @param string $links Array of hashes described above
	 * @return mixed array of links ids if the links exists, FALSE otherwise
	 */
	public static function get_links($links)
	{
		// First try to add any links missing from the db
		// The below generates the below query to find missing links and insert them all at once:
		/*
		 *    INSERT INTO `links` (`url`, `url_hash`, `domain`) 
		 *    SELECT DISTINCT * FROM (
		 *    	SELECT 'http://goat.com/mg9yqyFpXXXXX' AS `url`, '7d645adf5695a8b15dd5779c2b58e48185a729eb7ab5a01702dc7586a2e5a149' AS `url_hash`, 'goat.com' AS `domain` UNION ALL
		 *    	SELECT 'http://t.co/mg9yqyFp' AS `url`, '99edeb38bcddeb0450557a38782d16bf84d438b91ed6231e448b3ff696fc9820' AS `url_hash`, 't.co' AS `domain`
		 *    ) AS `a` 
		 *    WHERE (url, url_hash, domain) NOT IN (
		 *    	SELECT `url`, `url_hash`, `domain` 
		 *    	FROM `links` 
		 *    	WHERE (url, url_hash, domain) IN (
		 *    		('http://t.co/mg9yqyFp', '7d645adf5695a8b15dd5779c2b58e48185a729eb7ab5a01702dc7586a2e5a149', 't.co'), 
		 *    		('http://goat.com/mg9yqyFpXXXXX', '99edeb38bcddeb0450557a38782d16bf84d438b91ed6231e448b3ff696fc9820', 'goat.com')
		 *    	)
		 *    )		
		 */
		$query = DB::select()->distinct(TRUE);
		$link_subquery = NULL;
		foreach ($links as $link)
		{
			$union_query = DB::select(
							array(DB::expr("'".addslashes($link['url'])."'"), 'url'), 		
							array(DB::expr("'".addslashes($link['url_hash'])."'"), 'url_hash'),
							array(DB::expr("'".addslashes($link['domain'])."'"), 'domain'));
			if ( ! $link_subquery)
			{
				$link_subquery = $union_query;
			}
			else
			{
				$link_subquery = $union_query->union($link_subquery, TRUE);
			}
		}
		if ($link_subquery)
		{
			$query->from(array($link_subquery,'a'));
			$sub = DB::select('url', 'url_hash', 'domain')
			           ->from('links')
			           ->where(DB::expr('(url, url_hash, domain)'), 'IN', $links);
			$query->where(DB::expr('(url, url_hash, domain)'), 'NOT IN', $sub);
			DB::insert('links', array('url', 'url_hash', 'domain'))->select($query)->execute();
		}
		
		// Get the link IDs
		if ($links)
		{
			$query = DB::select('id')
			           ->from('links')
			           ->where(DB::expr('(url, url_hash, domain)'), 'IN', $links);

			return $query->execute()->as_array();
		}
		
		return FALSE;
	}	
}
