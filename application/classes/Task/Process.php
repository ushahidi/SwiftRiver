<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Crawler Task
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    SwiftRiver - https://github.com/ushahidi/SwiftRiver
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL)
 */

class Task_Process extends Minion_Task {

	// Run crawling scheduler
	protected function _execute(array $params)
	{
		Swiftriver::do_fork(function() {
			Swiftriver_Dropletqueue::process();;
		}, Swiftriver::CRAWL_MUTEX);
	}

}
?>
