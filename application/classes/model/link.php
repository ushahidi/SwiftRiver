<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Links
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
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
		$orm_link = ORM::factory('links')
					->where('link', '=', $url)
					->or_where('link_full', '=', $url)
					->find();
		
		if ($orm_link->loaded())
		{
			return $orm_link;
		}
		elseif ( ! $orm_link->loaded() AND $save)
		{
			// Get the full URL
			$full_link = Swiftriver_Links::full($url);
			$orm_link->link = $url;
			$orm_link->link_full = $full_link
			$orm_link->domain = parse_url($full_link, PHP_URL_HOST);
			$orm_link->link_date_add = date('Y-m-d H:i:s', time());
			
			// Save and return
			return $orm_link->save();
		}
		else
		{
			return FALSE;
		}
	}	
}
