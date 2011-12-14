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
	 * @return	void
	 */
	public function action_detail()
	{
	    
	    $id = (int) $this->request->param('id', 0);
	    
	    $droplet = ORM::factory('droplet')
			->where('id', '=', $id)
			->find();
			
		if ( ! $droplet->loaded())
		    throw new HTTP_Exception_404('The requested droplet :droplet was not found on this server.',
		    array(':droplet' => $id));	
		
		echo View::factory('pages/droplets/detail')
		        ->bind('droplet', $droplet);	
			
	}
	
	/**
	 * Delete the specified droplet from the account. 
	 * Doesn't actually delete the droplet from the db but removes the link
	 * from the channel filter
	 * 
	 * @throws HTTP_Exception_404 when the droplet being deleted does not exist.	
	 * @return	void
	 */
	public function action_delete()
	{
	    $droplet_id = (int) $this->request->param('id', 0);
	    
	    $river_id_arr = DB::Select('river_id')
	                ->from('rivers_droplets')
        	        ->join('rivers', 'INNER')
        	        ->on('rivers_droplets.river_id', '=', 'rivers.id')
        	        ->where('rivers_droplets.droplet_id', '=', $droplet_id)
        	        ->where('rivers.account_id', '=', $this->account->id)
        	        ->execute()
        	        ->as_array();
        
	    if(empty($river_id_arr))
	        throw new HTTP_Exception_404('The requested droplet :droplet was not found on this server.',
		                    array(':droplet' => $droplet_id));
	    
	    $droplet = ORM::factory('droplet', $droplet_id);
	    $river = ORM::factory('river', 
	                                $river_id_arr[0]['river_id']);
			
		if ( ! $droplet->loaded() || ! $river->loaded())
		    throw new HTTP_Exception_404('The requested droplet :droplet was not found on this server.',
		    array(':droplet' => $droplet_id));	
		    
		$river->remove('droplets', $droplet);	    
	}
}