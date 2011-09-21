<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Crawler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Crawler_Main extends Controller {

	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
	}

	public function action_index()
	{
		// Get all the available services
		$services = Plugins::services();
		foreach ($services as $key => $value)
		{
			try
			{
				Request::factory('crawler/'.$key)->execute();
			}
			catch (Exception $e)
			{
				// Probably doesn't have a crawler
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
				//continue;
			}
		}
	}

}