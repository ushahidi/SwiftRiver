<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Account_Droplet_Place
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
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
     * @param Model_Droplet $orm_droplet Droplet this tag belongs to
     * @param Model_Account $orm_account Account this tag belongs to 
     * @return Model_Account_Droplet_place
     */
    public static function get_place($place_name, $orm_droplet,  $orm_account)
    {    	
        $orm_place = Model_Place::get_place_by_name($place_name, TRUE);
        
        $account_place = ORM::factory('account_droplet_place')
                        ->where('droplet_id', '=', $orm_droplet->id)
                        ->where('place_id', '=', $orm_place->id)
                        ->where('account_id', '=', $orm_account->id)
                        ->find();
                        
        
        if ( ! $account_place->loaded())
        {
            $account_place->place_id = $orm_place->id;
            $account_place->droplet_id = $orm_droplet->id;
            $account_place->account_id = $orm_account->id;            
            $account_place->save();
        }
        
        return $account_place;
    }
}
?>