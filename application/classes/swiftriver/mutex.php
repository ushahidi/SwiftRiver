<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Concurrency control class. Allows a mutex to be acquired before executing
 * a function and thereafter released once execution is complete. Mutex
 * acquisition is achieved through a GET_LOCK request to the DB. A RELEASE_LOCK
 * DB call results in the 'dropping' of the lock 
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage  Libraries
 * @copyright   (c) 2012 Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Swiftriver_Mutex {
	
	
	/**
	 * Since a subsequent call to GET_LOCK releases any locks held,
	 * this static var prevents obtaining a second lock in the process
	 * losing the earlier one causing all sorts of wierdness.
	 * @var bool
	 */
	protected static $lock_exists = FALSE;
	
	/**
	 * Get a mutex from the database
	 *
	 * @param	string	 name
	 * @param	integer	 timeout
	 * @return	boolean
	 */	   
	public static function obtain($name, $timeout = 1)
	{
		if (self::$lock_exists)
			return FALSE;
			
		$query = DB::query(Database::SELECT, 'SELECT GET_LOCK(:name,:timeout) ret;');
		$query->parameters(array(
			':name' => $name,
			':timeout' => $timeout,
		));
		$result = $query->execute()->get('ret', 0);
		
		if ($result) 
		{
			self::$lock_exists = TRUE;
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Release a lock obtained via the obtain method
	 *
	 * @param  string  $name Name of the lock to be released
	 * @return bool
	 */		   
	public static function release($name)
	{
		$query = DB::query(Database::SELECT, 'SELECT RELEASE_LOCK(:name) ret;');
		$query->parameters(array(
			':name' => $name
		));
		$result = $query->execute()->get('ret', 0);
		
		if ($result) 
		{
			self::$lock_exists = FALSE;
			return TRUE;			
		}
		
		return FALSE;
	}
}
?>