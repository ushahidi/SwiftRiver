<div id="content" class="center">
	<div class="col_4 analytics">
		<article class="container base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo __("Channel Volume"); ?></h1>
				</div>
			</header>
			<section class="property-parameters analytics" id="channels-breakdown"></section>
		</article>
	</div>
	<div class="col_4 analytics">
		<article class="container base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo __("Breakdown of Total Tag Volume"); ?></h1>
				</div>
			</header>
			<section class="property-parameters analytics" id="tags-breakdown"></section>
		</article>
	</div>
	<div class="col_4 analytics">
		<article class="container base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo __("Breakdown of Extracted Media"); ?></h1>
				</div>
			</header>
			<section class="property-parameters analytics" id="media-breakdown"></section>
		</article>
	</div>
	<div style="clear:both;"></div>

	<div class="col_6">
		<article class="container base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo __("Analysis of Total River Volume"); ?></h1>
				</div>
			</header>
			<section class="property-parameters data-table">
				<?php if (array_key_exists('links_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with Links"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['links_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>
				
				<?php if (array_key_exists('image_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with Images"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['image_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>
				
				<?php if (array_key_exists('video_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with Videos"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['video_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>
				
				<?php if (array_key_exists('place_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with Place Tags"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['place_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>

				<?php if (array_key_exists('person_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with People Tags"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['person_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>

				<?php if (array_key_exists('organization_count', $content_analysis)): ?>
				<div class="parameter">
					<span class="breakdown-item"><?php echo __("% with Organization Tags"); ?></span>
					<span class="breakdown-val"><?php echo $content_analysis['organization_count']; ?></span>
				</div>
				<div style="clear:both;"></div>
				<?php endif; ?>

			</section>
		</article>
	</div>
</div>

<script type="text/javascript">
var channelsBreakdown = <?php echo $channels_breakdown; ?>;
var totalDropCount = <?php echo $total_drop_count; ?>;

channelsBreakdown.forEach(function(d) {
	d.drop_count = +d.drop_count;
	d.channel = d.channel + " " + (Math.round((d.drop_count/totalDropCount) * 100)) + "%";
});


var channelRenderOptions = {xAxis: {column: "channel"}, yAxis: {column: "drop_count"}};
var channelsPieChart = new Chart("pie", "#channels-breakdown")
	.height(180)
	.width(300)
	.radius(10)
	.legends(true)
	.data(channelsBreakdown)
	.render(channelRenderOptions);


// Tags breakdown

var totalTagCount = 0;
var tagsBreakdown = <?php echo $tags_breakdown; ?>;
tagsBreakdown.forEach(function(d) {
	d.tag_count = +d.tag_count;
	totalTagCount += d.tag_count;
});

tagsBreakdown.forEach(function(d) {	
	d.tag_type = d.tag_type + " " + (Math.round((d.tag_count/totalTagCount) * 100)) + "%";
});

var tagsRenderOptions = {xAxis: {column: "tag_type"}, yAxis: {column: "tag_count"}};
var tagsPieChart = new Chart("pie", "#tags-breakdown");
tagsPieChart.height(180)
	.width(300)
	.radius(10)
	.legends(true)
	.data(tagsBreakdown)
	.render(tagsRenderOptions);


// Media type breakdown
var totalMediaTypeCount = 0;
var mediaTypesBreakdown = <?php echo $media_types_breakdown; ?>;

mediaTypesBreakdown.forEach(function(d) {
	d.media_count = +d.media_count;
	totalMediaTypeCount += d.media_count;
});

mediaTypesBreakdown.forEach(function(d) {
	d.media_type = d.media_type + " " + (Math.round(d.media_count/totalMediaTypeCount * 100)) + "%";
});


var mediaTypesRenderOptions = {xAxis: {column: "media_type"}, yAxis: {column: "media_count"}};
var mediaTypesPieChart = new Chart("pie", "#media-breakdown");
mediaTypesPieChart.height(180)
	.width(300)
	.radius(10)
	.legends(true)
	.data(mediaTypesBreakdown)
	.render(mediaTypesRenderOptions);

</script>