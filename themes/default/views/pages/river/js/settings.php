<script type = "text/javascript">
/**
 * River dashboard javascript
 * Copyright 2012, Ushahidi Inc
 */
 (function() {

	// Stores the views for the channel config parameters
	var channelSettings = {};

	// Keeps track of the enabled channels - when creating a new river
	var enabledChannels = {};
	
	// Channel (RSS, Twitter etc) model
	window.Channel = Backbone.Model.extend();

	// Collection for the channels
	window.ChannelsCollection = Backbone.Collection.extend({
		model: Channel,
		url: "<?php echo $channels_url; ?>"
	})

	// Channel listing view
	window.ChannelListView = Backbone.View.extend({

		// Parent container for the channel listing
		el: $("#settings ul.tabs"),

		// Template for the panel for displaying the channel options
		panelTemplate: _.template($("#channel-panel-view").html()),

		initialize: function() {
			this.model.bind("reset", this.render, this);
		},

		render: function(eventName) {
			// Clear out any content
			$(this.el).empty();

			_.each(this.model.models, function(channel) {
				$(this.el).append(new ChannelView({model: channel}).render().el);
				
				$("#settings .tab-container").append(this.panelTemplate(channel.toJSON()));
			}, this);

			// Select the first channel and display its config options
			this.$("li:first").trigger("click");
			return this;
		}
	});

	// View for a single channel + it's configured options
	window.ChannelView = Backbone.View.extend({
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
			var _model = this.model.toJSON();

			// Hide all tab container items
			$(".tab-container article.tab-content").each(function() {
				var _article = this;
				$(_article).css("display", "none");
			});

			// Get the DOM	reference for the selected channel
			var panel = $(".tab-container #"+_model.channel);
			panel.css("display", "block");

			// Check if the panel is empty
			if (panel.children().length == 1) {
				// Show the tab+content for the selected channel
				
				if (_model.grouped) {
					
					// Create a group panel
					_.each(_model.channel_data, function(g) {
						
						// Model for the grouped options
						var tempModel = new Backbone.Model({
							channel: _model.channel,
							group_label: _model.group_label,
							data: g.data,
							config_options: _model.config_options
						});
						
						// Render the grouped options
						panel.append(new GroupedChannelOptionsView({
							channel: _model.channel,
							
							channelKey: _model.group_key,
							
							objectId: _.uniqueId('group_'),
							
							model: tempModel.toJSON()
							
						}).render().el);
						
					}, this);
					
				} else {
					
					// Show the single config items
					_.each(_model.channel_data, function(item) {

						// Display each config param for the channel and bid the
						// channel config values, config key + channel to the view
						panel.append(new ChannelOptionItemView({

							// Channel associated with this config param
							channel: _model.channel,
							
							groupItem: false,

							// Key used to store an indivdiual channel config param
							channelKey: item.key,

							// Unique id for this view object
							objectId: _.uniqueId('param_'),

							model: item.data
						}).render().el);
					});
					
				}
				
			}

			// Render buttons used to add new channel config items to the UI
			var configPanel = $(".channel-options", panel);
			if (configPanel.children().length == 0) {

				// Iterate through each of the config options and render
				if (_model.grouped) {
					
					// Grouped panel options
					configPanel.append(new AddChannelConfigParamButton({
						
						// Label for adding a new config param
						channel: _model.channel,
						
						label: _model.group_label,
							
						// Set grouped to true
						grouped: true,
						
						// Key used to store the group config
						channelKey: _model.group_key,
						
						parentEl: panel,
						
						model: _model
					}).render().el);
					
				} else {
					
					$.each(_model.config_options, function(opt) {

						// Model reference for the current config option
						var configModel = _model.config_options[opt];

						// Add the button
						// Extra properties (options) to be passed on to the option item view
						// at their time of creation
						configPanel.append(new AddChannelConfigParamButton({

							// Channel associated with this config item
							channel: _model.channel,
							
							// Set grouped to false
							grouped: false,

							// Key used to store an individual channel config param
							channelKey: opt,

							// Parent element
							parentEl: panel,

							model: configModel
						}).render().el);
					});
				}
			}

			return false;
		},

		// Event callback when the status of the channel/filter is toggeled
		toggleChannelStatus: function(event) {
			$(event.currentTarget).toggleClass("switch-on").toggleClass("switch-off");

			// Update the status of the channel in the DB
			var params = {
				channel: this.model.get("channel"),
				enabled: $(event.currentTarget).hasClass("switch-on") ? 1: 0,
				command: "update_status"
			};

			// Update status via XHR
			$.post("<?php echo $channels_url; ?>", params, function(response) {
				if (! response.success) {
					// No river. The status could not be updated in the DB
					enabledChannels[params.channel] = params.enabled;
				}
			});

			return false;
		},

		render: function(eventName) {
			// Set the className of the parent DOM object
			$(this.el).addClass("button-view " + this.model.toJSON().channel);
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// View for the button(s) used for adding channel config parameters
	window.AddChannelConfigParamButton = Backbone.View.extend({
		tagName: "li",

		// Default template for single channel config options
		template: _.template($("#channel-option-listing").html()),

		// Events
		events: {
			// Add a config option form field to the UI
			// TODO: Implement adding of group options
			"click a": "addOptionItem"
		},

		addOptionItem: function(e) {

			if (this.options.grouped) {
				
				var groupItemView = new GroupedChannelOptionsView({
					
					// Channel
					channel: this.model.channel,
					
					// Channel key
					channelKey: this.model.group_key,
					
					// Unique id for this view
					objectId: _.uniqueId('group_'),
					
					// Model
					model: this.model
				});
				
				this.options.parentEl.append(groupItemView.render().el);
				
			} else {
				
				// Check if the value property exists in the model
				if (typeof(this.model.value) == "undefined") {
					this.model.value = "";
				}
				
				// Create the item independently to enable shifting focus to it
				// after creation
				var optionItem = new ChannelOptionItemView({

					// Name of the channel associated with this view item
					channel: this.options.channel,
					
					groupItem: false,

					// Config key for this config parameter
					channelKey: this.options.channelKey,

					// Unique Id for the view object
					objectId: _.uniqueId('param_'),

					model: this.model
				});

				this.options.parentEl.append(optionItem.render().el);

				// Shift focus to the newly created item
				$(":input", optionItem.el).focus();
			}

			return false;
		},

		render: function(eventName) {
			if (this.options.grouped) {
				this.model.label = this.options.label;
			}
			
			$(this.el).html(this.template(this.model));
			return this;
		}
	});
	
	// View for rendering grouped channel options
	window.GroupedChannelOptionsView = Backbone.View.extend({
		tagName: "div",
		
		className: "group-item",
		
		// Template
		template: _.template($("#channel-option-item-header").html()),
		
		initialize: function() {
			channelSettings[this.options.objectId] = this;
			this.configParams = [];
		},
		
		// Events
		events: {
			"click span > a" : "removeGroupOption"
		},
		
		// Event callback for removing a group option
		removeGroupOption: function() {
			var _group = this;
			$(_group.el).remove();
			delete channelSettings[_group.options.objectId];
			
			return false;
		},
		
		render: function(eventName) {
			// Render the header
			
			$(this.el).html(this.template({label: this.model.group_label}));
			
			var headerTemplate = _.template($("#channel-option-item-label").html());
			
			_.each(this.model.config_options, function(v, k) {
				
				// Set the value attribute
				v.value = (typeof(this.model.data) != 'undefined') 
				    ? this.model.data[k].value 
				    : "";
				
				var itemView = new ChannelOptionItemView({
					
					// Channel for this item
					channel: this.model.channel,
					
					// Channel key
					channelKey: k,
					
					// Unique id for this parameter
					objectId: _.uniqueId('param_'),
					
					// Model
					model: v
				});
				
				// Set the header template to NULL
				itemView.headerTemplate = headerTemplate;
				
				this.configParams.push(itemView);
				
				$(this.el).append(itemView.render().el);
			}, this);
			return this;
		}
	});
	

	// View for an individual channel option
	window.ChannelOptionItemView = Backbone.View.extend({
		tagName: "div",

		className: "input",
		
		headerTemplate: _.template($("#channel-option-item-header").html()),

		template: _.template($("#channel-option-item").html()),

		initialize: function() {
			if (this.options.groupItem == false) {
				channelSettings[this.options.objectId] = this;
			}
		},

		// Events
		events: {
			// Removes a channel config value from the UI
			"click span > a": "removeOption"
		},

		// Deletes the option item
		removeOption: function() {
			var _obj = this;
			$(_obj.el).remove();

			// Delete view object from the working list
			delete channelSettings[_obj.options.objectId];
			return false;
		},

		render: function(eventName) {
			$(this.el).html(this.headerTemplate({label: this.model.label}))
			
			if (this.model.type == 'select' && typeof(this.model.values) != 'undefined') {
				var dropdownTemplate = _.template($("#channel-option-dropdown").html());
				dropdownTemplate = $(dropdownTemplate());
				
				var selectItem = _.template($("#channel-option-dropdown-item").html());
				_.each(this.model.values, function(val) {
					var selected = 	(this.model.value == val) ? "selected" : "";
					dropdownTemplate.append(selectItem({value: val, selected: selected}));
				}, this);
				
				// Add the dropdown
				$(this.el).append(dropdownTemplate);
				
			} else {
				$(this.el).append(this.template(this.model));
			}
			
			return this;
		}
	});


	// Fetch the channels and display them
	var channels = new ChannelsCollection();
	channels.fetch();
	var channelListView = new ChannelListView({
		model: channels
	});
	
	channelListView.render();


	// ----------------------------------------------------------------
	// Event bindings for the action buttons
	// ("Delete River", Cancel, "Apply Changes")
	// ----------------------------------------------------------------
	
	// "Apply changes"
	$(".controls-buttons p.button-go > a").live('click', function() {

		// To hold the post data to be submitted via XHR
		var _post = {};
		_post["channels"] = {};

		// Get the config params for all the channels
		$.each(channelSettings, function(k, view) {
			var c = view.options.channel;
			
			if (typeof(_post["channels"][c]) == 'undefined') {
				_post["channels"][c] = [];
			}

			if (!$(view.el).hasClass("group-item")) {
				var _data = {
					key: view.options.channelKey,
					label: view.model.label,
					type: view.model.type,
					value: view.$(":input").val()
				};

				_post["channels"][c].push(_data);
				
			} else if ($(view.el).hasClass("group-item")) {
				
				var groupData = {
					group: true,
					groupKey: view.options.channelKey,
					data: []
				};
				_.each(view.configParams, function(groupItem) {
					groupData.data.push({
						key: groupItem.options.channelKey,
						label: groupItem.model.label,
						type: groupItem.model.type,
						value: groupItem.$(":input").val()
					});
				}, this);
				
				_post["channels"][c].push(groupData);
			}

			// Get the collaborators
			_post["collaborators"] = {};

			// Get the status of the river (private/public)
			_post["public"] = {};
		});
		

		// Check for new river
		var _field = $(".new-river :text");
		var _isNew = false;
		if (typeof(_field) != 'undefined') {
			_post[_field.attr("name")] = _field.val();
			_post["selected_channels"] = enabledChannels;
			_isNew = true;
		}
		
		// Send for saving
		$.post("<?php echo $save_settings_url; ?>", _post, function(response) {
			if (response.success) {
				// If a new river, redirect to its page
				if (_isNew && response.redirect_url != "") {
					window.location.href = response.redirect_url;
				} else {
					// Reload the channels list
 					// channelListView.model.trigger("reset");
				}
			}
		}, "json");
		return false;
	});

	// "Delete River"
	$(".controls-buttons p.button-delete > a").live('click', function() {
		$.post("<?php echo $delete_river_url?>", function(response) {
			if (response.success) {
				// Redirect to the dashboard
				window.location.href = "/dashboard";
			}
		}, "json");
	});

	// -------------------------------------------------------------

})();
</script>