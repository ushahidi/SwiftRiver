<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * TagThe.net Init
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Inits
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class tag_the_net_init {
	
	public function __construct()
	{	
		// Hook into routing
		Event::add('sweeper.item.pre_save', array($this, 'filter'));
	}

	public function filter()
	{
		try
		{
			// Get Item Content
			$item = Event::$data;
			$content = $item->item_content;

			$tag_url = 'http://tagthe.net/api/?text='.urlencode($content);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $tag_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
			$data = curl_exec($ch);
			curl_close($ch);

			if ($xml = @simplexml_load_string($data))
			{
				$data = $xml->xpath("//dim");

				foreach($data as $entities)
				{
					foreach ($entities->item as $entity)
					{
						$tag = ORM::factory('tag')
							->where('tag', '=', $entity)
							->find();

						if ( ! $tag->loaded() )
						{
							$tag->tag = $entity;
							$tag->tag_type = strtolower($entities->attributes());
							$tag->tag_source = 'tagthenet';
							$tag->save();
						}

						if ( ! $item->has('tags', $tag))
						{
							$item->add('tags', $tag);
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
			// Some kind of error occurred
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
		}
	}
}

new tag_the_net_init;