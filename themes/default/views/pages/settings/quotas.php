<?php echo Form::open(); ?> 
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="">
	<div class="settings-toolbar"></div>
<?php echo Form::close(); ?>

<script type="text/template" id="quota-template">
	<header class="cf">
		<div class="property-title">
			<h1><%= channel_name %></h1>
		</div>
	</header>
	<section class="property-parameters">
	</section>
</script>

<script type="text/template" id="quota-item-template">
	<label for="<%= channel_option %>">
		<p class="field"><?php echo __("No. of "); ?><%= label %>s</p>
		<input type="text" name="<%= channel_option %>" value="<%= quota %>">
		<p class="actions" style="display:none;">
			<span class="button-blue"><a class="cancel" href="#"><?php echo __("Cancel"); ?></a></span>
		</p>
		<p class="actions" style="display:none;">
			<span class="button-blue"><a class="save" href="#"><?php echo __("Save"); ?></a></span>
		</p>
	</label>
</script>

<script type="text/javascript">
$(function(){

	var postURL = "<?php echo $post_url; ?>";

	var Channel = Backbone.Model.extend();
	var ChannelList = Backbone.Collection.extend({
		model: Channel
	});

	var ChannelQuota = Backbone.Model.extend({
		url: postURL
	});

	var channelList = new ChannelList();

	// View for a single channel and the quotas for each of its options
	var ChannelQuotaView = Backbone.View.extend({

		tagName: "article",

		className: "container base",

		template: _.template($("#quota-template").html()),

		addChannelQuotaItem: function(quotaItem) {
			var view = new ChannelQuotaItemView({model: quotaItem});
			this.$("section.property-parameters").prepend(view.render().el);
		},

		render: function() {
			this.$el.html(this.template({channel_name: this.model.get("channel_name")}));
			_.each(this.model.get("quota_options"), function(option){
				var quotaItem = new ChannelQuota(option);
				this.addChannelQuotaItem(quotaItem);
			}, this);
			return this;
		}
	});

	// View a single channel option quota
	var ChannelQuotaItemView = Backbone.View.extend({

		tagName: "div",

		className: "parameter",

		template: _.template($("#quota-item-template").html()),

		events: {
			// When the save button is clicked
			"click a.save": "save",

			// When the cancel button is clicked
			"click a.cancel": "cancel",

			// When the textbox contents change
			"keyup input": "toggleEditButtons",

			"focusout input": "toggleEditButtons",
		},

		// When the save button is clicked
		save: function(e) {
			this.hideButtons();
			// Clear any error messages
			this.$(".error-message").remove();
			
			var value = this.$("input").val();

			this.$("input").attr("readonly", true);
			var postData = {quota: value};

			if (this.model.get("id") === null) {
				postData.channel = this.model.get("channel");
				postData.channel_option = this.model.get("channel_option");
			}

			var loading_msg = window.loading_image.clone();
			var context = this;
			this.$("input").after(loading_msg);

			// Show loading icon
			this.model.save(postData, {
				wait: true,

				// Successful saving
				success: function(model, response) {
					var successMsg = $('<span class="success-message">Saved</span>');
					loading_msg.replaceWith(successMsg);
					successMsg.fadeOut(1500, function(){ context.$("input").removeAttr("readonly"); });					
				},
				// When an error is returned
				error: function(model, response) {
					var message = "Oops, unable to save. Try again.";
					if (response.status == 400) {
						message = JSON.parse(response.responseText)["error"];
					} 
					var error_msg = $('<span class="error-message">' + message + '</span>');
					loading_msg.replaceWith(error_msg).remove();
					context.$("input").removeAttr("readonly");
				}
			});
			return false;
		},

		// Handles click events for the cancel button
		cancel: function(e) {
			// Restore original value
			this.$("input").val(this.model.get("quota"));

			// Hide the action buttons
			this.hideButtons();
			return false;
		},

		hideButtons: function() {
			this.$("p.actions").fadeOut("slow");
		},

		toggleEditButtons: function(e) {
			var inputVal = $(e.currentTarget).val();
			if (this.model.get("quota") !== $.trim(inputVal)) {
				this.$("p.actions").fadeIn("slow");
			} else {
				this.$("p.actions").fadeOut("slow");
			}
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// Initialize the quotas view
	var QuotasView = Backbone.View.extend({

		initialize: function() {
			channelList.on("add", this.addChannel, this);
			channelList.on("reset", this.addChannels, this);
		},

		addChannels: function() {
			channelList.each(this.addChannel, this);
		},

		addChannel: function(channel) {
			var view = new ChannelQuotaView({model: channel});
			$("div.settings-toolbar").after(view.render().el);
		}
	});

	var quotasView = new QuotasView();
	channelList.reset(<?php echo $quotas; ?>);
});
</script>