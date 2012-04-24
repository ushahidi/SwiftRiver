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
	 * Overload saving to perform additional functions on the channel_filter
	 */
	public function save(Validation $validation = NULL)
	{
		$ret = parent::save();
		
		// Run post_save events
		Swiftriver_Event::run('swiftriver.channel.option.post_save', $this);
		
		return $ret;
	}
	
	/**
	 * Overrides the default behaviour to perform
	 * extra tasks before removing the channel filter
	 * entry 
	 */
	public function delete()
	{
		Swiftriver_Event::run('swiftriver.channel.option.pre_delete', $this);

		// Default
		parent::delete();
	}
	
	
	
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