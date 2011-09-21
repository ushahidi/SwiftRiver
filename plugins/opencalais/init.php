<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Open Calais Init
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

class open_calais_init {
	
	public function __construct()
	{	
		// Hook into routing
		Event::add('sweeper.item.pre_save', array($this, 'filter'));
	}

	public function filter()
	{
		// Include OpenCalais Class
		include_once Kohana::find_file('vendor', 'dg_open_calais/opencalais');

		// Get Our Service Key
		$settings = ORM::factory('opencalais_setting')
			->where('key', '=', 'service_key')
			->find();

		if ( $settings->loaded() AND $settings->value )
		{
			try
			{
				// Initialize Open Calais
				$oc = new OpenCalais($settings->value);
				$oc->setPrettyTypes = FALSE;

				// Get Item Content
				$item = Event::$data;
				$content = $item->item_content;

				// Retrieve Entities from Open Calais
				$entities = $oc->getEntities($content);
				//print_r($entities);
				if (is_array($entities))
				{
					foreach ($entities as $type => $values)
					{
						// We don't want URL's since we're already
						// extracting them
						if ( $type != 'URL' AND is_array($values) )
						{
							foreach ($values as $entity)
							{
								if ($entity)
								{
									$tag = ORM::factory('tag')
										->where('tag', '=', $entity)
										->find();

									if ( ! $tag->loaded() )
									{
										$tag->tag = $entity;
										$tag->tag_type = $type;
										$tag->tag_source = 'opencalais';
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
				}
			}
			catch (Exception $e)
			{
				// Some kind of Open Calais error
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
			}
		}
	}
}

new open_calais_init;