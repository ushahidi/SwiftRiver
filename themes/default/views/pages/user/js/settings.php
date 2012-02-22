$(document).ready(function() {
	
	// Do the save
	function saveAccountSettings() {
		$("div.panel-body #messages").html("");
		$(".actions .dropdown .container").hide();
		var loading_msg = window.loading_message.clone();
		loading_msg.appendTo($("div.panel-body .loading")).append("<?php echo __('Saving...') ?>");
		
		$.post("<?php echo URL::site().$account->account_path.'/ajax_settings'?>", {
			name: $('#name').val(),
			nickname: $('#nickname').val(),
			email: $('#email').val(),
			password: $('#password').val(),
			password_confirm: $('#password_confirm').val(),
			current_password: $('#current_password').val()
		},
		function(data){
			loading_msg.fadeOut();
			if ( typeof(data.status) != 'undefined' ) {
				if (data.status == 'success') {
					$('#messages').html('<div class="system_message system_success"><p><strong><?php echo __('Success!'); ?></strong> <?php echo __('Your settings have been saved'); ?>.</p></div>');
					$('#password').val('');
					$('#password_confirm').val('');
				} else if (data.status == 'error') {
					var errors = data.errors;
					var html = '';
					for (i in errors){
						html += '<div class="system_message system_error"><p><strong><?php echo __('Uh oh.'); ?></strong> '+errors[i]+'</p></div>';
					}
					$('#messages').html(html);
				};
			}
		}, 'json');
		
	}	
	
	// Current password dropdown
	$('.save-account-settings .actions .button-go').live('click', function(e) {
		
		// Request current password only if password / email is changing
		if(($('#password').val() && $('#password_confirm').val()) || ($('#email').val() != $('#orig_email').val())) {
			$(this).toggleClass('active');
			$(".actions .dropdown .container").show();
			$('#messages').html("");
			$('#current_password').val("");
			$(this).siblings('.dropdown').fadeToggle('fast')
			e.stopPropagation();
		} else {
			$(this).toggleClass('active');
			$(".actions .dropdown .container").hide();
			$('#messages').html("");
			$(this).siblings('.dropdown').fadeToggle('fast')
			e.stopPropagation();			
			saveAccountSettings()
		}
		return false;
	});
	
	// Dashboard Settings
	$('.save-account-settings .actions .confirm').live('click', function() {
		saveAccountSettings();
	});	
});
