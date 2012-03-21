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
	
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		Swiftriver_Event::add('swiftriver.template.head', array($this, 'template_header'));
	}
	
	/**
	 * Hook into the page header
	 * 
	 * @return	void
	 */
	public function template_header()
	{
		echo(Html::style('media/css/map.css'));
		echo(Html::style('media/css/colorbox.css'));
		echo(Html::script('http://openlayers.org/api/OpenLayers.js'));
		echo(Html::script('media/js/jquery.colorbox-min.js'));
		echo(Html::script('media/js/map.js'));
	}
	
	public function action_index() 
	{       
		$this->template->content->active = 'map';        
		$this->template->content->trend =  View::factory('map/index')
						->bind("geojson_url", $geojson_url)
						->bind("droplet_base_url", $droplet_base_url);
		if ($this->context == 'bucket')
		{
			$geojson_url = URL::site().$this->bucket->account->account_path.'/bucket/'.$this->bucket->bucket_name_url.'/trend/map/geojson';
		}
		else
		{
			$geojson_url = URL::site().$this->river->account->account_path.'/river/'.$this->river->river_name_url.'/trend/map/geojson';
		}
		$droplet_base_url = url::site().'droplet/detail/';
	}
	
	/**
	 * Return GeoJSON representation of the river
	 *
	 */
	public function action_geojson() 
	{
		if ($this->context == 'river')
		{
			$droplets_array = $this->_get_geo_river($this->id);
		}
		else if ($this->context == 'bucket')
		{
			$droplets_array = $this->_get_geo_bucket($this->id);
		}
		
		//Prepare the GeoJSON object
		$ret{'type'} = 'FeatureCollection';
		$ret{'features'} = array();
		
		//Add each droplet as a feature with point geometry and the droplet details
		//as the feature attributes
		foreach ($droplets_array['droplets'] as $droplet) 
		{
			$geo_droplet['type'] = 'Feature';
			$geo_droplet['geometry'] = array(
				'type' => 'Point',
				'coordinates' => array($droplet['longitude'], $droplet['latitude'])
			);
			$geo_droplet['properties'] = array(
				'droplet_id' => $droplet['id'],
				'droplet_title' => $droplet['droplet_title'],
				'droplet_content' => $droplet['droplet_content']
			);
			$ret{'features'}[] = $geo_droplet;
		}
		
		$this->auto_render = false;
		echo json_encode($ret);
	}
	
	/**
	 * Get geotagged droplets from a River
	 *
	 * @param int $id ID of the river	
	 */
	 private function _get_geo_river($id = NULL) 
	 {
		 $droplets = array(
			'total' => 0,
			'droplets' => array()
			);
			
		 if ($id) 
		 {
		 	$cached = Cache::instance('redis')->get('river.trends');

		 	if ( ! isset($cached[$id]))
		 	{
				$query = DB::select('droplets.id', 'droplet_title', 
									'droplet_content', 'droplets.channel',
									'identity_name', 'identity_avatar', 
									'droplet_date_pub', 
									array(DB::expr('X(place_point)'), 'longitude'), 
									array(DB::expr('Y(place_point)'), 'latitude'))
					->from('droplets')
					->join('rivers_droplets', 'INNER')
					->on('rivers_droplets.droplet_id', '=', 'droplets.id')
					->join('identities')
					->on('droplets.identity_id', '=', 'identities.id')
					->join('droplets_places')
					->on('droplets_places.droplet_id', '=', 'droplets.id')
					->join('places')
					->on('droplets_places.place_id', '=', 'places.id')
					->where('rivers_droplets.river_id', '=', $id);

				// Get our droplets as an Array		
				$droplets['droplets'] = $query->execute()->as_array();

				// Set 5 minute Cache
				Cache::instance('redis')->set('river.trends', array( $id => $droplets['droplets']), 300 );
		 	}
		 	else
		 	{
		 		//print_r($cached[$id]);
		 		$droplets['droplets'] = $cached[$id];
		 	}
			 
			
			$droplets['total'] = (int) count($droplets['droplets']);
		 }
		 
		 return $droplets;
	 }    

	/**
	 * Get geotagged droplets from a Bucket
	 *
	 * @param int $id ID of the bucket	
	 */
	 private function _get_geo_bucket($id = NULL) 
	 {
		 $droplets = array(
			'total' => 0,
			'droplets' => array()
			);

		 if ($id) 
		 {
			$query = DB::select('droplets.id', 'droplet_title', 
								'droplet_content', 'droplets.channel',
								'identity_name', 'identity_avatar', 
								'droplet_date_pub', 
								array(DB::expr('X(place_point)'), 'longitude'), 
								array(DB::expr('Y(place_point)'), 'latitude'))
				->from('droplets')
				->join('buckets_droplets', 'INNER')
				->on('buckets_droplets.droplet_id', '=', 'droplets.id')
				->join('identities')
				->on('droplets.identity_id', '=', 'identities.id')
				->join('droplets_places')
				->on('droplets_places.droplet_id', '=', 'droplets.id')
				->join('places')
				->on('droplets_places.place_id', '=', 'places.id')
				->where('buckets_droplets.bucket_id', '=', $id);

			 // Get our droplets as an Array		
			$droplets['droplets'] = $query->execute()->as_array();
			$droplets['total'] = (int) count($droplets['droplets']);
		 }

		 return $droplets;
	 }

}

?>
