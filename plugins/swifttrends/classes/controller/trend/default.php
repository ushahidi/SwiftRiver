<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Default Swift Trends Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Trend_Default extends Controller_Trend_Main {

	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		Swiftriver_Event::add('swiftriver.template.head', array($this, 'template_header'));

		if ($this->context == 'bucket')
		{
			$this->context_base_url = $this->bucket->account->account_path.'/bucket/'.$this->bucket->bucket_name_url;
		}
		else
		{
			$this->context_base_url = $this->river->account->account_path.'/river/'.$this->river->river_name_url;
		}		
	}

	public function action_index()
	{
		$this->trend = View::factory('swifttrends/content')
			->bind('trend_title', $trend_title);

		$trend_title = 'Top 10';
	}

	/**
	 * Tag Trends
	 * 
	 * @return	void
	 */
	public function action_tags()
	{
		$this->action_index();
	}
	
	/**
	 * Link Trends
	 * 
	 * @return	void
	 */	
	public function action_links()
	{
		$this->action_index();
	}

	/**
	 * Media Trends
	 * 
	 * @return	void
	 */
	public function action_media()
	{
		$this->action_index();
	}	

	/**
	 * Place Trends
	 * 
	 * @return	void
	 */
	public function action_places()
	{
		$this->action_index();
	}

	/**
	 * The Rickshaw Javascript
	 * 
	 * @return	void
	 */
	public function action_js()
	{
		$this->auto_render = false;

		$trend = $this->request->query('t');

		switch ($trend) {
			case 'links':
				$data = $this->_get_data('links', 'link_id', 'url');
				break;

			case 'media':
				$data = $this->_get_data('media', 'media_id', 'media');
				break;

			case 'places':
				$data = $this->_get_data('places', 'place_id', 'place_name');
				break;								
			
			default:
				$data = $this->_get_data();
				break;
		}

		echo View::factory('swifttrends/js')
			->bind('data', $data);
	}

	/**
	 * Hook into the page header
	 * 
	 * @return	void
	 */
	public function template_header()
	{
		echo(Html::style('media/css/ui-lightness/jquery-ui-1.8.19.custom.css'));
		echo(Html::style('media/css/graph.css'));
		echo(Html::style('media/css/detail.css'));
		echo(Html::style('media/css/legend.css'));
		echo(Html::style('media/css/extensions.css'));

		echo(Html::script($this->context_base_url.'/trend/default/js?t='.Request::$current->action()));
		echo(Html::script('media/js/jquery-ui-1.8.19.custom.min.js'));
		echo(Html::script('media/js/d3.v2.min.js'));
		echo(Html::script('media/js/rickshaw.min.js'));
		echo(Html::script('media/js/extensions.js'));
	}


	private function _get_data(
		$table = 'tags',
		$table_join = 'tag_id',
		$entity = 'tag', 
		$days = 1)
	{
		$data = array();

		// If we have cache engine, retrieve any set keys
		if ($this->cache)
		{
			try
			{
				$cached = $this->cache->get($this->context.'.trends.default.'.$table.'.'.$this->id);
			}
			catch (Cache_Exception $e)
			{
				// Do nothing, just log it
			}				
		}

		if ( ! isset($cached) OR is_null($cached) )
		{
			// Get Top Entities
			$items = DB::select( $table.'.id', $table.'.'.$entity, array(DB::expr('COUNT(*)'), 'count') )
				->from('droplets_'.$table)
				->join($table, 'INNER')
					->on('droplets_'.$table.'.'.$table_join, '=', $table.'.id')
				->join($this->context.'s_droplets', 'INNER')
					->on('droplets_'.$table.'.droplet_id', '=', $this->context.'s_droplets.droplet_id')
				->where($this->context.'s_droplets.'.$this->context.'_id', '=', $this->id)
				->group_by($table.'.'.$entity)
				->order_by('count', 'DESC')
				->limit(10)
				->execute()
				->as_array();

			$items = array_reverse($items);

			// For each Entity get the hourly time trend for X days
			$i = 0;
			foreach ($items as $item)
			{
				$data[$i]['color'] = "palette.color()";
				$data[$i]['name'] = $item[$entity];
				$data[$i]['data'] = $this->_prefill($days);

				$dates = DB::select( array(DB::expr('DATE_FORMAT(droplet_date_pub, \'%Y/%m/%d %H:00:00\')'), 'date_pub'),
					array(DB::expr('COUNT(*)'), 'date_count') )
					->from('droplets')
					->join('droplets_'.$table, 'INNER')
						->on('droplets.id', '=', 'droplets_'.$table.'.droplet_id')
					->where('droplets_'.$table.'.'.$table_join, '=', $item['id'])
					->where('droplet_date_pub', '<=', DB::expr('DATE_ADD(NOW(), INTERVAL '.$days.' DAY)') )
					->group_by('date_pub')
					->order_by('date_pub', 'ASC')
					->execute();

				foreach ($dates as $date)
				{
					foreach ($data[$i]['data'] as $key => $value)
					{
						if ($data[$i]['data'][$key]['x'] == strtotime($date['date_pub']))
						{
							$data[$i]['data'][$key]['y'] = (int) $date['date_count'];
						}
					}
				}

				$i++;
			}

			$data = $this->_json_encode($data);

			// If we have cache engine, set the key
			if ($this->cache)
			{
				// Set 2 minute Cache
				try
				{
					$cached = $this->cache->set($this->context.'.trends.default.'.$table.'.'.$this->id, $data, 120 );

				}
				catch (Cache_Exception $e)
				{
					// Do nothing, just log it
				}					
			}			
		}
		else
		{
			$data = $cached;
		}

		return $data;
	}

	/**
	 * The Rickshaw Javascript
	 * Charting requires that all elements in the series have equal number
	 * of x/y values, so we'll prefill with zero(0) values for X days
	 * 
	 * @param int $days number of days for which to create a prefill
	 * @return	array $prefill
	 */
	private static function _prefill($days)
	{
		$duration = $days * 24;
		$now = strtotime(date('Y-m-d H'.':00:00'));
		$prefill = array();
		for ($i=$duration; $i >= 0; $i--)
		{
			$prefill[] = array(
				'x' => ($now - ($i * 3600)),
				'y' => 0
				);
		}

		return $prefill;
	}

	/**
	 * Dirty Hack to add JS functions to valid JSON
	 * 
	 * @param array valid JSON
	 * @return array invalid json with embedded javascript function
	 */
	private static function _json_encode($array)
	{
		return preg_replace_callback(
			'/(?<=:)"palette.color\((?:(?!}").)"/',
			function ($string) {
				return str_replace('\\"','\"',substr($string[0],1,-1));
			},
			json_encode($array)
		);
	}	

}