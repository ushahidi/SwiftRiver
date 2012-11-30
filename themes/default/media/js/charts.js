/**
 * d3 wrapper for generating 2D charts
 * Contains the boilerplate code for creating a basic chart
 */
function Chart(type, parent) {
	// Chart type specified?
	if (type === undefined || type === null)
		throw "Error initializing chart. No chart type specified";

	// Supported chart types
	var _supported = ["line", "area", "bar", "hierarchical", "pie"];
	
	// Chart type supported?
	if (_supported.indexOf(type) === -1)
		throw "Invalid chart type (" + type + ") specified";

	// Height and width of the chart
	var _width = 960, _height = 500;
	
	var _margins = {top: 50, right: 40, bottom: 50, left: 60};
	
	// Bar height and (minimum) width
	var MINIMUM_BAR_HEIGHT = 20;
	var MINIMUM_BAR_WIDTH = 15;

	var _parent = 'body';
	
	// Data to be plotted
	var _data = [];
	
	var _x;
	var _xAxis = d3.svg.axis();
	var _xAxisPadding = 0.1;
	var _xAxisTitle;
	
	var _y;
	var _yAxis = d3.svg.axis();
	var _yAxisPadding = 0.2;
	var _yAxisTitle;
	
	var _multiSeries = false, _stacked = false, _legends = false;
	
	var svg;
	
	// Default color range
	var _colors = d3.scale.category20();
	var _radius = 150;
	
	if (parent !== undefined) {
		_parent = parent;
	}
	

	// Gets or sets the height of the chart
	this.height = function(h) {
		if (!arguments.length) return _height;
		_height = h;
		return this;
	};
	
	// Gets or sets the width of the chart
	this.width = function(w) {
		if (!arguments.length) return _width;
		_width  = w;
		return this;
	};
	
	this.margins = function(m) {
		if (!arguments.length) return _margins;
		// TODO: Check if the margins has all the properties
		_margins = m;
		return this;
	};
	
	this.leftOffset = function(o) {
		if (!arguments.length) return _marigns.left;
		_margins.left = o;
		return this;
	};
	
	/**
	 * Gets the length of the x Axis
	 */
	this.xAxisLength = function(length) {
		return this.width() - this.margins().left - this.margins().right;
	};

	// Gets or sets the x scale
	this.x = function(_) {
		if (!arguments.length) return _x;
		_x = _;
		return this;
	};

	this.xAxisPadding = function(p) {
		if (!arguments.length) return _xAxisPadding;
		_xAxisPadding = p;
		return this;
	};
	
	this.xAxisTitle = function(t) {
		if (!arguments.length) return _xAxisTitle;
		_xAxisTitle = t;
		return this;
	}
	
	/**
	 * Gets or sets the length of the y axis
	 * If the specified length is greater than the height of the chart
	 * the default axis length is computed and returned
	 */
	this.yAxisHeight = function(length) {
		return this.height() - this.margins().top - this.margins().bottom;
	};
	
	// Gets or sets the y scale
	this.y = function(_) {
		if(!arguments.length) return _y;
		_y = _;
		return this;
	};

	this.yAxisPadding = function(p) {
		if (!arguments.length) return _yAxisPadding;
		_yAxisPadding = p;
		return this;
	};
	
	this.yAxisTitle = function(t) {
		if (!arguments.length) return _yAxisTitle;
		_yAxisTitle = t;
		return this;
	}
	
	this.multiSeries = function(multi) {
		if (!arguments.length) return _multiSeries;
		_multiSeries = multi;
		return this;
	};

	this.stacked = function(stacked) {
		if (!arguments.length) return _stacked;
		_stacked = stacked;
		return this;
	};
	
	this.legends = function(_l) {
		if (!arguments.length) return _legends;
		_legends = _l;
		return this;
	}

	this.data = function(d) {
		if (!arguments.length) return _data;
		_data = d;
		return this;
	};	
	
	this.colors = function(c) {
		if (!arguments.length) return _colors;
		_colors = d3.scale.ordinal().range(c);
		return this;
	};

	// Gets the radius of the pie chart
	this.radius = function(r) {
		if (!arguments.length) return _radius;
		_radius = r;
		return this;
	}


	/**
	 * @param config - {Object} options for the chart
	 *
	 * Valid config parameters are:
	 * yAxis - {Object} A hash with the following properties
	 * 		title - {String} - Title of the axis
	 *  	type - {String} Type of data to be plotted
	 * 		column - {String} Name of the column with the y axis data
	 *      gridLines - {Boolean} Whether or not to show gridlines
	 *
	 * xAxis - {Object} A hash with the folllowing properties (same as descriptions as yAxis)
	 *		title - {String} 
	 *		type - {String}
	 * 		column- {String}
	 *      gridLines - {Boolean}
	 */
	this.render = function(config) {
		// Sanity checks
		if (!this.data.length)
			throw "FATAL ERROR: The chart data has not been specified";
		
		if (config === undefined) {
			throw "The chart configuration has not been specified";
		}
		
		if (config.yAxis === undefined) {
			throw "The configuration for the y axis has not been specified";
		}
		
		if (config.xAxis === undefined) {
			throw "The configuration for the x axis has not been specified";
		}
		
		// Replace the column/property names for the plot
		// data with x and y
		var modifiedData = [];
		if (this.multiSeries()) {
			this.data().forEach(function(d){
				v = {name: d.name, values: []};
				
				d.values.forEach(function(e){
					v.values.push({
						x: e[config.xAxis.column],
						y: e[config.yAxis.column]
					});
				});

				modifiedData.push(v);
			});

		} else {
			this.data().forEach(function(d) {
				modifiedData.push({x: d[config.xAxis.column], y: d[config.yAxis.column]});
			})
		}
		
		// Reset the chart data
		this.data(modifiedData);
		
		// Offsets for the root chart
		var xOffset = (type == "pie") ? (this.width()/2) * 0.75: this.margins().left,
			yOffset = (type == "pie") ? this.height()/2 : this.margins().top;

		// Root container for the chart
		svg = d3.select(_parent)
			.append('svg')
			.attr('width', this.width())
			.attr('height', this.height())
			.append('g')
			.attr('transform', 'translate(' + xOffset + ', ' + yOffset + ')');
				
		// Prepare the axes
		if (type !== "pie") {
			prepareXAxis(config.xAxis);
			prepareYAxis(config.yAxis);
		}

		var __function = type;
		if (type === "line") {
			if (this.multiSeries()) {
				__function = "multiSeriesLine";
			} 
		}
		
		// Call the charting function
		eval((__function + "Chart()"));

		// Set the color of the grid lines
		svg.selectAll("g.y.axis line")
			.style("stroke", "#E0E0E0");

		return this;
	}
	
	var _chart = this;

	// Helper function to set the scale, domain, range 
	// and orientation of the x axis (_xAxis)
	function prepareXAxis(config) {
		if (config.title) {
			_chart.xAxisTitle(config.title);
		}
		
		if (config.type === undefined || config.type === "general") {
			_x = d3.scale.ordinal();
			_x.domain(_chart.data().map(function(d) { return d.x; } ));

		} else if (config.type == "number" || config.type == "percent") {
			_x = d3.scale.linear().range([0, _chart.xAxisLength()]);
			_x.domain(d3.extent(_chart.data(), function(d) { return d.x; } )).nice();
			
		} else if (config.type == "date") {
			// Create the scale
			_x = d3.time.scale().range([0, _chart.xAxisLength()]);

			// Set the domain
			if (_chart.multiSeries()) {
				var dates = [];

				_chart.data().forEach(function(k) {
					k.values.forEach(function(v) {
						dates.push(v.x);
					});
				});

				_x.domain(d3.extent(dates, function(d) { return d; } ));
				
			} else {
				_x.domain(d3.extent(_chart.data(), function(d) { return d.x; } ));
			}

		}

		// Set the scale and orientation of the axis
		_xAxis.scale(_x).orient("bottom");
	}
	
	// Helper function to set the scale, domain, range 
	// and orientation of the y axis (_yAxis)
	function prepareYAxis(config) {
		if (config.title) {
			_chart.yAxisTitle(config.title);
		}
		
		if (config.type === undefined || config.type == "general") {
			_y = d3.scale.ordinal();
		} else if (config.type == "number" || config.type == "percent") {
			_y = d3.scale.linear().range([_chart.yAxisHeight(), 0]);
				
			// Set the domain
			if (_chart.multiSeries()) {
				_y.domain([
					d3.min(_chart.data(), function(k) { return d3.min(k.values, function(v) { return v.y; }); }),
					d3.max(_chart.data(), function(k) { return d3.max(k.values, function(v) { return v.y; }); })
				]).nice();
			} else {
				_y.domain(d3.extent(_chart.data(), function(d) { return d.y; } )).nice();
			}
		}

		// Se the axis scale and orientation
		_yAxis.scale(_y).orient("left");
		
		if (config.gridLines) {
			_yAxis.tickSize(-_chart.xAxisLength());
		}
			
		// NOTE: The data has to be in % format
		if (config.type === "percent") {
			_yAxis.tickFormat(d3.format(".2s"));
		}
	}
	
	// Plots a line chart
	function lineChart() {
		// Line generator
		var line = d3.svg.line()
			.interpolate("basis")
			.x(function(d) { return _chart.x()(d.x); })
			.y(function(d) { return _chart.y()(d.y); });
		
		svg.datum(_chart.data());

		preparePlottingArea();

		svg.append("path")
			.style("fill-opacity", "0.8")
			.attr("class", "line")
			.attr("d", line);
	}
	
	// Area chart - singe series
	function areaChart() {
		// Line generator
		var line = d3.svg.line()
			.x(function(d) { return _chart.x()(d.x); })
			.y(function(d) { return _chart.y()(d.y); });

		// Area generator
		var area = d3.svg.area()
			.x(line.x())
			.y0(_chart.yAxisHeight())
			.y1(line.y());

		svg.datum(_chart.data());

		// Generate the area path
		svg.append("path")
			.attr("class", "area")
			.attr("d", area)
			.style("fill-opacity", ".2");

		preparePlottingArea();

		// Generate the line path
		svg.append("path")
			.attr("class", "line")
			.attr("d", line)
			.style("stroke-width", 3);
		

		// TODO: Implement tooltips when a user hovers on the tick marls
		
		// Show tick marks
		// svg.selectAll(".dot")
		// 	.data(_chart.data().filter(function(d) { return d.y}))
		// 	.enter().append("circle")
		// 	.attr("class", "dot")
		// 	.attr("cx", line.x())
		// 	.attr("cy", line.y())
		// 	.attr("r", 3.5)
		// 	.style("fill", "steelBlue")
	}
	
	function preparePlottingArea() {
		svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0, " + _chart.yAxisHeight() + ")")
			.call(_xAxis);

		svg.append("g")
			.attr("class", "y axis")
			.call(_yAxis);
		
		// Set the title for the y axis
		if (_chart.yAxisTitle() !== null) {
			svg.append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", -45)
				.attr("dy", ".71em")
				.style("text-anchor", "end")
				.text(_chart.yAxisTitle());
		}
	}

	function multiSeriesLineChart() {
		var line = d3.svg.line()
			.interpolate("basis")
			.x(function(d) { return _chart.x()(d.x); })
			.y(function(d) { return _chart.y()(d.y); });

		// Prepare the axes
		preparePlottingArea();
				
		// Map each series to a color
		var color = _chart.colors();
		color.domain(d3.extent(_chart.data(), function(d) { return d.name; } ));

		var series = svg.selectAll(".series")
			.data(_chart.data())
		  .enter().append("g")
			.attr("class", "series");
		
		series.append("path")
			.attr("class", "line")
			.attr("d", function(d) { return line(d.values); })
			.style("stroke", function(d) { return color(d.name); });
		
		if (_chart.legends()) {
			showLegend(color, 0, 0);
		}
	}
	
	// Plots a bar chart
	function barChart() {
		var dateRange = _chart.x().domain(),
			numberOfDays = d3.time.days(dateRange[0], dateRange[1]).length,
			numberOfBars = _chart.data().length,
			numberOfTicks = numberOfBars,
			barWidth = Math.floor(_chart.xAxisLength()/numberOfBars);

		// Given the index i, calculates the position of the
		// bar on the x axis
		function getBarPosition(i) {
			return 	((i*barWidth) + barWidth + i) - barWidth*.9;
		};
		
		// Assumption: Data on the xAxis represents time
		if (numberOfDays <= numberOfBars) {
			// Daily
			_xAxis.ticks(d3.time.days, 1).tickFormat(d3.time.format("%d/%m '%y"));

		} else if (Math.floor(numberOfDays/numberOfBars) <= 2) {
			// Weekly
			_xAxis.ticks(d3.time.weeks, 2).tickFormat(d3.time.format("%b-%d"));
			numberOfTicks = Math.floor(d3.time.weeks(dateRange[0], dateRange[1]).length/2);

		} else if (Math.floor(numberOfDays/numberOfBars) <= 4) {
			// Fortnight
			_xAxis.ticks(d3.time.weeks, 3).tickFormat(d3.time.format("%b-%d"));
			numberOfTicks = Math.floor(d3.time.weeks(dateRange[0], dateRange[1]).length/3);

		} else {
			// Monthly
			_xAxis.ticks(d3.time.months, 1).tickFormat(d3.time.format("%B"));
			numberOfTicks = d3.time.months(dateRange[0], dateRange[1]).length;
		}

		// No. of ticks per bar
		var ticksPerBar = Math.round(numberOfBars/numberOfTicks);

		// Aligns the ticks with the bar
		var tickAdjustment = barWidth/ticksPerBar;

		svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(" + barWidth + ", " + _chart.yAxisHeight() + ")")
			.call(_xAxis);

		// Fix the tick position
		svg.selectAll(".x.axis g")
			.attr("transform", function(d, i) {
				var barPosition = getBarPosition(i);
				var xPos = barPosition * ticksPerBar + tickAdjustment;
				return "translate("+ xPos +", 0)"; 
			});

		svg.append("g")
			.attr("class", "y axis")
			.call(_yAxis);
		
		// Set the title for the y axis
		if (_chart.yAxisTitle !== null) {
			svg.append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", -40)
				.attr("x", -(_chart.height() - _chart.yAxisHeight()))
				.style("text-anchor", "end")
				.text(_chart.yAxisTitle);
		}

		// Draw the bars
		svg.selectAll(".bar")
			.data(_chart.data())
		  .enter().append("rect")
			.attr("class", "bar")
			.attr("x", function(d, i) { return getBarPosition(i); })
			.attr("width", barWidth)
			.attr("y", function(d) { return _chart.y()(d.y)})
			.attr("height", function(d) { return _chart.yAxisHeight() - _chart.y()(d.y); });
		
	};

	function hierarchicalChart() {
		// Change the orientation of the x axis
		_xAxis.orient("top");
		_y.range(["steelblue"]);

		svg.append("rect")
			.attr("class", "background")
			.attr("width", this.width)
			.attr("height", "100%");

		svg.append("g")
			.attr("class", "x axis")
			.call(_xAxis)
			.append("text")
			.attr("x", _chart.xAxisLength()/2)
			.attr("y", "-35")
			.attr("dy", ".85em")
			.attr("text-anchor", "end")
			.text(_chart.xAxisTitle())
			.style("font-weight", "bold");

		svg.append("g")
			.attr("class", "y axis")
			.append("line").attr("y1", "100%");

		// Draw the bars
		var bar = svg.insert("g", ".y.axis")
			.attr("class", "enter")
			.attr("transform", "translate(0,5)")
			.selectAll("g")
			.data(_chart.data())
			.enter().append("g")
			.attr("transform", function(d, i) {
				return "translate(0, " + MINIMUM_BAR_HEIGHT * i * 1.2 + ")";
			})
			.style("opacity", "1");

		bar.append("text")
			.attr("x", -6)
			.attr("y", MINIMUM_BAR_HEIGHT/2)
			.attr("dy", ".35em")
			.attr("text-anchor", "end")
			.text(function(d) { return d.y; });

		bar.append("rect")
			.attr("width", function(d) { return _chart.x()(d.x); })
			.attr("height", MINIMUM_BAR_HEIGHT)
			.style("fill", function(d) { return _chart.y()(d); });		
	}
	
	function pieChart() {
		var arc = d3.svg.arc()
			.outerRadius(_chart.radius() - 10)
			.innerRadius(_chart.radius() - 70);

		var pie = d3.layout.pie()
			.sort(null)
			.value(function(d) { return d.y; });
			
		// Map the color to the x values
		var pieColors = _chart.colors();
		pieColors.domain(_chart.data().map(function(d) { return d.x; } ));

		var g = svg.selectAll(".arc")
			.data(pie(_chart.data()))
		  .enter().append("g")
			.attr("class", "arc");

		g.append("path")
			.attr("d", arc)
			.style("fill", function(d) { return pieColors(d.data.x); });
			
		if (_chart.legends()) {
			showLegend(pieColors, -_chart.radius()*.75, -_chart.radius()*.75);
		}
	}

	// Displays the legend
	function showLegend(colorMap, xOffset, yOffset) {
		var legend = svg.selectAll(".legend")
			.data(colorMap.domain().slice().reverse())
			.enter().append("g")
			.attr("class", "legend")
			.attr("transform", function(d, i) {
				return "translate(" + xOffset + "," + (yOffset + (i*25)) + ")";
			});
		
		legend.append("rect")
			.attr("x", _chart.xAxisLength()-24)
			.attr("width", 18)
			.attr("height", 18)
			.style("fill", colorMap);
				
		legend.append("text")
			.attr("x", _chart.xAxisLength()-32)
			.attr("y", 9)
			.attr("dy", ".35em")
			.style("text-anchor", "end")
			.text(function(d) { return d; })
			.style("font-size", "11");
	}

	return this;
}