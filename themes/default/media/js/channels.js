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
	
	// Single channel option in a river and collection of the same
	var ChannelOption = Backbone.Model.extend();
	
	var ChannelOptions = Backbone.Collection.extend({
		model: ChannelOption
	});
	
	// Single channel in the modal "Add channels" window
	var AddChannelsViewItem = Backbone.View.extend({
		
		tagName: "label",
		
		events: {
			"change input": "toggleChannel"
		},
        
        initialize: function() {
          this.template = _.template($("#channels-modal-channel-item-template").html());  
        },
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));			
			return this;	
		},
		
		toggleChannel: function() {
			var channel = this.collection.getChannel(this.model.get("channel"));
			
			if (channel !== undefined) {
				// Channel exists, simple toggle its active state.
				channel.toggleEnabled();
			} else {
				// Channel doesn't exist in the river, create it
				var button = $("div.channels .settings-toolbar p.button-blue").clone();
				var loading_msg = $('<div>Adding channels, please wait...</div>').prepend(window.loading_image.clone());
				
				// Show the loading message if syncing takes longer than 500ms
				var t = setTimeout(function() { $("div.channels .settings-toolbar p.button-blue").replaceWith(loading_msg); }, 500);
				
				this.collection.create({channel: this.model.get("channel")}, {
					wait: true,
					complete: function() {
						clearTimeout(t);
						loading_msg.replaceWith(button);
					}
				});
			}
		}
	});
	
	// The modal "Add channels" window
	var AddChannelsView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "modal",
		        
        initialize: function() {
            this.template = _.template($("#add-channels-modal-template").html());
        },
		
		render: function() {
			this.$el.html(this.template());
			
			// Render the channel list
			channelsConfig.each (function(config) {
				
				// Set the flag for whether the channel exists in a river
				var channel = this.collection.getChannel(config.get("channel"));
				config.set("added", channel !== undefined && channel.get("enabled"));
				
				var view = new AddChannelsViewItem({model: config, collection: this.collection});
				this.$("form.channels").append(view.render().el);
			}, this);
					
			return this;	
		},
	});
	
	
	// Single parameter in the "Add parameter" drop down.
	var ParameterView = Backbone.View.extend({
		
		tagName: "li",
		
		events: {
			"click a": "addParameter"
		},
        
        initialize: function() {
            this.template = _.template($("#parameter-template").html());
        },
		
		addParameter: function() {
			this.options.channelView.channelOptions.add({
					key: this.model.key,
					value: null
				});
			this.$el.parents(".popover-window").fadeOut('fast').unbind();
			return false;
		},
		
		render: function() {
			this.$el.html(this.template(this.model));
			return this;
		}
	});
	
	// Single channel option view
	var ChannelOptionView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter",
		
		
		events: {
			"click .remove-small": "remove",
			"focusout input": "hideSaveButton",
			"click .button-blue a.save": "save",
			"keyup input": "keypressSave",
			"click .button-blue a.edit": "edit",
			"click .button-blue a.cancel": "cancel",
		},
		
		initialize: function(options) {
            this.template = _.template($("#channel-option-template").html());
			this.config = channelsConfig.getChannelOptionConfig(options.channel.get("channel"), this.model.get('key'));
			this.renderMode = "view";
			this.savingMode = false;
			this.actionsHTML = null;
		},
		
		// Depending on the option type, return a <input>, <select> or group of these
		createInputHtml: function(config, value, group) {
			var placeholder = "";
			if (config.placeholder != undefined && config.placeholder) {
				placeholder = config.placeholder;
			}
			
			if (value == undefined || value == null) {
				value = ""
			}
			
			var input = "";
			this.actionsHTML = "";
			if (config.type == "select") {
				input = '<select name="' + config.key + '">'
				input += '<option value="">Select one</option>';
				_.each(config.values, function(value) {
					input += '<option value="' + value + '">' + value + '</option>';
				});
				input += '</select>'
			} else if (config.type == "group") {
				_.each(config.group_options, function(conf) {
					input += '<label><p class="field">' + conf.label + '</p>'
					input += this.createInputHtml(conf, "", true);
					input += '</label>';
				}, this);
				input += '<span class="button-blue"><a class="save" href="#">Save</a></span>';
			} else if (config.type == "file") {
				input = '<span class="button-blue has_file"><a href="#">Select file</a><input type="file" name="file"></span>';
			} else {
				if (value != "" && this.renderMode == "view") {
					input = '<p class="field-text">'+value+'</p>';
					this.actionsHTML += '<p class="actions"><span class="button-blue"><a class="edit" href="#">Edit</a></span></p>';
				} else {
					input = '<input type="text" name="' + config.key + '" placeholder="' + placeholder + '" value="' + value + '" />';
					if (value != "" && !group) {
						this.actionsHTML += '<p class="actions"><span class="button-blue"><a class="cancel"href="#">Cancel</a></span></p>';
					}
				}

				if (!group) {
					this.actionsHTML += '<p class="actions" style="display:none"><span class="button-blue"><a class="save" href="#">Save</a></span></p>';
				}
			}
			return input;
		},
		
		getInputField: function() {
			return this.createInputHtml(this.config, this.model.get("value"));
		},
		
		render: function() {
			var data = this.model.toJSON();
			data.input = this.getInputField();
			data.config = this.config;
			this.$el.html(this.template(data));
			if (this.actionsHTML != null && this.actionsHTML != "") {
				this.$("p.remove-small").after(this.actionsHTML);
			}
			
			// Attach the file upload handler
			if (this.config.type == 'file')
			{
				this.attachFileUploadHandler();
			}
			
			return this;
		},
				
		remove: function() {
			if (this.model.isNew()) {
				// Not synced yet, just remove from view
				this.$el.fadeOut("slow");
			} else {
				var view = this;
				this.model.destroy({
					wait: true,
					success: function() {
						view.$el.fadeOut("slow");
					}
				});
			}
		},
		
		showSaveButton: function() {
			this.$("span.error-message").remove();
			this.$("a.save").parents("p.actions").fadeIn("slow");
		},
		
		hideSaveButton: function() {
			var newValue = this.$("input[type=text]").val();
			if( ! newValue || newValue == this.model.get("value")  ) {
				this.$("a.save").parents("p.actions").fadeOut();
			}
		},
		
		keypressSave: function(e) {
			if(e.which == 13){
				this.save();
				return false;
			} else {
				var newValue = $.trim(this.$("input[type=text]").val());
				if(newValue != "" && newValue && newValue != this.model.get("value")) {
					this.showSaveButton();
				} else {
					this.hideSaveButton();
				}
			}
		},
		
		attachFileUploadHandler: function() {
			var view = this;
			var loading_msg = window.loading_image.clone();
			this.$('input[type=file]').fileupload({
				dataType: 'json',
				url: this.options.baseURL + "/file/" + this.options.channel.get("id"),
				add: function(e, data) {
					data.formData = {'key': view.config.key}
					data.submit();
				},
				start: function (e) {
					// Disable the inputs and show the loading icon
					view.$("span.error-message").remove();
					view.$("input").attr("disabled", "disabled").blur();
					view.$(".button-blue").hide().after(loading_msg);
				},
				done: function (e, data) {
					if (!data.result.length) {
						var error_msg = $('<span class="error-message">No parameters were found in the file</span>');
						loading_msg.replaceWith(error_msg).remove();
						view.$(".button-blue").fadeIn()
						view.$("input").removeAttr("disabled");
						return;
					} else {
						view.collection.add(data.result);
						view.$el.hide().remove();
					}
				},
				fail: function (e, data) {
					response = data.jqXHR;
					var message = "Oops, file upload failed. Try again.";
					if (response.status == 400) {
						message = JSON.parse(response.responseText)["error"];
					} 
					var error_msg = $('<span class="error-message">' + message + '</span>');
					loading_msg.replaceWith(error_msg).remove();
					 view.$(".button-blue").fadeIn()
					view.$("input").removeAttr("disabled");
				}
			});
		},
		
		// Set the models value and sync to the server
		save: function() {
			// Disable the inputs and show the loading icon
			this.$("input, select").attr("disabled", "disabled").blur();
			var loading_msg = window.loading_image.clone();
			this.$("a.save").parents("p.actions").hide().after(loading_msg);
			
			// Get the user's inputs and do grouping for group options
			var inputs = this.$("input, select");
			if (inputs.length == 1) {
				value = inputs[0].value;
			} else {
				// Grouped options
				value = {};
				$.each(inputs, function(index, val) {
					value [val.name] = val.value;
				});
			}
			
			// Do the save showing a sucess/error message
			this.savingMode = true;
			var view = this;
			this.model.save({'value': value}, {
				wait: true,
				complete: function() {

				},
				success: function() {
					var success_msg = $('<span class="success-message">Saved</span>');
					loading_msg.replaceWith(success_msg).remove();					
					success_msg.fadeOut(4000, function() {
						$(this).remove();
						view.renderMode = "view";
						view.render();
					});
				},
				error: function(model, response) {
					var message = "Oops, unable to save. Try again.";
					if (response.status == 400) {
						message = JSON.parse(response.responseText)["error"];
					} 
					var error_msg = $('<span class="error-message">' + message + '</span>');
					loading_msg.replaceWith(error_msg).remove();
					view.$("input, select").removeAttr("disabled");
				}
			});
			
			this.savingMode = false;
			return false;
		},

		// Create edit fields for the channel option
		edit: function() {
			this.renderMode = "edit";
			this.render();
			return false;
		},

		// When the edit operation is cancelled
		cancel: function() {
			// Only process cancel action when not in saving mode
			if (!this.savingMode) {
				this.renderMode = "view";
				this.render();
			}
			return false;
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
			this.model.destroy({
				wait: true,
				success: function() {
					view.$el.fadeOut("slow");
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

		className: "modal",
		
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
			this.$('#modal-viewport').addClass('view-secondary');
			var view = new AddChannelModalView({
				collection: this.options.config,
				baseUrl: this.options.baseUrl
			});
			view.on("add", this.channelAdded, this);			
			this.showSecondaryView(view.render().$el);
			return false;
		},
		
		showEditChannel: function(channel) {
			var view = new EditChannelModalView({config: this.options.config, model: channel});
			view.on("change", this.channelUpdated, this);
			this.showSecondaryView(view.render().$el);
		},
		
		channelAdded: function(channel) {
			this.collection.add(channel);
			this.showPrimaryView();
		},
		
		channelUpdated: function(channel) {
			channel.view.render();
			this.showPrimaryView();
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
		
		tagName: "div",

		className: "modal-segment",
		
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
			if (this.channelView == null || this.isFetching)
				return false;
				
			this.isFetching = true;
			view = this;
			
			this.channelView.save({
				complete: function() {
					view.isFetching = false;
				},
				success: function() {
					view.trigger("add", view.channelView.model);
				},
				error: function() {
					console.log("Channel save error");
				}
			});
			return false;
		}
	});
	
	var EditChannelModalView = Channels.EditChannelModalView = Backbone.View.extend({
		
		tagName: "div",

		className: "modal-segment",
		
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
			if (this.channelView == null || this.isFetching)
				return false;
				
			this.isFetching = true;
			view = this;
			
			this.channelView.save({
				complete: function() {
					view.isFetching = false;
				},
				success: function() {
					view.trigger("change", view.channelView.model);
				},
				error: function() {
					console.log("Channel save error");
				}
			});
			return false;
		}
	});
	
}(this));