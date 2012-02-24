<div id='placement'></div>
<script>
	$(document).ready(function () { 
		var tg1 = $("#placement").timeline({
			"data_source":"<?php echo $json_url; ?>",
			"icon_folder":"<?php echo $icon_url; ?>",
			"min_zoom":5,
			"max_zoom":100,
			"show_footer":true,
			"display_zoom_level":true,
			"event_overflow":"scroll"
		});
	});
</script>