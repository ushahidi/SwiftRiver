function settingsEdit() {
	$.post('<?php echo URL::site()?>dashboard/ajax_settings', {
			username: $('#username').val(),
			email: $('#email').val(),
			oldpassword: $('#current_password').val(),
			newpassword: $('#password').val()
		},
		function(data){
			
		}, "json");
}

$(document).ready(function() {
	// Edit page contents
	$('.button_go').live('click', function() {
		alert('test');
	});
});