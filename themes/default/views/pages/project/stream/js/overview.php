var dialog;
function showInfo(id){
	$( ".ui-dialog-content" ).dialog("close"); // Close any dialogs that might be open
	dialog = "#dialog-"+id;
	$( dialog ).dialog( "open" );
	getInfo(id);
}
function getInfo(id){
	$( dialog ).html( '' );
	$( dialog ).html('<div style="text-align:center;"><img src="<?php echo URL::base()."themes/default/media/img/loading_g2.gif"; ?>"></div>');
	$.post('<?php echo URL::base().'project/'.$project->id.'/stream/ajax_edit';?>', {
			item_id: id
		},
		function(data){
			$( dialog ).html(data);
			$( "#accordion-"+id ).accordion();
			$( "#slider-"+id ).selectToUISlider({labels: 5}).hide();
		});
}