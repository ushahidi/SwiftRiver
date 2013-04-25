/**
 * Channels module
 */
(function (root) {
	
	// Init the module
	Channels = root.Channels = {};
	
	// Channel plugin configuration model and collection
	var ChannelConfig = Backbone.Model.extend();
	
	var ChannelsConfig = Channels.ChannelsConfig = Backbone.Collection.extend({
		model: ChannelConfig,
		
		// Get all parameters in a channel
		getChannelConfig: function(channel) {
			return this.find(function(config) {
				return config.get("channel") == channel;
			}, this)
		},
		
		// Get options for single parameter in a channel
		getChannelOptionConfig: function(channel, key) {
			var channelConfig = this.getChannelConfig(channel);
			return channelConfig.get('options')[key];
		}
	});
    
	// Channels model and collection for channels in this river
	var Channel = Backbone.Model.extend({
		
		toggleEnabled: function() {
			this.save({enabled: !this.get("enabled")});
		}
	});
	
	var ChannelList = Channels.ChannelList = Backbone.Collection.extend({
		
		model: Channel,
		
		getChannel: function(channelKey) {
			return this.find(function(channel) {
				return channel.get("channel") == channelKey;
			}, this)
		},
		
		numActive: function() {
			return this.filter(function(channel) {
				return channel.get("enabled");
			}, this).length
		}
	});
	
	// Global channel list
	var channelList = Channels.channelList = new ChannelList();
	
	// Single channel in the modal listing
	var ChannelModalView = Backbone.View.extend({
		
		tagName: "li",
		
		events: {
			"click .remove": "removeChannel",
			"click": "editChannel",
		},
		
		initialize: function() {
            this.template = _.template($("#channel-modal-template").html());
		},
				
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		removeChannel: function() {
			view = this;
			
			if (view.isFetching)
				return;
			
			view.isFetching = true;
			
			var button = view.$("span.remove");
			
			// Show loading icon if there is a delay
			var t = setTimeout(function() { button.removeClass("icon-cancel").html(loading_image); }, 500);
			
			this.model.destroy({
				wait: true,
				complete: function() {
					view.isFetching = false;
				},
				success: function() {
					view.$el.fadeOut("slow");
				},
				error: function() {
					showFailureMessage("Unable to remove channel. Try again later.");
					button.html("");
					button.addClass("icon-cancel");
				}
			});
			return false;
		},
		
		editChannel: function() {
			this.trigger("edit", this.model);
			return false;
		}
		
	});
	
	// List of channels in a modal view
	var ChannelsModalView = Channels.ChannelsModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view",
		
		constructor: function(message, callback, context) {
			Backbone.View.prototype.constructor.apply(this, arguments);

			this.delegateEvents({
				"click li.add a": "showAddChannel",
				"click .modal-back": "showPrimaryView"
			});
		},
		
		initialize: function(options) {
			this.template = _.template($("#channels-modal-template").html());
			this.collection.url = options.baseUrl;
			this.collection.on("reset", this.addChannels, this);
			this.collection.on("add", this.addChannel, this);
		},
		
		addChannels: function() {
			this.collection.each(this.addChannel, this);
		},
		
		addChannel: function(channel) {
			var view = new ChannelModalView({model: channel});
			channel.view = view;
			view.on("edit", this.showEditChannel, this);
			this.$(".view-table ul").prepend(view.render().el);
		},
		
		render: function(eventName) {
			this.$el.html(this.template());
			this.addChannels();
			return this;
		},
		
		showAddChannel: function() {
			var view = new AddChannelModalView({
				collection: this.options.config,
				baseUrl: this.options.baseUrl
			});
			view.on("add", this.channelAdded, this);
			modalShow(view.render().$el);
			return false;
		},
		
		showEditChannel: function(channel) {
			var view = new EditChannelModalView({config: this.options.config, model: channel});
			view.on("change", this.channelUpdated, this);
			modalShow(view.render().$el);
		},
		
		channelAdded: function(channel) {
			this.collection.add(channel);
			modalBack();
			showSuccessMessage("Channel added successfully.", {flash:true});
		},
		
		channelUpdated: function(channel) {
			channel.view.render();
			modalBack();
			showSuccessMessage("Channel updated successfully.", {flash:true});
		},
		
		showSecondaryView: function(contentEl) {
			this.$('#modal-secondary').html(contentEl);
			contentEl.fadeIn('fast');
			this.$('#modal-primary > div').fadeOut('fast');
			this.$('#modal-container').scrollTop(0,0);	
		},
		
		showPrimaryView: function() {
			this.$('#modal-viewport').removeClass('view-secondary');
			this.$('#modal-primary > div').fadeIn('fast');
			this.$('#modal-secondary .modal-segment').fadeOut('fast');
			return false;
		}
	});
	
	var ChannelTypeConfigView = Backbone.View.extend({
		
		tagName: "li",
		
		events: {
			"click": "setSelected",
		},
		
		initialize: function() {
			this.template = _.template($("#channel-config-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		setSelected: function() {
			this.trigger("click", this.model);
			return false;
		}
	});
	
	var ChannelOptionConfigView = Backbone.View.extend({
		
		tagName: "li",
		
		events: {
			"click": "setSelected",
		},
		
		initialize: function() {
			this.template = _.template($("#channel-option-config-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		setSelected: function() {
			this.trigger("click", this.model);
			return false;
		}
		
	});
	
	var EditChannelParametersView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "modal-field-tabs-window",
		
		initialize: function() {
			this.template = _.template($("#edit-channel-template").html());
			this.textTemplate =  _.template($("#edit-channel-text-template").html());
			this.geoTemplate =  _.template($("#edit-channel-geo-template").html());
			this.fileTemplate = _.template($("#edit-channel-file-template").html());
		},
		
		render: function() {
			this.$el.html(this.template());
			this.renderOption(this.options.config, false);
			return this;
		},
		
		renderOption: function(option, isGroup) {
			if (option.get("type") == "text") {
				this.$("div").append(this.getTextView(option, isGroup));
			} else if (option.get("type") == "geo") {
				this.$("div").append(this.getGeoView(option, isGroup));
			} else if (option.get("type") == "file") {
				this.$("div").append(this.getFileView(option, isGroup));
			} else if (option.get("type") == "group") {
				_.each(option.get("options"), function(groupOption) {
					this.renderOption(new Backbone.Model(groupOption), true);
				},this);
			}
		},
		
		getModelValue: function(config, isGroup) {
			var key = config.get("key");
			var parameters = this.model.get("parameters");
			var val = null;
			if (parameters) {
				if (!isGroup && parameters.key == key) {
					val = parameters.value;
				} else {
					if (_.has(parameters.value, key)) {
						val = parameters.value[key];
					}
				}
			}
			return val;
		},
		
		getTextView: function(config, isGroup) {
			var data = config.toJSON();
			data.isGroup = isGroup;
			data.val = this.getModelValue(config, isGroup);
			return this.textTemplate(data);
		},
		
		getGeoView: function(config, isGroup) {
			return this.geoTemplate(config.toJSON());
		},
		
		getFileView: function(config, isGroup) {
			return this.fileTemplate(config.toJSON());
		},
		
		save: function(options) {
			var config = this.options.config;
			parameters = {
				"key": config.get("key"),
				"value": this.getValue(config)
			};		
			this.model.set("parameters", parameters);
			this.model.save({
				"parameters": parameters
			}, options);
		},
		
		getValue: function(option) {
			if (option.get("type") == "text") { 
				return this.$("input[name=" + option.get("key") + "]").val();
			} else if (option.get("type") == "group") { 
				var groupOptions = {};
				_.each(option.get("options"), function(groupOption) {
					var val = this.getValue(new Backbone.Model(groupOption));
					
					if (val != undefined && val.length) {
						groupOptions[groupOption["key"]] = val;
					}
				},this);
				return groupOptions;
			}
		}
	});
	
	var AddChannelModalView = Channels.AddChannelModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view modal-segment",
		
		events: {
			"click .button-submit": "saveChannel",
		},
		
		channelView: null,
		
		isFetching: false,
		
		initialize: function() {
			this.template = _.template($("#add-channel-modal-template").html());
			this.channelConfigTemplate = _.template($("#channel-config-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template());
			this.addChannelTypes();
			return this;
		},
		
		// Show a list of available channels
		addChannelTypes: function() {
			this.collection.each(function(config) {
				var view = new ChannelTypeConfigView({model: config});
				view.on("click", function(config) {
					// Show available options for the selected channel
					// Clear the view
					this.$(".modal-field-tabs-window").html("");
					this.$(".modal-field-tabs-menu li").remove();
					view.$el.siblings().removeClass("active");
					
					// Set the this channel as selected and render the options
					view.$el.addClass("active");
					this.addChannelOptionTypes(config);
				}, this);
				
				this.$(".modal-tabs-menu").append(view.render().el);
			}, this);
		},
		
		// Show a list of available options for a channel
		addChannelOptionTypes: function(channelConfig) {
			_.each(channelConfig.get("options"), function(optionConfig) {
				var view = new ChannelOptionConfigView({model: new Backbone.Model(optionConfig)});
				
				view.on("click", function(optionConfig) {
					view.$el.siblings().removeClass("active");
					view.$el.addClass("active");
					
					var channel = new Channel();
					channel.urlRoot = this.options.baseUrl;
					channel.set("channel", channelConfig.get("channel"));
					var channelView = this.channelView = new EditChannelParametersView({config: optionConfig, model: channel});					
					this.$(".modal-field-tabs-window").replaceWith(channelView.render().el);
				}, this);
				
				this.$(".modal-field-tabs-menu").append(view.render().el);
			}, this);
		},
		
		saveChannel: function() {
			view = this;
			
			if (this.channelView == null || view.isFetching)
				return false;
				
			view.isFetching = true;
			
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Adding channel</em>").append(loading_image_squares);
			}, 500);
			
			this.channelView.save({
				complete: function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
				},
				success: function() {
					view.trigger("add", view.channelView.model);
				},
				error: function() {
					showFailureMessage("There was a problem adding the channel. Try again later.");
				}
			});
			return false;
		}
	});
	
	var EditChannelModalView = Channels.EditChannelModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view modal-segment",
		
		channelView: null,
		
		isFetching: false,
		
		events: {
			"click .button-submit": "saveChannel",
		},
				
		initialize: function() {
			this.template = _.template($("#edit-channel-modal-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template());
			
			var parameters = this.model.get("parameters");
			var channel = this.model.get("channel");			
			var optionConfig = this.options.config.getChannelOptionConfig(channel, parameters.key);			
			optionConfig = new Backbone.Model(optionConfig);
			var channelView = this.channelView = new EditChannelParametersView({config: optionConfig, model: this.model});					
			this.$(".modal-field-tabs-window").replaceWith(channelView.render().el);
			
			return this;
		},
		
		saveChannel: function() {
			view = this;
			
			if (this.channelView == null || view.isFetching)
				return false;
				
			view.isFetching = true;
			
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Saving channel</em>").append(loading_image_squares);
			}, 500);
			
			this.channelView.save({
				complete: function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
				},
				success: function() {
					view.trigger("change", view.channelView.model);
				},
				error: function() {
					showFailureMessage("There was a problem adding the channel. Try again later.");
				}
			});
			return false;
		}
	});
	
	// Single channel in the drops channel listing
	var DroplistChannelView = Backbone.View.extend({
		
		tagName: "li",
				
		events: {
			"click": "toggleChannel"
		},
		
		initialize: function() {
            this.template = _.template($("#channel-drop-list-template").html());
			this.model.on("change", this.render, this);
			this.model.on("destroy", this.removeChannel, this);
		},
				
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			
			// Set class to active if this model is part of a drop filter
			var model = this.model;
			var filters = this.options.dropFilters;
			var view = this;
			if (filters.has("channel_ids")) {
				var channelFilters = filters.get("channel_ids");
				if (_.find(channelFilters, function(channelId) { return channelId == model.get("id"); })) {
					view.$el.addClass("active");
				}
			}
			
			return this;	
		},
		
		toggleChannel: function() {
			this.$el.toggleClass("active");
			
			// Update the drop filter by adding this model's channel id to the filter
			var model = this.model;
			var filters = this.options.dropFilters;
			if (filters.has("channel_ids")) {
				var channelFilters = filters.get("channel_ids");
				var newChannelFilters = [];
				if (_.find(channelFilters, function(channelId) { return channelId == model.get("id"); })) {
					// Removing a channel that is part of the filter
					newChannelFilters = _.filter(channelFilters, function(channelId) {return channelId != model.get("id");});
				} else {
					// Adding a channel to the filter
					newChannelFilters = _.union([model.get("id")], channelFilters);
				}
				
				if (_.size(newChannelFilters) > 0) {
					filters.set("channel_ids", newChannelFilters);
				} else {
					filters.unset("channel_ids");
				}
			} else {
				filters.set("channel_ids", [model.get("id")]);
			}
			
			return false;
		},
		
		removeChannel: function() {
			this.$el.fadeOut("slow");
		}
	});
	
	
	// List of channels in the drop listing
	var DroplistChannelsView = Channels.DroplistChannelsView = Backbone.View.extend({
		
		el: "#drops-channel-list",
		
		initialize: function() {
			this.collection.on("reset", this.addChannels, this);
			this.collection.on("add", this.addChannel, this);
			this.collection.on("add remove reset destroy", this.renderCount, this);
			this.render();
		},
		
		addChannels: function() {
			this.collection.each(this.addChannel, this);
		},
		
		addChannel: function(channel) {
			var view = new DroplistChannelView({model: channel, dropFilters: this.options.dropFilters});
			this.$(".filters-type-details ul").append(view.render().el);
		},
		
		render: function(eventName) {
			this.renderCount();
			this.addChannels();
			return this;
		},
		
		renderCount: function() {
			this.$(".total").html(this.collection.size());
		}
	});
	
}(this));