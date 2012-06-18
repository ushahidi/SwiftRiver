<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Plugin Details for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

return array(
    'feedwriter' => array(
        'name' => 'FeedWriter',
        'description' => 'Create RSS2 and Atom feeds from buckets.',
        'author' => 'Nick Lewis',
        'email' => 'nick@ushahidi.com',
        'version' => '0.1',
        'settings' => true,
        'channel' => false,
        'dependencies' => false,
        'service' => true
    )
);

?>
