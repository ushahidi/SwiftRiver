<?php defined('SYSPATH') or die('No direct script access.');

abstract class Session extends Kohana_Session {
	
	/**
	 * Override's Kohana's default session adapter with the adapter provided
	 * in the site'
	 * 
	 * @param   string   type of session (native, cookie, etc)
	 * @param   string   session identifier
	 * @return  Session
	 * @uses    Kohana::$config
	 */
	public static function instance($type = NULL, $id = NULL)
	{
		// Load the configuration for this type
		$default_session_adapter = Kohana::$config->load('site')->get('default_session_adapter');
		
		if ($default_session_adapter)
		{
			$type = $default_session_adapter;
		}
		
		return parent::instance($type, $id);
	}
}

?>
