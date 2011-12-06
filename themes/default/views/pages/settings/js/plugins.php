function pluginAction(button, action, id){
	var form = $(button).parents('form:first');
	$("input[name='action']",form).val(action);
	$("input[name='id']",form).val(id);
	form.submit();
}