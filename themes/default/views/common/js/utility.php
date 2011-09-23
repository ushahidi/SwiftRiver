function quickAction( action, confirmAction, id )
{
	var answer = confirm('<?php echo __('Are you sure you want to'); ?> ' + confirmAction + ' <?php echo __('this item'); ?>?')
	if (answer){

		// Set Submit Type
		$("#list #action").attr("value", action);

		if (id != '') {
			// Submit Form For Single Item
			$("#list #id").attr("value", id);
			$("#list").submit();
		}

	} else {
	//	return false;
	}
}