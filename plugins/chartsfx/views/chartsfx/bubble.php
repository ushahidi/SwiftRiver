<div id="chartsfx"></div>
<script type="text/javascript">

var dimensions = ($('#chartsfx').width() - 30);
$('#chartsfx').height(dimensions);
var r = dimensions,
		format = d3.format(",d"),
		fill = d3.scale.category20c();

var bubble = d3.layout.pack()
		.sort(null)
		.size([r, r]);

var vis = d3.select("#chartsfx").append("svg")
		.attr("x", 0)
		.attr("width", r)
		.attr("height", r)
		.attr("class", "bubble");

$('#chartsfx').addClass('chart_loading').removeClass('chart_loaded');
d3.json("<?php echo $flare_url; ?>", function(json) {

	var node = vis.selectAll("g.node")
			.data(bubble.nodes(classes(json))
			.filter(function(d) { return !d.children; }))
		.enter().append("g")
			.attr("class", "node")
			.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

	node.append("title")
			.text(function(d) { return d.className + ": " + format(d.value); });

	node.append("circle")
			.attr("r", function(d) { return d.r; })
			.style("fill", function(d) { return fill(d.packageName); });

	node.append("text")
			.attr("text-anchor", "middle")
			.attr("dy", ".3em")
			.text(function(d) { return d.className.substring(0, d.r / 3); });

	$('#chartsfx').addClass('chart_loaded').removeClass('chart_loading');
});

// Returns a flattened hierarchy containing all leaf nodes under the root.
function classes(root) {
	var classes = [];

	function recurse(name, node) {
		if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
		else classes.push({packageName: name, className: node.name, value: node.size});
	}

	recurse(null, root);
	return {children: classes};
}

</script>