<div id="content" class="center">
	<div class="col_12 analytics">
	</div>
</div>

<script type="text/template" id="channel-activity-template">
    <header class="cf">
        <div class="property-title">
            <h1><%= channel %> <?php echo __("activity over the :duration", array(":duration" => $duration)); ?></h1>
        </div>
    </header>
    <section class="property-parameters analytics"></section>
</script>

<script type="text/javascript">
$(function(){
	var channelsTrend = <?php echo $channels_trend; ?>;
	var ChannelActivityChart = Backbone.View.extend({
		tagName: "article",
        
		className: "container base",
        
		template: _.template($("#channel-activity-template").html()),
        
		render: function() {
			this.$el.html(this.template(this.options));
			return this;
		}
	});
    
	var ActivityChartsControl = Backbone.View.extend({
		el: "div.analytics",
        
		initialize: function() {
			var context = this;
			var parseDate = d3.time.format("%Y-%m-%d").parse;
			// Plot hierarchical bar chart for each channel
			d3.keys(channelsTrend).forEach(function(c){
				channelsTrend[c].forEach(function(e){
					e.drop_count = +e.drop_count; 
					e.activity_date = parseDate(e.activity_date); 
				});
				if (channelsTrend[c].length > 1) {
					context.plotSourceData({channel: c, data: channelsTrend[c]});
				}
			});
		},
        
		plotSourceData: function(source) {
			var view = new ChannelActivityChart({channel: source.channel});
			var sectionName = "section-" + view.cid;

			this.$el.append(view.render().el);
			view.$("section").attr({id: sectionName});
		   
			// Initialize the chart
			var renderOptions = {
				xAxis: {type: "date", column: "activity_date"},
				yAxis: {type: "number", column: "drop_count", title: "No. of drops", gridLines: true}
			};

			var graph = new Chart("area", "#"+sectionName);
			graph.height(380)
				.data(source.data)
				.render(renderOptions);
		},

	});

	var chartsControl = new ActivityChartsControl();
});
</script>