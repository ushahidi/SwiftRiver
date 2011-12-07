function inlineEdit() {
	inputID = $('#inline_edit_id').val();
	inputName = $('#inline_edit_name').val();
	inputValue = $('#inline_edit_text').val();

	if ( (typeof (inputID) != 'undefined' && inputID) && (typeof (inputName) != 'undefined' && inputName) && (typeof (inputValue) != 'undefined' && inputValue) ) {
		$.post('<?php echo URL::site()?>'+inputName+'/ajax_title', { edit_id: inputID, edit_value: inputValue },
			function(data){
				$('button.cancel').parent().remove();
				$('.edit_input').replaceWith('<span class="edit_trigger" title="'+ inputName +'" id="'+ inputID +'" onclick="">' + inputValue + '</span>');
			}, "json");
	}
}


var inlineInputValue;
$(document).ready(function() {

	// Inline Editing
	$('.edit_trigger').live('click', function() {
		inlineInputValue = $(this).text();
		inlineInputType = $(this).attr('title');
		inlineInputId = $(this).attr('id').replace(/[^0-9]/g, '');
		$(this).replaceWith('<span class="edit_input"><input type="hidden" id="inline_edit_id" value="'+ inlineInputId +'"><input type="hidden" id="inline_edit_name" value="'+ inlineInputType +'"><input type="text" id="inline_edit_text" value="'+ inlineInputValue +'" placeholder="Enter the name of your River"></span>');
		$('.edit').append('<div class="buttons"><button class="save" onclick="inlineEdit()"><?php echo __('Save'); ?></button><button class="cancel"><?php echo __('Cancel'); ?></button></div>');
		$('button.cancel').click(function() {
			$(this).parent().remove();
			$('.edit_input').replaceWith('<span class="edit_trigger" title="'+ inlineInputType +'" id="edit_'+ inlineInputId +'" onclick="">' + inlineInputValue + '</span>');
		});
	});

	// Create new bucket
	$('li.create_new').live('click', function(e) {
		$(this).empty();
		$(this).parents('ul.dropdown').append('<li class="create_name"><input type="text" id="bucket_name" name="bucket_name" value="" placeholder="<?php echo __('Name your new bucket'); ?>"><div class="buttons"><button class="save"><?php echo __('Save'); ?></button><button class="cancel"><?php echo __('Cancel'); ?></button></div></li>');
		e.stopPropagation();
		$('li.create_name').click(function(e) {
			e.stopPropagation();	
		});
		$('button.save').click(function(e) {
			$.post('<?php echo URL::site()?>bucket/ajax_new', { bucket_name: $('#bucket_name').val() },
			function(data){
				if ( typeof(data.status) != 'undefined' ) {
					if (data.status == 'success') {
						$('<li>'+data.bucket+'</li>').insertBefore('li.create_new');
						$('button.cancel').closest('ul.dropdown').children('li.create_new').append('<a onclick=""><span class="create_trigger"><em>Create new</em></span></a>');
						$('button.cancel').closest('li.create_name').remove();
						e.stopPropagation();
					} else if (data.status == 'error') {
						var errors = data.errors;
						var html = '';
						for (i in errors){
							html += '<?php echo __('Uh oh.'); ?> '+errors[i]+'\n';
						}
						alert(html);
					};
				}
			}, 'json');			
		});
		$('button.cancel').click(function(e) {
			$(this).closest('ul.dropdown').children('li.create_new').append('<a onclick=""><span class="create_trigger"><em><?php echo __('Create new'); ?></em></span></a>');
			$(this).closest('li.create_name').remove();
			e.stopPropagation();
		});
	});
});