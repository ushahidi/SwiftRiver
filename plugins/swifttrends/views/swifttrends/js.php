$(document).ready(function () {

	var palette = new Rickshaw.Color.Palette( { scheme: 'classic9' } );

	// instantiate our graph!

	var graph = new Rickshaw.Graph( {
		element: document.getElementById("chart"),
		width: 800,
		height: 500,
		renderer: 'area',
		stroke: true,
		series: <?php echo $data; ?>
	} );

	graph.render();

	var slider = new Rickshaw.Graph.RangeSlider( {
		graph: graph,
		element: $('#slider')
	} );

	var hoverDetail = new Rickshaw.Graph.HoverDetail( {
		graph: graph
	} );

	var annotator = new Rickshaw.Graph.Annotate( {
		graph: graph,
		element: document.getElementById('timeline')
	} );

	var legend = new Rickshaw.Graph.Legend( {
		graph: graph,
		element: document.getElementById('legend')

	} );

	var shelving = new Rickshaw.Graph.Behavior.Series.Toggle( {
		graph: graph,
		legend: legend
	} );

	var order = new Rickshaw.Graph.Behavior.Series.Order( {
		graph: graph,
		legend: legend
	} );

	var highlighter = new Rickshaw.Graph.Behavior.Series.Highlight( {
		graph: graph,
		legend: legend
	} );

	var smoother = new Rickshaw.Graph.Smoother( {
		graph: graph,
		element: $('#smoother')
	} );

	var ticksTreatment = 'glow';

	var xAxis = new Rickshaw.Graph.Axis.Time( {
		graph: graph,
		ticksTreatment: ticksTreatment
	} );

	xAxis.render();

	var yAxis = new Rickshaw.Graph.Axis.Y( {
		graph: graph,
		tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
		ticksTreatment: ticksTreatment
	} );

	yAxis.render();


	var controls = new RenderControls( {
		element: document.querySelector('form'),
		graph: graph
	} );

	

});	