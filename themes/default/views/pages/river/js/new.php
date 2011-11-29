$(document).ready(function() {
	$('section.panel div.panel_body').load('<?php echo $settings; ?>');
	$('section.panel').addClass('preload');
});