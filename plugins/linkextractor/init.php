<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Link Extractor Init
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

class link_extractor_init {
	
	public function __construct()
	{	
		// Hook into routing
		Event::add('sweeper.item.post_save_new', array($this, 'filter'));
	}

	public function filter()
	{
		try
		{
			// Get Item Content
			$item = Event::$data;

			$links = Links::extract($item->item_content);
			foreach ($links as $orig_link)
			{
				$full_link = Links::full($orig_link);
				if ( $orig_link == $full_link OR 
					! $full_link )
				{
					$full_link = $orig_link;
				}

				$link = ORM::factory('link')
					->where('link_full', '=', $full_link)
					->find();

				if ( ! $link->loaded() )
				{
					$link->link = $orig_link;
					$link->link_full = $full_link;
					$link->save();
				}

				if ( ! $item->has('links', $link))
				{
					$item->add('links', $link);
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

new link_extractor_init;