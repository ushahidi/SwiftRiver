<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Account_Droplet_Media
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
class Model_Account_Droplet_Media extends ORM {
    
	/**
	 * Media belongs to a droplet
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'droplet' => array(),
		'account' => array(),
		'media' => array()
	);
	
	/**
	* Checks if a given media already exists creating it if it doesn't
	*
	* @param string $url The media url
	* @param Model_Droplet $droplet_id Droplet id this media belongs to
	* @param Model_Account $account_id Account id this media belongs to 
	* @return Model_Account_Droplet_Media
	*/
	public static function get_media($url, $droplet_id,  $account_id)
	{
		$orm_media = Model_Media::get_media_by_url($url, TRUE);
        
		$account_media = ORM::factory('account_droplet_media')
                        ->where('droplet_id', '=', $droplet_id)
                        ->where('media_id', '=', $orm_media->id)
                        ->where('account_id', '=', $account_id)
                        ->find();
                        
        
		if ( ! $account_media->loaded())
		{
			$account_media->media_id = $orm_media->id;
			$account_media->droplet_id = $droplet_id;
			$account_media->account_id = $account_id;
			$account_media->save();
		}
        
		return $account_media;
	}
}
?>