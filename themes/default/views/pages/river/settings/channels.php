<div id="content" class="settings channels cf">
	<div class="center">
		<div class="col_12">
			<div class="settings-toolbar">
				<p class="button-blue button-small create"><a href="/markup/modal-channels.php" class="modal-trigger"><span class="icon"></span>Add channels</a></p>
			</div>

			<div class="alert-message blue" style="display:none;">
				<p><strong>No channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
			</div>
			
			<!-- CHANNELS WILL GO HERE -->

		</div>
	</div>
</div>

<script type="text/template" id="channels-modal-channel-item-template">
	<input type="checkbox" name="<%= channel %>" <%= added ? "checked" : "" %>/>
	<%= name %>
</script>

<script type="text/template" id="add-channels-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Add Channel</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body select-list">
		<form class="channels">
			<!-- CHANNEL LIST WILL GO HERE -->
		</form>
	</div>
</script>

<script type="text/template" id="parameter-template">
<a href="#"><%= label %></a>
</script>

<script type="text/template" id="channel-option-template">
	<label>
		<p class="field"><%= config.label %></p>
		<% if (typeof title !== 'undefined') { %>
			<p class="title"><%= title.substring(0, 19) + (title.length > 20 ? "..." : "") %></p>
		<% } %>
		<%= input %>
		<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
	</label>
</script>

<script type="text/template" id="channel-template">
	<header class="cf">
		<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
		<div class="property-title">
			<a href="#" class="avatar-wrap"><img onerror="showDefaultAvatar(this)" src="<?php echo URL::site('media/img'); ?>/channel-<%= channel %>.gif" /></a>
			<h1><%= name %></h1>
			<div class="popover add-parameter">
				<p class="button-white has-icon add"><a href="#" class="popover-trigger"><span class="icon"></span>Add parameter</a></p>
				<ul class="popover-window base">
				</ul>
			</div>
		</div>
	</header>
	<section class="property-parameters channel-options">
	</section>
</script>

<script type="text/template" id="confirm-window-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Remove this channel?</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body">
		<div class="settings-toolbar">
			<p class="button-blue"><a href="#">Yes</a></p>
			<p class="button-blank close"><a href="#">Nope, nevermind</a></p>
		</div>
	</div>
</script>

<script type="text/javascript">

