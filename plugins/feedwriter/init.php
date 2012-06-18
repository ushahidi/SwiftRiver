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
    public function __construct()
    {
        Swiftriver_Event::add('swiftriver.template.head', array('Feedwriter_Init', 'Inject'));
    }

    public static function Inject()
    {
        echo '<script type="text/javascript">$(document).ready(function(){$(".page-title.bucket-title.cf div h1 span").after(\'<span class="rss-feed" style="margin-left: 20px; display: none;"><a href="rss" target="_blank"><img src="/themes/default/media/img/channel-rss.gif" alt="RSS" title="RSS" height="12" style="margin-bottom: 4px;" /></a></span>\');$("span.rss-feed").fadeIn()})</script>';
    }
}

new Feedwriter_Init;

// Bind the plugin to valid URLs
Route::set('feeds', '<account>/bucket/<name>/<action>',
    array(
        'action' => '(rss|atom)' // atom NYI
    ))
    ->defaults(array(
        'controller' => 'feedwriter',
        'action' => 'generate'
    ));



?>
