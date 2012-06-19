<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

class Feedwriter_Init
{
    public static function Inject()
    {
        echo Html::script("plugins/feedwriter/media/js/icon.js");
    }
}

Swiftriver_Event::add('swiftriver.template.head', array('Feedwriter_Init', 'Inject'));

// Bind the plugin to valid URLs
Route::set('feeds', '<account>/bucket/<name>/<action>',
    array(
        'action' => '(rss|atom)'
    ))
    ->defaults(array(
        'controller' => 'feedwriter',
        'action' => 'generate'
    ));



?>
