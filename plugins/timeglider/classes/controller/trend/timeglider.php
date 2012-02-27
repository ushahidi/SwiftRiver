<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Time Glider droplet visualization
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
class Controller_Trend_Timeglider extends Controller_Trend_Main {
	
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
		echo(Html::style('media/css/jquery-ui-1.8.5.custom.css'));
		echo(Html::style('media/css/Timeglider.css'));
		echo(Html::script('media/js/jquery-ui.js'));
		echo(Html::script('media/js/timeglider-0.1.3.min.js'));
	}

	public function action_index() 
	{
		$this->template->content->active = 'timeglider';        
		$this->template->content->trend =  View::factory('timeglider/index')
			->bind('json_url', $json_url)
			->bind('icon_url', $icon_url);

		if ($this->context == 'bucket')
		{
			$json_url = URL::site().$this->bucket->account->account_path.'/bucket/'.$this->bucket->bucket_name_url.'/trend/timeglider/json';
		}
		else
		{
			$json_url = URL::site().$this->river->account->account_path.'/river/'.$this->river->river_name_url.'/trend/timeglider/json';
		}

		$icon_url = URL::base().'media/img/icons/';
	}

	public function action_json()
	{
		$json = array(
				'id' => $this->context.'-'.$this->id,
				'title' => ($this->context == 'river') ? $this->river->river_name : $this->bucket->bucket_name,
				'focus_date' => '2012-02-22 13:00:00',
				'initial_zoom' => 43,
				'timezone' => '-05:00',
				'size_importance' => 'true',
				'events' => array()
			);
		
		foreach ($this->droplets['droplets'] as $droplet)
		{
			$json['events'][] = array(
					'id' => $droplet['id'],
					'title' => $droplet['droplet_title'],
					'description' => 'xxx',
					'startdate' => date('Y-m-d H:i:s', strtotime($droplet['droplet_date_pub'])),
					'link' => 'xx',
					'importance' => '30',
					'icon' => 'triangle_green.png'
				);
		}

		$this->auto_render = false;
		echo '['.json_encode($json).']';
	}
}