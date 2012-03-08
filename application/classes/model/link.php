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
	 * Overload saving to perform additional functions on the link
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time links only
		if ($this->loaded() === FALSE)
		{
			// Save the date the link was first added
			$this->link_date_add = date("Y-m-d H:i:s", time());
		}

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
		$orm_link = ORM::factory('link')
					->where('link', '=', $url)
					->find();
		
		if ($orm_link->loaded())
		{
			return $orm_link;
		}
		elseif ( ! $orm_link->loaded() AND $save)
		{
			// Get the full URL
			$orm_link->link = $url;
			$orm_link->link_full = $url;
			$orm_link->link_domain = parse_url($url, PHP_URL_HOST);
			
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
		 *		INSERT INTO `links` (`link`, `link_full`, `link_domain`) 
		 *		SELECT DISTINCT * FROM (
		 *			SELECT 'http://goat.com/mg9yqyFpXXXXX' AS `link`, 'http://goat.com/mg9yqyFpXXXXX' AS `link_full`, 'goat.com' AS `link_domain` UNION ALL
		 *			SELECT 'http://t.co/mg9yqyFp' AS `link`, 'http://t.co/mg9yqyFp' AS `link_full`, 't.co' AS `link_domain`
		 *		) AS `a` 
		 *		WHERE (link, link_full, link_domain) NOT IN (
		 *			SELECT `link`, `link_full`, `link_domain` 
		 *			FROM `links` 
		 *			WHERE (link, link_full, link_domain) IN (
		 *				('http://t.co/mg9yqyFp', 'http://t.co/mg9yqyFp', 't.co'), 
		 *				('http://goat.com/mg9yqyFpXXXXX', 'http://goat.com/mg9yqyFpXXXXX', 'goat.com')
		 *			)
		 *		)
		 */
		$query = DB::select()->distinct(TRUE);
		$link_subquery = NULL;
		foreach ($links as $link)
		{
			$union_query = DB::select(
							array(DB::expr("'".addslashes($link['link'])."'"), 'link'), 		
							array(DB::expr("'".addslashes($link['link_full'])."'"), 'link_full'),
							array(DB::expr("'".addslashes($link['link_domain'])."'"), 'link_domain'));
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
			$sub = DB::select('link', 'link_full', 'link_domain')
			           ->from('links')
			           ->where(DB::expr('(link, link_full, link_domain)'), 'IN', $links);
			$query->where(DB::expr('(link, link_full, link_domain)'), 'NOT IN', $sub);
			DB::insert('links', array('link', 'link_full', 'link_domain'))->select($query)->execute();
		}
		
		// Get the link IDs
		if ($links)
		{
			$query = DB::select('id')
			           ->from('links')
			           ->where(DB::expr('(link, link_full, link_domain)'), 'IN', $links);

			return $query->execute()->as_array();
		}
		
		return FALSE;
	}	
}
