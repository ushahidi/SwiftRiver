<?php defined('SYSPATH') or die('No direct script access');

/**
 * Welcome controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Welcome extends Controller_Template {
	
	/**
	 * Whether to automatically render the view
	 * @var bool
	 */
	public $auto_render = TRUE;
	
	/**
	 * File name of the view template
	 * @var string
	 */
	public $template = 'pages/welcome';
	
	public function before()
	{
		parent::before();
		
		// TODO
	}
	
	public function action_index()
	{
		// TODO Set the content for the view
	}
}
?>
