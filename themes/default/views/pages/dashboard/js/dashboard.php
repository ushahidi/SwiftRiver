$(document).ready(function() {
	// Dashboard Settings
	$('.button-go').live('click', function() {
		$.post('<?php echo URL::site()?>dashboard/ajax_settings', {
			name: $('#name').val(),
			email: $('#email').val(),
			password: $('#password').val(),
			password_confirm: $('#password_confirm').val(),
			current_password: $('#current_password').val()
		},
		function(data){
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
	});
});

function deleteItem(id, item){
	$.post('<?php echo URL::site()?>'+item+'/ajax_delete', { id: id},
		function(data){
			if ( typeof(data.status) != 'undefined' ) {
				if (data.status == 'success') {
					$('#item_'+id).remove();
				};
			}			
		}, 'json');
}