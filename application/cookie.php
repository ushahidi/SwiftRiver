<?php defined('SYSPATH') or die('No direct script access');
/**
 * Cookie configuration
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Cookie config
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */

return array(

	// Restrict the domain the cookie is available to
    'domain' => '',

    // Only transmit cookies over secure connections
    'secure' => FALSE,

    // Magic salt to add to the cookie
    // NOTE: This is the default value and MUST be changed on your deployment
    'salt' => 'WqLHtxZ3X4iGu%<CceGZwR3dAd?3Z4BW',

    // Number of seconds before the cookie expires
    'expiration' => 0
);
