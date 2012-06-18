<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

Route::set('feeds', '<account>/bucket/<name>/<action>',
    array(
        'action' => '(rss|atom)' // atom NYI
    ))
    ->defaults(array(
        'controller' => 'feedwriter',
        'action' => 'generate'
    ));

?>

