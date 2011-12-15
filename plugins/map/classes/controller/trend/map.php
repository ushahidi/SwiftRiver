<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Map droplet visualization
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Libraries
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Trend_Map extends Controller_Trend_Main {
    
    public function action_index() {       
        
        $id = $this->request->param('id');
        
        $this->template->content->active = 'map';        
        $this->template->content->trend =  View::factory('map/index')
                        ->bind("geojson_url", $geojson_url)
                        ->bind("droplet_base_url", $droplet_base_url);
                        
        $geojson_url = url::site().'river/geojson/'.$id;
        $droplet_base_url = url::site().'droplet/detail/';
    }
        
}

?>
