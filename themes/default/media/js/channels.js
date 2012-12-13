/**
 * Channels module
 */
(function (root) {
	
	// Init the module
	Channels = root.Channels = {};
	
	// Channel plugin configuration model and collection
	var ChannelConfig = Backbone.Model.extend();
	
	var ChannelsConfig = Backbone.Collection.extend({
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
    
	var channelsConfig = Channels.channelsConfig = new ChannelsConfig();
	
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
	
	// Single channel view
	var ChannelView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "container base",
		
		events: {
			"click a.remove-large": "confirmDeleteChannel"
		},
		
		initialize: function() {
            this.template = _.template($("#channel-template").html());
			this.model.on("change:enabled", this.activeChanged, this);
			
			// Add channel options
			this.channelOptions = new ChannelOptions();
			this.channelOptions.url = this.options.baseURL + "/options/" + this.model.get("id");
			this.channelOptions.on("add", this.addChannelOption, this);
			this.channelOptions.reset(this.model.get('options'));
		},
		
		activeChanged: function() {
			if (!this.model.get("enabled")) {
				// Channel no longer active, remove from view
				this.$el.fadeOut('slow');
			} else {
				this.$el.fadeIn('slow');
			}
		},
		
		addChannelOption: function(option) {
			var view = new ChannelOptionView({
					model: option,
					collection: this.channelOptions,
					channel: this.model,
                    baseURL: this.options.baseURL
				});
			this.$("section.channel-options").prepend(view.render().el);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			
			// Populate the parameter list
			var config = channelsConfig.getChannelConfig(this.model.get("channel"));
			_.each(config.get("options"), function(option) {
				var view = new ParameterView({model: option, channelView: this});
				this.$("div.add-parameter ul").append(view.render().el);
			}, this);
			
			// Render channel options
			this.channelOptions.each(this.addChannelOption, this);
			
			if (!_.size(config.get("options"))) {
				// Hide the 'Add parameter' button
				this.$("div.add-parameter").hide();
			}
			
			return this;	
		},
		
		confirmDeleteChannel: function() {
			new ConfirmationWindow("Remove this channel?", this.deleteChannel, this).show();
			return false;
		},
		
		deleteChannel: function() {
			var view = this;
			this.model.destroy();
			view.$el.fadeOut("slow");
		}
	});
	
	// The channels app
	var ChannelsControl = Channels.ChannelsControl = Backbone.View.extend({
		el: "div.channels",
		
		events: {
			"click .settings-toolbar p.create a": "showAddChannelsModal"
		},
		
		initialize: function() {
			this.collection.on("add", this.addChannel, this);
			this.collection.on("reset", this.addChannels, this);
			
			this.collection.on('reset', this.checkEmpty, this);
			this.collection.on('add', this.checkEmpty, this);
			this.collection.on('change:enabled', this.checkEmpty, this);
			this.collection.on('remove', this.checkEmpty, this);
		},
		
		addChannel: function(channel) {
			var view = new ChannelView({model: channel, baseURL: this.options.baseURL});
			this.$("div.col_12").append(view.render().el);
		},
		
		addChannels: function() {
			this.collection.each(this.addChannel, this);
		},

		showAddChannelsModal: function() {
			var addChannelsView = new AddChannelsView({collection: this.collection});
			modalShow(addChannelsView.render().el);
			return false;
		},
		
		checkEmpty: function(model) {
			if (this.collection.length && this.collection.numActive()) {
				this.$(".alert-message").fadeOut('slow');
			} else {
				this.$(".alert-message").fadeIn('slow');
			}
		}
	});
	
}(this));