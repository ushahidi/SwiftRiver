<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Droplet Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Droplet extends Controller_Swiftriver {
	
	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 */
	public $auto_render = FALSE;
	
	 /**
	  * Single droplet restful api
	  */ 
	 public function action_api()
	 {
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ( ! $this->admin)
		{
			throw new HTTP_Exception_403();
		}
		
		switch ($this->request->method())
		{
			case "POST":
				// Get the POST data
				$droplet = json_decode($this->request->body(), TRUE);
				Kohana::$log->add(Log::DEBUG, "Droplet received");
				$droplet_orm = NULL;
				try
				{
					$droplet_orm = Swiftriver_Dropletqueue::add($droplet, FALSE);
				}
				catch (DatabaseException $e)
				{
					// Do nothing
					Kohana::$log->add(Log::ERROR, "Error adding droplet: ".$e->getMessage());
				}
				
				if ($droplet_orm)
				{
					// Response with the newly created droplet
					echo json_encode($droplet_orm->as_array());
				}
			break;
		}
	 }
}