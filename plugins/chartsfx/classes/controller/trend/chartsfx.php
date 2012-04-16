<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * ChartsFX visualization
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

Class Controller_Trend_Chartsfx extends Controller_Trend_Main {

	public function before()
	{
		// Execute parent::before first
		parent::before();

		// For adding our js/css to the header
		Swiftriver_Event::add('swiftriver.template.head', array($this, '_template_header'));

		if ($this->context == 'bucket')
		{
			$this->flare_url = URL::site().$this->bucket->account->account_path.'/bucket/'.$this->bucket->bucket_name_url.'/trend/chartsfx/flare';
		}
		else
		{
			$this->flare_url = URL::site().$this->river->account->account_path.'/river/'.$this->river->river_name_url.'/trend/chartsfx/flare';
		}
	}

	public function action_bubble()
	{
		$this->trend = View::factory('chartsfx/bubble')
			->bind('flare_url', $this->flare_url);		
	}

	public function action_sunburst()
	{
		$this->trend = View::factory('chartsfx/sunburst')
			->bind('flare_url', $this->flare_url);		
	}

	public function action_cluster()
	{
		$this->trend = View::factory('chartsfx/cluster')
			->bind('flare_url', $this->flare_url);		
	}		

	public function action_flare()
	{
		// Get Top Tags
		$tags = DB::select( 'tags.tag', array(DB::expr('COUNT(*)'), 'tag_count') )
			->from('droplets_tags')
			->join('tags', 'INNER')
				->on('droplets_tags.tag_id', '=', 'tags.id')
			->join($this->context.'s_droplets', 'INNER')
				->on('droplets_tags.droplet_id', '=', $this->context.'s_droplets.droplet_id')
			->where($this->context.'s_droplets.'.$this->context.'_id', '=', $this->id)
			->group_by('tags.tag')
			->order_by('tag_count', 'DESC')
			->limit(200)
			->execute()
			->as_array();
		
		// Shuffling the array gives a much better visual
		shuffle($tags);

		$json = array(
				'name' => 'tags',
				'children' => array()
			);

		//++ TODO - Group tags by entity type (organization, person, location etc)
		foreach ($tags as $tag)
		{
			$json['children'][] = array(
					'name' => $tag['tag'],
					'children' => array(array(
							'name' => $tag['tag'],
							'size' => $tag['tag_count']
							)
						)
				);
		}

		$this->auto_render = false;
		echo json_encode($json);
	}

	public function action_flare2()
	{
		// Get Top Tags
		$tags = DB::select( 'tags.tag', 'tags.tag_type', array(DB::expr('COUNT(*)'), 'tag_count') )
			->from('droplets_tags')
			->join('tags', 'INNER')
				->on('droplets_tags.tag_id', '=', 'tags.id')
			->join($this->context.'s_droplets', 'INNER')
				->on('droplets_tags.droplet_id', '=', $this->context.'s_droplets.droplet_id')
			->where($this->context.'s_droplets.'.$this->context.'_id', '=', $this->id)
			->group_by('tags.tag')
			->order_by('tag_count', 'DESC')
			->limit(100)
			->execute()
			->as_array();
		
		// Shuffling the array gives a much better visual
		shuffle($tags);

		$json = array(
				'name' => 'tags',
				'children' => array()
			);

		//++ TODO - Group tags by entity type (organization, person, location etc)
		$new_array = array();
		foreach ($tags as $tag)
		{
			$new_array[$tag['tag_type']][] = array(
					'name' => $tag['tag'],
					'size' => $tag['tag_count']
				);
		}

		foreach ($new_array as $key => $value)
		{
			$json['children'][] = array(
					'name' => $key,
					'children' => $value
				);
		}

		$this->auto_render = false;
		echo json_encode($json);
	}	

	/**
	 * Hook into the page header
	 * 
	 * @return	void
	 */
	public function _template_header()
	{
		echo(Html::style('media/css/chartsfx.css'));
		if ($this->request->action() == 'cluster')
		{
			echo(Html::style('media/css/chartsfx_cluster.css'));
		}
		echo(Html::script('media/js/d3.v2.min.js'));
	}
}