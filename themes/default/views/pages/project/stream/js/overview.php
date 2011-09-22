function showInfo(id){
	$( "#dialog-"+id ).dialog( "open" );
	$( "#dialog-"+id ).html('<div style="text-align:center;"><img src="<?php echo URL::base()."themes/default/media/img/loading_g2.gif"; ?>"></div>');
	$.post('<?php echo URL::base().'project/'.$project->id.'/stream/ajax_edit';?>', {
			item_id: id
		},
		function(data){
			$( "#dialog-"+id ).html(data);
			$( "#accordion-"+id ).accordion();
		});
}
$(function() {
//	$( "#accordion" ).accordion();
});