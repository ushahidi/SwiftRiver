<script type="text/javascript">
/**
 * Backbone JS wiring for the river settings view. Used by the settings
 * view and new river pages
 *
 * @author     Ushahidi Dev Team
 * @package    SwiftRiver - https://github.com/Swiftriver_v2
 * @copyright  (c) 2012 - Ushahidi Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
(function() {
	
	window.River = Backbone.Model.extend({
		urlRoot: "<?php echo $river_url_root?>"
	});

	window.ChannelOption = Backbone.Model.extend({
		urlRoot: "<?php echo $channel_options_url?>"
	});

	window.ChannelConfigOption = Backbone.Model.extend();

	window.ChannelOptionsCollection = Backbone.Collection.extend({
		model: ChannelOption
	});

	window.ChannelConfigOptionsCollection = Backbone.Collection.extend({
		model: ChannelConfigOption
	});

	window.Channel = Backbone.Model.extend({
		urlRoot: "<?php echo $channels_url; ?>",

		// Enables the channel
		setEnabled: function(enabled) {
			this.save({enabled: enabled}, {wait: true});
		}
	});

	window.ChannelsCollection = Backbone.Collection.extend({
		model: Channel
	});


	// View for the river view
	window.RiverSettingsView = Backbone.View.extend({
		el: $("div#settings"),

		initialize: function() {
			this.model = new River(<?php echo $river_data; ?>);
			var river = this.model;

			this.$("input[name='river_public']").each(function(){
				if ($(this).val() == river.get("river_public")) {
					$(this).attr("checked", "checked");
				} else {
					$(this).removeAttr("checked");
				}
			});
		},

		events: {
			// When the river is renamed
			"click button#rename_river": "renameRiver",

			// When the privacy settings are changed
			"click input[name='river_public']": "toggleRiverPrivacy",

			// When delete river is confirmed
			"click div.actions .dropdown li.confirm a": "deleteRiver",
		},

		renameRiver: function(e) {
			// Get the name the river
			var riverName = $("input#river_name").val();
			if ($.trim(riverName).length > 0 && this.model.get("river_name") != riverName) {
				// Save the new river name
				this.model.save({name_only: true, river_name: riverName}, {
					wait: true,
					success: function(model, response) {
						if (response.success) {
							window.location.href = response.redirect_url;
						}
					}
				});
			}
		},

		toggleRiverPrivacy: function(e) {
			var radio = $(e.currentTarget);
			if ($(radio).val() != this.model.get("river_public")) {

				// String for the new status of the river
				var newStatus = ($(radio).val() == 1) ? 
				    "<?php echo __('public') ?>" 
				    : "<?php echo __('private'); ?>";

				// Show confirmation dialog before updating
				if (confirm("<?php echo __('Are you sure you want to make this river '); ?>" + newStatus + '?')) {

					// Update the privacy settings of the river
					this.model.save({privacy_only: true, river_public: $(radio).val()}, {
						wait: true, 
						success: function(model, response) {
							if (response.success) {
								window.location.href = response.redirect_url;
							}
						}
					});
				} else {
					// Prevent the switch
					e.preventDefault();
				}
			}
		},

		// When the river is deleted
		deleteRiver: function(e) {
			this.model.destroy({wait: true, success: function(model, response) {
				if (response.success) {
					window.location.href = response.redirect_url
				}
			}});

			return false;
		}
	});


	// Global reference for the channles listing
	window.ChannelsList = new ChannelsCollection();


	// View for the channel listing
	window.ChannelsListView = Backbone.View.extend({

		el: $("#settings div.tab-controls > ul.tabs"),

		initialize: function() {
			ChannelsList.on('reset', this.addChannels, this);
		},

		// Adds a channel item
		addChannel: function(channel) {
			var view = new ChannelView({model: channel});
			this.$el.append(view.render().el);
		},

		addChannels: function() {
			ChannelsList.each(this.addChannel, this);

			// Select the first element
			this.$("li:first").trigger("click");
		}
	});

	// View for a single channel item
	window.ChannelView = Backbone.View.extend({

		tagName: "li",

		className: "button-view",

		template: _.template($("#channel-template").html()),

		initialize: function() {
			// Panel options view
			this.optionsPanelView = new ChannelOptionsPanelView({channelView: this});

			// Register events for the collections
			this.channelOptions = new ChannelOptionsCollection();
			this.channelOptions.on('reset', this.addChannelOptions, this);

			this.channelConfigOptions = new ChannelConfigOptionsCollection();
			this.channelConfigOptions.on('reset', this.addChannelConfigOptions, this);
		},

		events: {
			// When the channel is activated
			"click a span.switch": "toggleChannelStatus",

			// When the channel is selected
			"click ": "selectChannel"
		},

		toggleChannelStatus: function(e) {
			$(e.currentTarget).toggleClass("switch-on").toggleClass("switch-off");
			var enabled = $(e.currentTarget).hasClass("switch-on") ? 1 : 0;

			// Save the status of the filter the server
			this.model.setEnabled(enabled);

			return false;
		},


		selectChannel: function(e) {
			// Remove "active" class from all channels
			$("ul.tabs li.button-view").removeClass("active");

			// Add "active" class to the current channel
			$(e.currentTarget).addClass("active");

			// Hide all panels
			$(".tab-container article.tab-content").each(function() {
				var panel = this;
				$(panel).css("display", "none");
			});

			// Display the current panel
			$(this.optionsPanelView.el).css("display", "block");

			return false;
		},

		// Helper function that creates and saves a channel filter option
		createAndSaveChannelOption: function(option) {
			var channelView = this;
			var controlView = option.get("srcControlView");

			// Remove the control view from the option
			option.unset("srcControlView");

			option.save(null, {
				wait: true, 
				success: function(model, response) {
					if (response.success) {
						var createdOption = new ChannelOption(response.data);
						createdOption.set({new: true});

						// Add the option to the UI
						channelView.optionsPanelView.addChannelOption(createdOption);

						// Clear the text field and remove any error CSS classes
						$(":text", controlView).val("");
						$(":text", controlView).removeClass("error");
						
					} else {

						// Get the control view from the option and re-enable the text fields
						$("input", controlView).removeAttr("disabled");

						// Add error class
						$(":text", controlView).addClass("error");
					}

				}
			});
			
		},

		// Adds a single channel filter option on the UI
		addChannelOption: function(option) {

			// Check if the channel filter option is newly added via the UI
			if (typeof option.get("id") == "undefined") {
				
				option.set({channel: this.model.get("channel")});

				// Check if the channel is enabled
				if (typeof (this.model.get("id")) == "undefined") {
					var channelView = this;

					// Enable the channel for the current river
					this.model.save({enabled: 1}, {
						wait: true,
						success: function(model, response) {
							// If sucessful, proceed
							if (response.success) {
								channelView.$("a span.switch").toggleClass("switch-on").toggleClass("switch-off");

								// Save the channel option
								option.set({channel_filter_id: response.id});
								channelView.createAndSaveChannelOption(option);
							}
						}
					});
				} else {
					// Channel already exists for the river, set the id and save
					option.set({channel_filter_id: this.model.get("id")});
					this.createAndSaveChannelOption(option);
				}

			} else {
				// Add the option to the panel view
				this.optionsPanelView.addChannelOption(option);
			}

		},

		// Adds a collection of channel filter options to the IO
		addChannelOptions: function() {
			this.channelOptions.each(this.addChannelOption, this);
		},

		// Adds the channel filter options
		addChannelConfigOption: function(option) {

			var channelFilterId = this.model.get("id");

			// Traverse each of the options and create a control view
			_.each(option.toJSON(), function(k,v){ 
				var data = k;
				data.key = v;
				data.channel_filter_id = channelFilterId;

				// Control view
				var controlView = new ChannelOptionControlView({
					model: new ChannelConfigOption(data),
					channelView: this
				});

				this.optionsPanelView.addOptionControlView(controlView);

			}, this);
			
		},

		addChannelConfigOptions: function() {
			this.channelConfigOptions.each(this.addChannelConfigOption, this);
		},

		render: function(eventName) {
			// Add a class
			this.$el.addClass(this.model.get("channel"));

			// Set the data attribute
			this.$el.attr("data-channel-filter-id", this.model.get("id"));

			// Render
			this.$el.html(this.template(this.model.toJSON()));
			this.optionsPanelView.render();

			this.channelConfigOptions.reset(this.model.get('options'));
			this.channelOptions.reset(this.model.get('data'));
			return this;
		}
	});


	// View for the panel containing the channel option items
	window.ChannelOptionsPanelView = Backbone.View.extend({

		container: $("#settings div.tab-container"),

		tagName: "article",

		className: "tab-content filters",

		template: _.template($("#channel-option-panel-template").html()),

		// Adds an option control view/button to the options panel
		addOptionControlView: function(control) {
			this.$("ul.channel-options").append(control.render().el);
		},

		// Adds a channel option the panel
		addChannelOption: function(channelOption) {
			var optionView = new ChannelOptionItemView({model: channelOption});

			if (typeof channelOption.get("new") == "undefined") {
				this.$el.append(optionView.render().el);
			} else {
				// Newly added item - via the UI
				this.$("ul.channel-options").after(optionView.render().el);
			}
		},

		render: function(eventName) {
			this.$el.css("display", "none");
			this.$el.append(this.template());

			$(this.container).append(this.$el);

			return this;
		}
	});

	// View for the individual option item
	window.ChannelOptionItemView  = Backbone.View.extend({

		tagName: "div",

		className: "row cf",

		headerTemplate: _.template($("#option-item-header-template").html()),

		template: _.template($("#channel-option-template").html()),

		initialize: function() {
			if (typeof this.model.get("value") == "undefined") {
				this.model.set({value: ""});
			}
		},

		events: {
			"click span > a": "removeOptionItem"
		},

		// Callback for the "remove" event
		removeOptionItem: function(e) {
			if (typeof this.model.get("id") == 'undefined') {
				this.$el.hide(300);
			} else {
				var view = this;
				// Delete from the server
				this.model.destroy({wait: true, success: function(model, response) {
					view.$el.hide(300);
				}});
			}
			return false;
		},

		render: function(eventName) {
			this.$el.html(this.headerTemplate(this.model.get("data")));
			$(this.el).append(this.template(this.model.toJSON()));
			return this;
		}
	});

	// View for controls used to add option items
	window.ChannelOptionControlView = Backbone.View.extend({
		
		tagName: "li",

		template: _.template($("#channel-option-control-template").html()),

		// Template for the "Add button"
		controlButton: _.template($("#channel-option-control-button-template").html()),

		initialize: function() {

			if (this.model.get("type") == "text") {
				// Input template
				this.controlTemplate = _.template($("#channel-option-input-template").html());
			} else if (this.model.get("type") == "select" || this.model.get("type") == "dropdown") {

				// Dropdown template
				this.controlTemplate = _.template($("#channel-option-dropdown-template")).html();
			}
		},

		events: {
			"click button.channel-button": "addChannelFilterOption",

			"keypress .channel-option-input :text": "toggleButtonStatus",

			"change .channel-option-input :text": "toggleButtonStatus"
		},


		addChannelFilterOption: function(e) {
			if (this.model.get("type") == "text") {
				var field = this.$(".channel-option-input :text");
				var value = $(field).val();
				
				var channelOption = new ChannelOption({
					key: this.model.get("key"),
					data: {
						label: this.model.get("label"),
						type: this.model.get("type"),
						value: value
					}
				});

				// Disable the input field until a response is returned
				$(field).attr("disabled", "disabled");

				// Disable the button
				$(e.currentTarget).attr("disabled", "disabled");

				// Bind this view to the channel option
				channelOption.set({srcControlView: this.el});

				// Check if the channel is enabled and trigger a status update
				var channelView = this.options.channelView;
				channelView.addChannelOption(channelOption);
				
			}
		},

		// Toggle the "disabled" status of the button
		toggleButtonStatus: function(e) {
			if ($.trim(e.currentTarget).length > 0) {
				this.$("button.channel-button").removeAttr("disabled");
			} else {
				this.$("button.channel-button").attr("disabled", "disabled");
			}
		},

		render: function(eventName) {
			this.$el.append(this.template({label: this.model.get("label")}));
			this.$el.append(this.controlTemplate(this.model.toJSON()));
			this.$el.append(this.controlButton());

			// Disable any button
			this.$("button").attr("disabled", "disabled");

			return this;
		}
	});

	// Boostrap settings view and the channels listing
	window.riverSettingsView = new RiverSettingsView;
	window.ChannelsView = new ChannelsListView;
	ChannelsList.reset(<?php echo $channels_list; ?>);

})();
</script>