<?php header("content-type: application/x-javascript");

/**
 * Javascript RSS Icon Injection for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author	Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

echo '$(document).ready(function(){$(".page-title.bucket-title.cf div h1 span").after(\'<span class="rss-feed" style="margin-left: 20px; display: none;"><a href="rss'.($_GET['t'] ? '?t='.$_GET['t'] : '').'" target="_blank"><img src="/themes/default/media/img/channel-rss.gif" alt="RSS" title="RSS" height="12" style="margin-bottom: 4px;" /></a></span>\');$("span.rss-feed").fadeIn()})';
?>
