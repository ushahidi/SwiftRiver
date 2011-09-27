$(document).ready(function(){
	getCharts();
});

function getCharts(){
	//$("#charts").html('<div style="text-align:center;"><img src="<?php echo URL::base()."themes/default/media/img/loading_g2.gif"; ?>"></div>');

	var ajaxDataRenderer = function(url, plot, options) {
		var ret = null;
		$('#charts').html('<div style="text-align:center;"><img src="<?php echo URL::base()."themes/default/media/img/loading_g2.gif"; ?>"></div>');
		$.ajax({
			async: false,
			url: url,
			dataType:"json",
			success: function(data) {
				//console.debug(data);
				ret = data;
			}
		});
		$('#charts').html('');
		return ret;
	};

	var jsonurl = "<?php echo URL::base().'charts/index/'.$project->id;?>";


	var plot = $.jqplot('charts', jsonurl,{
		dataRenderer: ajaxDataRenderer,
		dataRendererOptions: {
			unusedOptionalUrl: jsonurl
		},
		axes:{
			xaxis:{
				tickOptions:{formatString:'%b %#d, %y'},
				min:'<?php echo $first_date; ?>',
				tickInterval:'1 week',
				renderer:$.jqplot.DateAxisRenderer
			}
		},
		highlighter: {
			show: true,
			sizeAdjust: 7.5
		},
		seriesDefaults: {
			showMarker:true,
			pointLabels:{ show:true, location:'s', ypadding:4 }
		},			
	});	
}