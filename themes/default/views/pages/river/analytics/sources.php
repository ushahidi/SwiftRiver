<div id="content" class="center">
	<div class="col_12 analytics">
	</div>
</div>

<script type="text/template" id="source-activity-template">
    <header class="cf">
        <div class="property-title">
            <h1><?php echo __("Top 10 sources from ") ?><%= channel %></h1>
        </div>
    </header>
    <section class="property-parameters analytics"></section>
</script>

<script type="text/javascript">
$(function(){
	var sourcesTrend = <?php echo $sources_trend; ?>;
	var SourceActivityChart = Backbone.View.extend({
		tagName: "article",
        
		className: "container base",
        
		template: _.template($("#source-activity-template").html()),
        
		render: function() {
			this.$el.html(this.template(this.options));
			return this;
		}
	});
    
	var ActivityChartsControl = Backbone.View.extend({
		el: "div.analytics",
        
		initialize: function() {
			var context = this;

			// Plot hierarchical bar chart for each channel
			d3.keys(sourcesTrend).forEach(function(k){
				sourcesTrend[k].forEach(function(s){
					s.drop_count = +s.drop_count;
					// Limit the identity names to 25 characters
					// This is because SVG does not support text-wrapping
					if (s.identity_name.length > 25) {
						s.identity_name = s.identity_name.substr(0, 24);
					}
				});
				context.plotSourceData({channel: k, data: sourcesTrend[k]});
			});
		},
        
		plotSourceData: function(source) {
			var view = new SourceActivityChart({channel: source.channel});
			var sectionName = "section-" + view.cid;

			this.$el.append(view.render().el);
			view.$("section").attr({id: sectionName});

			var renderOptions = {
				xAxis: {title: "No. of Drops", type: "number", column: "drop_count"},
				yAxis: {title: "Sources", type: "general", column: "identity_name"}
			};

			var graph = new Chart("hierarchical", "#"+sectionName);
			graph.width(840)
				.height(300)
				.leftOffset(140)
				.data(source.data)
				.render(renderOptions);
		},

    });

	var chartsControl = new ActivityChartsControl();
});
</script>