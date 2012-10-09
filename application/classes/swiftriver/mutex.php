<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Concurrency control class. Allows a mutex to be acquired before executing
 * a function and thereafter released once execution is complete. Mutex
 * acquisition is achieved through a GET_LOCK request to the DB. A RELEASE_LOCK
 * DB call results in the 'dropping' of the lock 
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Libraries
 * @copyright  (c) 2012 Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
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
			throw new Swiftriver_Exception_Mutex(__('Another lock already exists'));
			
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
		else
		{
			throw new Swiftriver_Exception_Mutex(__('Unable to obtain lock'));
		}
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
		
		throw new Swiftriver_Exception_Mutex(__('Unable to release lock'));
	}
}
?>