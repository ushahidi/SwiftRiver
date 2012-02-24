<div id="chartsfx"></div>
<script type="text/javascript">

var dimensions = ($('#chartsfx').width() - 50);
$('#chartsfx').height(dimensions*0.8);
var w = dimensions,
	h = (dimensions*0.8),
	r = Math.min(w, h) / 2,
	x = d3.scale.linear().range([0, 2 * Math.PI]),
	y = d3.scale.sqrt().range([0, r]),
	color = d3.scale.category20c();

var vis = d3.select("#chartsfx").append("svg")
		.attr("width", w)
		.attr("height", h)
	.append("g")
		.attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");

var partition = d3.layout.partition()
		.value(function(d) { return d.size; });

var arc = d3.svg.arc()
	.startAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x))); })
	.endAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x + d.dx))); })
	.innerRadius(function(d) { return Math.max(0, y(d.y)); })
	.outerRadius(function(d) { return Math.max(0, y(d.y + d.dy)); });

$('#chartsfx').addClass('chart_loading').removeClass('chart_loaded');
d3.json("<?php echo $flare_url; ?>", function(json) {
	var path = vis.data([json]).selectAll("path")
			.data(partition.nodes)
		.enter().append("path")
			.attr("d", arc)
			.style("fill", function(d) { return color((d.children ? d : d.parent).name); })
			.on("click", click);

	path.append("text")
		.attr("transform", function(d) { return "rotate(" + (d.x + d.dx / 2 - Math.PI / 2) / Math.PI * 180 + ")"; })
		.attr("x", function(d) { return Math.sqrt(d.y); })
		//.attr("dx", "100") // margin
		//.attr("dx", function(d) { return d.depth == 0 ? "-20" : "6";}) // margin
		.attr("dy", ".35em") // vertical-align
		.text(function(d) { return d.name; });

	function click(d) {
		path.transition()
			.duration(750)
			.attrTween("d", arcTween(d));
	}

	$('#chartsfx').addClass('chart_loaded').removeClass('chart_loading');
});

// Interpolate the scales!
function arcTween(d) {
	var xd = d3.interpolate(x.domain(), [d.x, d.x + d.dx]),
			yd = d3.interpolate(y.domain(), [d.y, 1]),
			yr = d3.interpolate(y.range(), [d.y ? 20 : 0, r]);
	return function(d, i) {
		return i
				? function(t) { return arc(d); }
				: function(t) { x.domain(xd(t)); y.domain(yd(t)).range(yr(t)); return arc(d); };
	};
}

</script>