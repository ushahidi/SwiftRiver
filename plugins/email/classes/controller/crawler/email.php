<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Email Crawler Controller
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
class Controller_Crawler_Email extends Controller_Crawler_Main {

	/**
	 * This Project
	 */
	protected $project;

	/**
	 * This Feed
	 */
	protected $feed;

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

	}
}