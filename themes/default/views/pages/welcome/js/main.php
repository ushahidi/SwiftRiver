<script type="text/javascript">
/**
 * Backbone.js wiring for the droplets MVC
 */
$(function(){ 
	/* spotlights carousel */
	$(".hero-statements").cycle({
		 fx: 'fade',
		 speed: 500,
		 timeout: 3300,
		 height: 250,
		 autostop: 1
	});
	
	//"reset" link
	$("a.reset").click(function(){
		$(".the-keywords").val("");
		$(".the-keywords").focus();
		return false;
	});
	
	var isPageFetching = false;
	
	function doCreateRiver() {
		if (!$(".the-keywords").val().length || isPageFetching)
			return
			
		isPageFetching = true;
		
		var loading_msg = window.loading_message.clone().append("<br />Please wait while we create your river...");
		loading_msg.appendTo($(".sign-up-box div.loading")).show();
		$("p.steps-panel-nav").fadeOut();
			
		$.ajax({
			url: "<?php echo URL::site('welcome/ajax') ?>",
			type: "POST",
			dataType: 'json',
			data: {keywords: $(".the-keywords").val()},
			complete: function(){
				loading_msg.fadeOut().remove();
				isPageFetching = false;
			},
			success: function(data) {
				if(data.status == 'OK') {
					window.location = data.url;
				} else {
					flashMessage($(".sign-up-box div.system_error"), "<?php echo __('Oops, we are unable to create you river at the moment. Try again later.'); ?>");
					$("p.steps-panel-nav").fadeIn();
				}
			},
			error: function() {
				flashMessage($(".sign-up-box div.system_error"), "<?php echo __('Oops, we are unable to create you river at the moment. Try again later.'); ?>");
				$("p.steps-panel-nav").fadeIn();
			}
		});
	}
	
	// When the next button is clicked, create the river
	$(".steps-panel-nav a.next").click(doCreateRiver);
	
	// Create the river if the enter key is pressed in the search box
	$(".the-keywords").keypress(function (e) {
		if(e.which == 13){
			doCreateRiver();
		}
	});
	
	// Focus the search box
	$(".the-keywords").focus();
});

</script>