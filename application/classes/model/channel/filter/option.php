<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Channel_Filter_Options
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
class Model_Channel_Filter_Option extends ORM {
	
	/**
	 * A channel_filter_option belongs to a channel_filter
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'channel_filter' => array()
		);
	
	
	/**
	 * Parses the "value" column of the channel filter option and returns it 
	 * as an array
	 *
	 * @return array
	 */
	public function get_option_as_array()
	{
		// Decode the JSON string for the options
		$options =  json_decode($this->value, TRUE);
		
		return array($this->key => $options);
	}
}
?>