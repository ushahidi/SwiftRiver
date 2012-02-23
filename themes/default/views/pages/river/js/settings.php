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
			this.save({enabled: enabled}, {wait: true, success: function(model, response) { this.id = response.id; }});
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

			var radio = null;
			this.$("input[name='river_public']").each(function(){
				// Remove the checked property
				$(this).removeAttr("checked");

				if ($(this).val() == river.get("river_public")) {
					radio = this;
				}
			});
			$(radio).attr("checked", true);
		},

		events: {
			// When the river is renamed
			"click button#rename_river": "renameRiver",

			// When the privacy settings are changed
			"click input[name='river_public']": "toggleRiverPrivacy",

			// When delete river is confirmed
			"click section.actions .dropdown li.confirm a": "deleteRiver",
		},

		renameRiver: function(e) {
			// Get the name the river
			var riverName = $("input#river_name").val();
			if ($.trim(riverName).length > 0 && this.model.get("river_name") != riverName) {
				// Save the new river name
				this.model.save({name_only: true, river_name: riverName}, {
					wait: true,
					success: function(model, response) {
						// Change the river title
						$("#display_river_name").html(response.river_name);

						// HTML5 compatibility check
						if (typeof(window.history.pushState) != "undefined") {

							// Modify the address bar
							window.history.pushState(model, response.river_name, response.river_base_url);

							// Update the settings, filters and "more" links
							$("li.view-panel > a.settings").attr("href", response.settings_url);
							$("li.view-panel > a.filter").attr("href", response.filters_url);
							$("#river_more_url > a").attr("href", response.more_url);
						} else {
							// Reload the page
							window.location.href = response.river_base_url;
						}
					}
				});
			}
		},

		toggleRiverPrivacy: function(e) {
			var radio = $(e.currentTarget);
			if ($(radio).val() != this.model.get("river_public")) {

				// String for the new status of the river
				var newStatus = ($(radio).val() == 1)
				    ? "<?php echo __('public') ?>" 
				    : "<?php echo __('private'); ?>";

				// Show confirmation dialog before updating
				if (confirm("<?php echo __('Are you sure you want to make this river '); ?>" + newStatus + '?')) {

					// Update the privacy settings of the river
					this.model.save({privacy_only: true, river_public: $(radio).val()}, {
						wait: true, 
						success: function(model, response) {
							var targetEl = $("#display_river_name");

							targetEl.parent("h1").toggleClass("private").toggleClass("public");
							targetEl.css("display", "inline-block");
							targetEl.prev("span.icon").css("display", "inline-block");
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
				window.location.href = response.redirect_url
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
					var createdOption = new ChannelOption(response);
					createdOption.set({new: true});

					// Add the option to the UI
					channelView.optionsPanelView.addChannelOption(createdOption);

					// Clear the text field and remove any error CSS classes
					$(":text", controlView).val("");
					$(":text", controlView).removeClass("error");


					// Get the control view from the option and re-enable the text fields
					$("input", controlView).removeAttr("disabled");
					$("button", controlView).removeAttr("disabled");

				},
				error: function(model, response) {
					$(":text", controlView).addClass("error");
					
					// Get the control view from the option and re-enable the text fields
					$("input", controlView).removeAttr("disabled");
					$("button", controlView).removeAttr("disabled");
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
							channelView.$("a span.switch").toggleClass("switch-on").toggleClass("switch-off");

							// Save the channel option
							option.set({channel_filter_id: response.id});
							channelView.createAndSaveChannelOption(option);
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
		addChannelOptions: function(options) {
			this.channelOptions.each(this.addChannelOption, this);
			if (typeof options != "undefined") {
				var view = this;
				_.each(options, function(option) { view.addChannelOption(new ChannelOption(option)); }, this);
			}
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

		containerDiv: "<div class=\"channel-option-input\"></div>",

		// Template for the "Add button"
		controlButton: _.template($("#channel-option-control-button-template").html()),

		initialize: function() {

			this.inputType = this.model.get("type");

			if (this.inputType == "text" || this.inputType == "file" || this.inputType == "password") {
				// Input template
				this.controlTemplate = _.template($("#channel-option-input-template").html());
			} else if (this.model.get("type") == "select" || this.model.get("type") == "dropdown") {
				// Dropdown template
				this.controlTemplate = _.template($("#channel-option-dropdown-template")).html();
			}

			if (this.inputType == "file") {
				// Create a form to use for the file upload
				this.form = $("<form method=\"POST\" enctype=\"multipart/form-data\" action=\"<?php echo $channel_options_url; ?>\" target=\"upload_target\"></form>");

				// Create hidden input fields with the channel_filter_id and key
				this.form.append("<input type=\"hidden\" name=\"channel_filter_id\" value=\""+this.model.get("channel_filter_id")+"\">");
				this.form.append("<input type=\"hidden\" name=\"key\" value=\""+this.model.get("key")+"\">");

				// Append the form to the DOM
				this.form.append(this.containerDiv);

				this.$el.append(this.form);

				// Add the OMPL target <iframe> to the DOM
				window.ChannelView = this.options.channelView;
				this.$el.append($("<iframe id=\"upload_target\" name=\"upload_target\" style=\"width:0px;height:0px;border: none;\"></iframe>"));
			} else {
				this.$el.append(this.containerDiv);
			}

			this.container = this.$("div.channel-option-input");
		},

		events: {
			"click button.channel-button": "addChannelFilterOption",

			"keypress .channel-option-input :text": "toggleButtonStatus",

			"change .channel-option-input :text": "toggleButtonStatus",

			"change .channel-option-input :file": "toggleButtonStatus"
		},


		addChannelFilterOption: function(e) {
			if (this.inputType == "text") {
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
				
			} else if (this.inputType == "file") {

				// Check if the channel is enabled
				if (typeof this.model.get("channel_filter_id") == "undefined") {
					var channelView = this.options.channelView;
					var form = this.form;

					// Trigger channel enable/disable
					channelView.$("a span.switch").trigger("click");

					// A dirty hack! - Check for the newly created channel ID after 500ms
					// TODO: Review this implementation
					setTimeout(function() {
						// Set the channel filter id
						$("input[name='channel_filter_id']", form).val(channelView.model.get("id"));

						// Submit the form
						form.submit();
						$(":file", form).val("");
					}, 500);

				} else {
					// Initiate file upload
					this.form.submit();
					$(":file", this.form).val("");
				}

			}
		},

		// Toggle the "disabled" status of the button
		toggleButtonStatus: function(e) {
			if ($.trim($(e.currentTarget).val()).length > 0) {
				this.$("button.channel-button").removeAttr("disabled");
			} else {
				this.$("button.channel-button").attr("disabled", "disabled");
			}
		},

		render: function(eventName) {
			this.container.append(this.template({label: this.model.get("label")}));
			this.container.append(this.controlTemplate(this.model.toJSON()));
			this.container.append(this.controlButton({type: this.inputType}));

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