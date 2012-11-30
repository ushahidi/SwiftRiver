<div id="content" class="center">
	<div class="col_6 analytics">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo __("Volume Breakdown"); ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters analytics" id="summary-stats"></section>
		</article>
	</div>
	<div class="col_6">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo __("Fact Sheet"); ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters analytics">
				<div class="parameter">
					<h1><?php echo $days_active; ?> <span><?php echo __("days active"); ?></span></h1>
				</div>
		    	<div class="parameter">
					<h1><?php echo $total_drop_count; ?> <span><?php echo __("drops"); ?></span></h1>
				</div>
				<div class="parameter">
					<h1><?php echo $used_quota; ?><span><?php echo __("% full"); ?></span></h1>
				</div>
				<div class="parameter">
					<h1><?php echo $river_velocity ?> <span><?php echo __("drops/day"); ?></span><h1>
				</div>
		    </section>
		</article>
	</div>
</div>

<script type="text/javascript">
	var breakDown = <?php echo $breakdown; ?>;
	var totalDropCount = <?php echo $total_drop_count; ?>;

	// Sanitize the data
	breakDown.forEach(function(b){
		b.drop_count = +b.drop_count;
		b.channel = b.channel + " " + (Math.round((b.drop_count/totalDropCount) * 100)) + "%";
	});
	
	// Generate the pie chart
	var renderOptions = {xAxis: {column: "channel"},  yAxis: {column: "drop_count"}};
	var pieChart = new Chart("pie", "#summary-stats");
	pieChart.height(400)
		.width(500)
		.radius(150)
		.legends(true)
		.data(breakDown)
		.render(renderOptions);

</script>