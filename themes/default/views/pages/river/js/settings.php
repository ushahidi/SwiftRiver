<script type="text/javascript">
/**
 * River dashboard javascript
 * Copyright 2012, Ushahidi Inc
 */
(function() {
	
	window.Channel = Backbone.Model.extend();
	window.ChannelsCollection = Backbone.Collection.extend({
		model: Channel,
		url: "<?php echo $channels_url; ?>"
	})
	
	// Channel listing view
	window.ChannelListView = Backbone.View.extend({
		// Parent container for the channel listing
		el: $("#channels ul.tabs"),
		
		// Template for the panel for displaying the channel options
		panelTemplate: _.template($("#channel-panel-view").html()),
		
		initialize: function() {
			this.model.bind("reset", this.render, this);
		},
		
		render: function(eventName) {
			_.each(this.model.models, function(channel) {
				$(this.el).append(new ChannelItemView({model: channel}).render().el);
				
				$("#channels .tab-container").append(this.panelTemplate(channel.toJSON()));
			}, this);
			
			// TODO: Select the first channel in the river and display its 
			// config options
			return this;
		}
	});
	
	// View for a single channel item
	window.ChannelItemView = Backbone.View.extend({
		tagName: "li",
		
		template: _.template($("#channel-list-item").html()),
		
		// When a channel item is selected
		events: {
			// Show the options for the selected channel
			"click": "showChannelOptions",
			
			// Toggle the on/off status of the channel
			"click a span.switch": "toggleChannelStatus"
		},
		
		showChannelOptions: function(event) {
			// Remove any "active class"
			$("ul.tabs li.button-view").removeClass("active");
			
			// Add "active" class to the selected channel
			$(event.currentTarget).addClass("active");
			
			// JSON representation of the model
			var _model  = this.model.toJSON();
			
			// Hide all tab container items
			$(".tab-container article.tab-content").each(function(){
				var _article = this;
				$(_article).css("display", "none");
			});
			
			// Get the DOM  reference for the selected channel
			var panel = $(".tab-container #" + _model.channel);
			panel.css("display", "block");
			
			// Check if the panel is empty
			if (panel.children().length == 1) {
				// Show the tab+content for the selected channel
				_.each(_model.channel_data, function(item) {
					panel.append(new ChannelOptionItemView({model: item.data}).render().el);
				});
			}
			
			// Rendering for the channel config options - used to add new items
			// to the UI
			var configPanel = $(".channel-options", panel);
			if (configPanel.children().length == 0) {
				
				// Iterate through each of the config options and render
				$.each(_model.config_options, function(opt) {
					
					// Model reference for the current config option
					var configModel = _model.config_options[opt];
					
					// Add the config item item to the display
					configPanel.append(new ChannelOptionConfigItem({
						configItem: opt,
						parentEl: panel,
						model: configModel
					}).render().el);
				});
			}
			
			return false;
		},
		
		toggleChannelStatus: function(event) {
			$(event.currentTarget).toggleClass("switch-on").toggleClass("switch-off");
			
			// TODO: Update the status of the channel in the DB via HTTP POST
			return false;
		},
		
		render: function(eventName) {
			// Set the className of the parent DOM object
			$(this.el).addClass("button-view " + this.model.toJSON().channel);
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
	// View for the channel option listing
	window.ChannelOptionConfigItem = Backbone.View.extend({
		tagName: "li",
		
		template: _.template($("#channel-option-listing").html()),
		
		// Events
		events: {
			// Add a config option form field to the UI
			// TODO: Implement adding of group options
			"click a": "addOptionItem"
		},
		
		addOptionItem: function(e) {
			// Check if the value property exists in the model
			if (typeof(this.model.value) == "undefined") {
				this.model.value = "";
			}
			
			this.options.parentEl.append(new ChannelOptionItemView({model: this.model}).render().el);
			return false;
		},
		
		render: function(eventName) {
			$(this.el).html(this.template(this.model));
			return this;
		}
	});
	
	// View for an individual channel option
	window.ChannelOptionItemView = Backbone.View.extend({
		tagName: "div",
		
		className: "input",
		
		template: _.template($("#channel-option-item").html()),
		
		// Events
		events: {
			// Removes a channel config value from the UI
			"click span > a": "removeOption"
		},
		
		// Deletes the option item
		removeOption: function() {
			var _object = this;
			$(_object.el).remove();
			return false;
		},
		
		render: function(eventName) {
			$(this.el).html(this.template(this.model));
			return this;
		}
	});
	
	
	// Fetch the channels and display them
	bootstrap = function() {
		this.channels = new ChannelsCollection();
		this.channels.fetch();
		this.channelListView = new ChannelListView({model: this.channels});
		this.channelListView.render();
	}
	
	bootstrap();
	
})();
</script>