<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Yahoo Placemaker Settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Placemaker_Setting extends ORM
{
	/**
	 * Validation for open calais settings
	 * @param array $arr
	 * @return array
	 */
	public function validate($arr)
	{
		return Validation::factory($arr)
			->rule('appid', 'not_empty');
	}
}