$(function() {
	
	// Base fetch url
	var base_url = "<?php echo $base_url; ?>"
	
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
	var channelsConfig = new ChannelsConfig();
	
	// Bootstrap the channel configuration before anything else
	channelsConfig.reset(<?php echo $channels_config; ?>);
	
	// Channels model and collection for channels in this river
	var Channel = Backbone.Model.extend({
		
		toggleEnabled: function() {
			this.save({enabled: !this.get("enabled")});
		}
	});
	
	var Channels = Backbone.Collection.extend({
		
		model: Channel,
		
		url : base_url + "/manage",
		
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
	var channels = new Channels();
	
	// Single channel option in a river and collection of the same
	var ChannelOption = Backbone.Model.extend();
	
	var ChannelOptions = Backbone.Collection.extend({
		model: ChannelOption
	});
	
	// Single channel in the modal "Add channels" window
	var AddChannelsViewItem = Backbone.View.extend({
		
		tagName: "label",

		template: _.template($("#channels-modal-channel-item-template").html()),
		
		events: {
			"change input": "toggleChannel"
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));			
			return this;	
		},
		
		toggleChannel: function() {
			var channel = channels.getChannel(this.model.get("channel"));
			
			if (channel !== undefined) {
				// Channel exists, simple toggle its active state.
				channel.toggleEnabled();
			} else {
				// Channel doesn't exist in the river, create it
				var button = $("div.channels .settings-toolbar p.button-blue").clone();
				var loading_msg = $('<div>Adding channels, please wait...</div>').prepend(window.loading_image.clone());
				
				// Show the loading message if syncing takes longer than 500ms
				var t = setTimeout(function() { $("div.channels .settings-toolbar p.button-blue").replaceWith(loading_msg); }, 500);
				
				channels.create({channel: this.model.get("channel")}, {
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
		
		template: _.template($("#add-channels-modal-template").html()),
		
		render: function() {
			this.$el.html(this.template());
			
			// Render the channel list
			channelsConfig.each (function(config) {
				
				// Set the flag for whether the channel exists in a river
				var channel = channels.getChannel(config.get("channel"));
				config.set("added", channel !== undefined && channel.get("enabled"));
				
				var view = new AddChannelsViewItem({model: config});
				this.$("form.channels").append(view.render().el);
			}, this);
					
			return this;	
		},
	});
	
	// The delete channel confirmation modal window
	var ConfirmationWindow = Backbone.View.extend({
		tagName: "article",
		
		className: "modal",
		
		template: _.template($("#confirm-window-template").html()),
		
		events: {
			"click .button-blue a": "confirm"
		},
		
		show: function() {
			modalShow(this.render().el);
		},
		
		render: function() {
			this.$el.html(this.template());
			return this;	
		},
		
		confirm: function() {
			modalHide();
			this.options.callback.call(this.options.context);
			return false;
		}
	});
	
	// Single parameter in the "Add parameter" drop down.
	var ParameterView = Backbone.View.extend({
		
		tagName: "li",
		
		template: _.template($("#parameter-template").html()),
		
		events: {
			"click a": "addParameter"
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
		
		template: _.template($("#channel-option-template").html()),
		
		events: {
			"click .remove-small": "remove",
			"focusout input": "hideSaveButton",
			"click .button-blue a": "save",
			"keyup input": "keypressSave",
		},
		
		initialize: function(options) {
			this.config = channelsConfig.getChannelOptionConfig(options.channel.get("channel"), this.model.get('key'));
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
				}, this)
				input += '<span class="button-blue"><a href="#">Save</a></span>';
			} else if (config.type == "file") {
				input = '<span class="button-blue has_file"><a href="#">Select file</a><input type="file" name="file"></span>';
			} else {
				input = '<input type="text" name="' + config.key + '" placeholder="' + placeholder + '" value="' + value + '" />';
				if (!group) {
					input += '<span class="button-blue" style="display:none"><a href="#">Save</a></span>';
				}
			}
			return input
		},
		
		getInputField: function() {
			return this.createInputHtml(this.config, this.model.get("value"));
		},
		
		render: function() {
			var data = this.model.toJSON();
			data.input = this.getInputField();
			data.config = this.config;
			this.$el.html(this.template(data));
			
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
			this.$(".button-blue").fadeIn("slow");
		},
		
		hideSaveButton: function() {
			var newValue = this.$("div.parameter input[type=text]").val();
			if( ! newValue || newValue == this.model.get("value")  ) {
				this.$("div.parameter .button-blue").fadeOut();
			}
		},
		
		keypressSave: function(e) {
			if(e.which == 13){
				this.save();
				return false;
			} else {
				var newValue = this.$("input[type=text]").val();
				if(newValue && newValue != this.model.get("value") && newValue != "" ) {
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
				url: base_url + "/file/" + this.options.channel.get("id"),
				add: function(e, data) {
					data.formData = {'key': view.config.key}
					data.submit();
				},
				start: function (e) {
					// Disable the inputs and show the loading icon
					view.$("span.error-message").remove();
					view.$("input").attr("disabled", "disabled").blur();
					view.$("div.parameter .button-blue").hide().after(loading_msg);
				},
				done: function (e, data) {
					if (!data.result.length) {
						var error_msg = $('<span class="error-message">No parameters were found in the file</span>');
						loading_msg.replaceWith(error_msg).remove();
						view.$("div.parameter .button-blue").fadeIn()
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
					view.$("div.parameter .button-blue").fadeIn()
					view.$("input").removeAttr("disabled");
				}
			});
		},
		
		// Set the models value and sync to the server
		save: function() {
			// Disable the inputs and show the loading icon
			this.$("input, select").attr("disabled", "disabled").blur();
			var loading_msg = window.loading_image.clone();
			this.$(".button-blue").hide().after(loading_msg);
			
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
			
			return false;
		}
	});
	
	// Single channel view
	var ChannelView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "container base",
		
		template: _.template($("#channel-template").html()),
		
		events: {
			"click a.remove-large": "confirmDeleteChannel"
		},
		
		initialize: function() {
			this.model.on("change:enabled", this.activeChanged, this);
			
			// Add channel options
			this.channelOptions = new ChannelOptions();
			this.channelOptions.url = base_url + "/options/" + this.model.get("id");
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
					channel: this.model
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
			
			return this;	
		},
		
		confirmDeleteChannel: function() {
			new ConfirmationWindow({callback: this.deleteChannel, context: this}).show();
			return false;
		},
		
		deleteChannel: function() {
			var view = this;
			this.model.destroy();
			view.$el.fadeOut("slow");
		}
	});
	
	// The channels app
	var ChannelsControl = Backbone.View.extend({
		el: "div.channels",
		
		events: {
			"click .settings-toolbar p.create a": "showAddChannelsModal"
		},
		
		initialize: function() {
			channels.on("add", this.addChannel, this);
			channels.on("reset", this.addChannels, this);
			
			channels.on('reset', this.checkEmpty, this);
			channels.on('add', this.checkEmpty, this);
			channels.on('change:enabled', this.checkEmpty, this);
			channels.on('remove', this.checkEmpty, this);
		},
		
		addChannel: function(channel) {
			var view = new ChannelView({model: channel});
			this.$("div.col_12").append(view.render().el);
		},
		
		addChannels: function() {
			channels.each(this.addChannel, this);
		},

		showAddChannelsModal: function() {
			var addChannelsView = new AddChannelsView();
			modalShow(addChannelsView.render().el);
			return false;
		},
		
		checkEmpty: function(model) {
			if (channels.length && channels.numActive()) {
				this.$(".alert-message").fadeOut('slow');
			} else {
				this.$(".alert-message").fadeIn('slow');
			}
		}
	});
	
	// Bootstrap the channel control
	new ChannelsControl();
	channels.reset(<?php echo $channels; ?>);
});

</script>