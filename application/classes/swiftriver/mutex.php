<?php defined('SYSPATH') OR die('No direct access allowed.');

class Swiftriver_Mutex {
    
    
    // Since a subsequent call to GET_LOCK releases any locks held,
    // this static var prevents obtaining a second lock in the process
    // losing the earlier one causing all sorts of wierdness.
    protected static $lock_exists = FALSE;
    
    /**
	 * Get a mutex from the database
	 *
	 * @param   string   name
	 * @param   integer  timeout
	 * @return  boolean
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