<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Forbidden Exception
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @subpackage  Exceptions
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

abstract class Swiftriver_API_Exception extends Exception {
	
	protected $message = NULL;
	
	protected $errors = NULL;
	
	public function __construct(array $error) 
	{
		$this->message = $error['message'];
		
		if (isset($error['errors']))
		{
			$this->errors = $error['errors'];
		}
	}
	
	public function get_errors()
	{
		return $this->errors;
	}
}
