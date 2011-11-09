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