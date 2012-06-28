<?php header("content-type: application/x-javascript");
echo '$(document).ready(function(){$(".page-title.bucket-title.cf div h1 span").after(\'<span class="rss-feed" style="margin-left: 20px; display: none;"><a href="rss?t='.$_GET['t'].'" target="_blank"><img src="/themes/default/media/img/channel-rss.gif" alt="RSS" title="RSS" height="12" style="margin-bottom: 4px;" /></a></span>\');$("span.rss-feed").fadeIn()})';
?>
