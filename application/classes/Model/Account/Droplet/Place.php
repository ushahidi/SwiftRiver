<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Account_Droplet_Place
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
class Model_Account_Droplet_Place extends ORM {
    
	/**
	 * A tag belongs to a droplet
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'droplet' => array(),
		'account' => array(),
		'place' => array()
	);
		    
	/**
	* Checks if a given place already exists and creates it if it doesn't
	*
	* @param string $place_name The place's name
	* @param Model_Droplet $droplet_id Droplet id this link belongs to
	* @param Model_Account $account_id Account id this link belongs to 
	* @return Model_Account_Droplet_place
	*/
	public static function get_place($place_name, $droplet_id,  $account_id)
	{
		$orm_place = Model_Place::get_place_by_name($place_name, TRUE);
        
		$account_place = ORM::factory('Account_Droplet_Place')
                        ->where('droplet_id', '=', $droplet_id)
                        ->where('place_id', '=', $orm_place->id)
                        ->where('account_id', '=', $account_id)
                        ->find();
                        
        
		if ( ! $account_place->loaded())
		{
			$account_place->place_id = $orm_place->id;
			$account_place->droplet_id = $droplet_id;
			$account_place->account_id = $account_id;
			$account_place->save();
		}
        
		return $account_place;
    }
}
?>