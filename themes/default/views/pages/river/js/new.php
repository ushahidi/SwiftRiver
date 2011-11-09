$(document).ready(function() {
	$('section.panel div.panel_body').load('<?php echo $channels; ?>');
	$('section.panel').addClass('preload');
});