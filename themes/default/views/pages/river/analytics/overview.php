<div id="content" class="center">
	<div class="col_3 analytics-summary">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo number_format($total_drop_count); ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters">
				<div class="parameter">
					<?php echo __("Total Drops"); ?>
				</div>
		    </section>
		</article>
	</div>
	<div class="col_3 analytics-summary">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo $days_active ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters">
				<div class="parameter">
					<?php echo __("Days Active"); ?>
				</div>
		    </section>
		</article>
	</div>
	<div class="col_3 analytics-summary">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo $drops_per_day ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters">
				<div class="parameter">
					<?php echo __("Drops per Day"); ?>
				</div>
		    </section>
		</article>
	</div>

	<div class="col_3 analytics-summary">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo $used_quota; ?>%</h1>
		        </div>
		    </header>
		    <section class="property-parameters">
				<div class="parameter">
					<?php echo __("Filled Up") ?>
				</div>
		    </section>
		</article>
	</div>
	<div style="clear:both;"></div>
	<div class="col_12 analytics">
		<article class="container base">
		    <header class="cf">
		        <div class="property-title">
		            <h1><?php echo __("Growth Trend"); ?></h1>
		        </div>
		    </header>
		    <section class="property-parameters analytics" id="growth-trend"></section>
		</article>
	</div>
</div>

<script type="text/javascript">
var riverGrowthTrend = <?php echo $river_growth_trend; ?>;
var parseDate = d3.time.format("%Y-%m-%d").parse;

riverGrowthTrend.forEach(function(d){
	d.drop_count = +d.drop_count;
	d.activity_date = parseDate(d.activity_date);
});

// Rendering options
var renderOptions = {
	xAxis: {column: "activity_date", type: "date"}, 
	yAxis: {column: "drop_count", type: "number", gridLines: true,  title: "No . of drops"}
};

// Generate the chart
var chart = new Chart("area", "#growth-trend");
chart.height(350)
	.width(960)
	.data(riverGrowthTrend)
	.render(renderOptions);

</script>