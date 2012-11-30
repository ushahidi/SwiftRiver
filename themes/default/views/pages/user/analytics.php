<div class="col_12 analytics">
	<article class="container base">
		<header class="cf">
			<div class="property-title"><h1><?php echo __("River Growth Rate"); ?></h1></div>
		</header>
		<section class="property-parameters analytics" id="river-growth-trend"></section>
	</article>
</div>

<script type="text/javascript">
	var riverGrowthTrend = <?php echo $river_growth_trend; ?>;
	
	var parseDate = d3.time.format("%Y-%m-%d").parse;

	// Format the data
	var rivers = [];
	d3.keys(riverGrowthTrend).forEach(function(name){
		riverGrowthTrend[name].forEach(function(d){
			d.activity_date = parseDate(d.activity_date);
			d.drop_count = +d.drop_count;
		});
		rivers.push({name: name, values: riverGrowthTrend[name]});
	});

	// Chart rendering options
	var renderOptions = {
		xAxis: {type: "date", column: "activity_date"},
		yAxis: {type: "number", column: "drop_count", title: "No. of drops", gridLines: true}
	};
	
	// Initialize and render the chart
	var chart = new Chart('line', "#river-growth-trend");
	chart.height(380)
		.multiSeries(true)
		.legends(true)
		.data(rivers)
		.render(renderOptions);

</script>