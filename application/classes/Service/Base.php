<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Abstract Service Class
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category    Services
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

abstract class Service_Base {
	
	/**
	 * @var SwiftRiver_API
	 */
	protected $api = NULL;
	
	public function __construct($api)
	{
		$this->api = $api;
	}
	
}