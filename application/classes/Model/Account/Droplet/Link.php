<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Account_Droplet_Links
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
class Model_Account_Droplet_Link extends ORM {
    
	/**
	 * A tag belongs to a droplet
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'droplet' => array(),
		'account' => array(),
		'link' => array()
	);
	
	/**
	* Checks if a given link already exists creating it if it doesn't
	*
	* @param string $url The link url
	* @param Model_Droplet $droplet_id Droplet id this link belongs to
	* @param Model_Account $account_id Account id this link belongs to 
	* @return Model_Account_Droplet_Link
	*/
	public static function get_link($url, $droplet_id,  $account_id)
	{
		$orm_link = Model_Link::get_link_by_url($url, TRUE);
        
		$account_link = ORM::factory('Account_Droplet_Link')
                        ->where('droplet_id', '=', $droplet_id)
                        ->where('link_id', '=', $orm_link->id)
                        ->where('account_id', '=', $account_id)
                        ->find();
                        
        
		if ( ! $account_link->loaded())
		{
			$account_link->link_id = $orm_link->id;
			$account_link->droplet_id = $droplet_id;
			$account_link->account_id = $account_id;
			$account_link->save();
		}
        
		return $account_link;
	}
}
?>