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
 * @category   Plugins
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class open_calais_init {
	
	public function __construct()
	{	
		// Hook into routing
		Swiftriver_Event::add('swiftriver.droplet.extract_entities', array($this, 'filter'));
	}

	public function filter()
	{
		// Include OpenCalais Class
		include_once Kohana::find_file('vendor', 'dg_open_calais/opencalais');

		// Get Our Service Key
		$settings = ORM::factory('opencalais_setting')
			->where('key', '=', 'service_key')
			->find();

		if ($settings->loaded() AND $settings->value )
		{
			try
			{
				// Initialize Open Calais
				$oc = new OpenCalais($settings->value);
				$oc->setPrettyTypes = FALSE;

				// Get the droplet content
				$droplet = Event::$data;
				$content = $droplet['droplet_content'];

				// Retrieve Entities from Open Calais
				$entities = $oc->getEntities($content);
				
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
									// Add the extracted entities to the list of tags
									array_push($entity, $droplet['tags']);
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