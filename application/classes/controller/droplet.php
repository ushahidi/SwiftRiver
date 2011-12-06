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
class Controller_Droplet extends Controller_Template {
    
	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'pages/droplet/detail';    
    
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
		    exit;	
		    
		$this->template->bind('droplet', $droplet);		
	}
}