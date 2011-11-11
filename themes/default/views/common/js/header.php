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

var ci = 0;
function channelOption(channel, option, label){
	if ( typeof (channel) != 'undefined' && channel ) {
		ci++;
		$('#'+channel).append('<div class="input" id="channel_option_'+ci+'"><h3>'+label+' [ <a href="javascript:channelOptionR(\'channel_option_'+ci+'\')">&#8212;</a> ]</h3><input type="text" name="'+channel+'_'+option+'[]" /></div>');
	}
}

function channelOptionR(id){
	if ( typeof (id) != 'undefined' && id ) {
		$('#'+id).remove();
	}
}