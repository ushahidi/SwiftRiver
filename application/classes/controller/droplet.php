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
	
	public function action_index()
	{
		$droplet_id = intval($this->request->param('id'));
		
		$semantics = $_GET['semantics'];
		
		if ( ! empty($semantics))
		{
			$droplet = ORM::factory('droplet', $droplet_id);
			
			switch ($semantics)
			{
				case 'tags':
				$tags = array();
				foreach ($droplet->tags->find_all() as $tag)
				{
					$tags[] = array(
						'id' => $tag->id,
						'tag' => $tag->tag
					);
				}
				echo json_encode($tags);
				break;
				
				case 'places':
				$places = array();
				foreach ($droplet->places->find_all() as $place)
				{
					$places[] = array(
						'id' => $place->id,
						'place_name' => $place->place_name
					);
				}
				echo json_encode($places);
				break;
				
				case 'links':
				$links = array();
				foreach ($droplet->links->find_all() as $link)
				{
					$links[] = array(
						'id' => $link->id,
						'link' => $link->link_full
					);
				}
				echo json_encode($links);
				break;
			}
		}
	}
	
	/**
	 * Gets the list of buckets
	 */
	public function action_buckets()
	{
		$this->tempalte = "";
		$this->auto_render = FALSE;
		
		$droplet_id = $this->request->param('id', 0);
		$droplet = ORM::factory('droplet', $droplet_id);
		if ($droplet->loaded())
		{
			$buckets = array();
			foreach ($droplet->buckets->find_all() as $bucket)
			{
				$buckets[] = array(
					'id' => $bucket->id,
					'bucket_name' => $bucket->bucket_name
				);
			}
			
			echo json_encode($buckets);
		}
		else
		{
			echo json_encode(array());
		}
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
	
	
	/**
	 * Handle user defined tag addition
	 * 
	 * @return	void
	 */
	public function action_ajax_add_tag()	
	{
	    $this->template = '';
		$this->auto_render = FALSE;
		
		$post = Validation::factory($this->request->post())
		                        ->rule('edit_value', 'not_empty')
		                        ->rule('edit_value', 'alpha_dash')
		                        ->rule('id', 'not_empty')
		                        ->rule('id', 'numeric');
		                        
		if ( ! $post->check())
		{
		   echo json_encode(array(
		                        'status'=>'error',
		                        'errors' => $post->errors('add_tag')		 
		   ));
		   return;
		}	        
		                        
		
	    $droplet_id = (int) $this->request->post('id');
	    $tag_name = $this->request->post('edit_value');
	    $orm_droplet = ORM::factory('droplet', $droplet_id);	    
	    	    
	    if ( ! $orm_droplet->loaded())
	        throw new HTTP_Exception_404(__('The requested droplet :droplet was not found on this server.'),
		    array(':droplet' => $droplet_id));
		
		$account_tag = Model_Account_Droplet_Tag::get_tag($tag_name, $orm_droplet, $this->account);
	    
		echo json_encode(array(
		                    'status'=>'success',
		                    'html'=>'<li><a href="#">'.$account_tag->tag->tag.'</a></li>'
		));
	}	
	
	/**
	 * Handle user defined place addition
	 * 
	 * @return	void
	 */
	public function action_ajax_add_place()	
	{
	    $this->template = '';
		$this->auto_render = FALSE;
		
		$post = Validation::factory($this->request->post())
		                        ->rule('edit_value', 'not_empty')
		                        ->rule('edit_value', 'alpha_dash')
		                        ->rule('id', 'not_empty')
		                        ->rule('id', 'numeric');
		                        
		if ( ! $post->check())
		{
		   echo json_encode(array(
		                        'status'=>'error',
		                        'errors' => $post->errors('add_place')		 
		   ));
		   return;
		}	        
		
		
	    $droplet_id = (int) $this->request->post('id');
	    $place_name = $this->request->post('edit_value');
	    $orm_droplet = ORM::factory('droplet', $droplet_id);	    
	    	    
	    if ( ! $orm_droplet->loaded())
	        throw new HTTP_Exception_404(__('The requested droplet :droplet was not found on this server.'),
		    array(':droplet' => $droplet_id));
		
		$account_place = Model_Account_Droplet_Place::get_place($place_name, $orm_droplet, $this->account);
	    
	    echo json_encode(array(
		                    'status'=>'success',
		                    'html'=>'<p class="edit"><span class="edit_trigger" title="place" id="edit_'.$account_place->place->id.'">'.$account_place->place->place_name.'</span></p>'
		));
	}

	/**
	 * Handle user defined link addition
	 * 
	 * @return	void
	 */
	public function action_ajax_add_link()	
	{
	    $this->template = '';
		$this->auto_render = FALSE;
		
		$post = Validation::factory($this->request->post())
		                        ->rule('edit_value', 'not_empty')
		                        ->rule('edit_value', 'url')
		                        ->rule('id', 'not_empty')
		                        ->rule('id', 'numeric');
		                        
		if ( ! $post->check())
		{
		   echo json_encode(array(
		                        'status'=>'error',
		                        'errors' => $post->errors('add_place')		 
		   ));
		   return;
		}		
		
	    $droplet_id = (int) $this->request->post('id');
	    $url = $this->request->post('edit_value');
	    $orm_droplet = ORM::factory('droplet', $droplet_id);	    
	    	    
	    if ( ! $orm_droplet->loaded())
	        throw new HTTP_Exception_404(__('The requested droplet :droplet was not found on this server.'),
		    array(':droplet' => $droplet_id));
		
		$account_link = Model_Account_Droplet_Link::get_link($url, $orm_droplet, $this->account);
	    
	    echo json_encode(array(
		                    'status'=>'success',
		                    'html'=>'<p class="edit"><span class="edit_trigger" title="link" id="edit_'.$account_link->link->id.'">'.$account_link->link->link_full.'</span></p>'
		));
	    
	}		
}