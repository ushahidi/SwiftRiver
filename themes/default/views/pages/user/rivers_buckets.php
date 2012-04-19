<div class="col_12">
	<?php if ($owner): ?>
		<article class="container action-list base">
			<div id="owner_alert" class="alert-message blue">
				<p>
					<strong><?php echo __("No :item_type", array(":item_type" => $item_type)); ?></strong>
					<?php echo __("You have not created any :item_type", array(":item_type" => $item_type)); ?>
				</p>
			</div>

			<header id="owner_header" class="cf">
				<div class="property-title">
					<h1><?php echo $owner_header ?></h1>
				</div>
			</header>
			<section id="owner_items" class="property-parameters">
			</section>
		</article>

		<article class="container action-list base">
			<div id="subscriber_alert" class="alert-message blue">
				<p>
					<strong><?php echo __("No subscriptions"); ?></strong>
					<?php echo __("You have not subscribed to any :item_type", array(":item_type" => $item_type)); ?>
				</p>
			</div>

			<header id="subscriber_header" class="cf">
				<div class="property-title">
					<h1><?php echo $subscriber_header; ?></h1>
				</div>
			</header>
			<section id="subscribed_items" class="property-parameters">
			</section>
		</article>
	<?php else: ?>
		<article class="container action-list base">
			<div id="owner_alert" class="alert-message blue">
				<p>
					<strong><?php echo __("No :item_type", array(":item_type" => $item_type)); ?></strong>
					<?php echo __(":item_owner does not have any :item_type", 
					array(":item_owner" => $item_owner, ":item_type" => $item_type)); ?>
				</p>
			</div>
			<header id="owner_header" class="cf">
				<div class="property-title">
					<h1><?php echo $owner_header; ?></h1>
					<p id="subscribe_all" class="button-white add-parameter follow">
						<a href="#" title="<?php echo __("Subscribe"); ?>">
							<span class="icon"></span><?php echo __("Subscribe to all"); ?>
						</a>
					</p>
				</div>
			</header>
			<section id="owner_items" class="property-parameters">
			</section>
		</article>
	<?php endif; ?>
</div>

<script type="text/template" id="river-bucket-item-template">
	<div class="actions">
		<% var subscriber_label = (subscriber_count == 1) 
		       ? "<?php echo __("follower"); ?>" 
		       : "<?php echo __("followers"); ?>"; 
		%>
		<p class="follow-count"><strong><%= subscriber_count %></strong> <%= subscriber_label %></p>

		<% if (is_owner) { %>
			<p id="delete_item" class="remove-small">
				<a href="#">
					<span class="icon"></span>
					<span class="nodisplay"><?php echo __("Delete"); ?></span>
				</a>
			</p>
		<% } else { %>
			<% if (subscribed) { %>
				<p id="unsubscribe_single" class="button-white follow only-icon has-icon selected">
					<a href="#" title="<?php echo __("Unsubscribe"); ?>">
						<span class="icon"></span>
					</a>
				</p>
			<% } else { %>
				<p id="unsubscribe_single" class="button-white follow only-icon has-icon">
					<a href="#" title="<?php echo __("Subscribe"); ?>">
						<span class="icon"></span>
					</a>
				</p>
			<% } %>
		<% } %>

	</div>
	<h2><a href="<%= item_url %>"><%= item_name %></a></h2>
</script>

<script type="text/javascript">
$(function() {
	var RiverBucketItem = Backbone.Model.extend({
		
		toggleSubscribe: function(target) {
			this.save(
			{
				subscribed: this.get("subscribed") ? 0 : 1,
				subscriber_count: this.get("subscribed") 
				    ? parseInt(this.get("subscriber_count")) - 1 
				    : parseInt(this.get("subscriber_count")) + 1,
			},
			{
				wait: true, 
				success: function(model, response) { 
					<?php if ($owner): ?>
					if (!model.get("subscribed"))
						$(target).fadeOut();
					<?php endif; ?>
				}
			});
		}
	});
	
	var RiverBucketItemList = Backbone.Collection.extend({
		model: RiverBucketItem,
	});
	
	var RiverBucketItemView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter",
		
		template: _.template($("#river-bucket-item-template").html()),
		
		events: {
			"click #unsubscribe_single > a": "toggleSubscription",
			"click #delete_item > a": "delete"
		},
		
		initialize: function () {
			this.model.on('change', this.render, this);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleSubscription: function(e) {
			this.model.toggleSubscribe(this.$el);
		},
		
		delete: function(e) {
			targetEl = this.$el;
			this.model.destroy({
				wait: true, 
				success: function(model, response) { 
					$(targetEl).fadeOut();
				}
			});

			e.stopPropagation();
		}
		
	});
		
		
	var ProfileView = Backbone.View.extend({
		
		initialize: function() {
			this.items = new RiverBucketItemList;
			this.items.on('add', this.addItem, this);
			this.items.on('reset', this.addItems, this);
			this.itemViews = [];
			this.btnSubscribeAll = $("p#subscribe_all > a");
			this.btnSubscribeAll.on("click", this.itemViews, this.handleSubscription);

			// DOM references - to be used for toggling visibility
			this.subscriberHeader = $("header#subscriber_header");
			this.subscriberListing = $("section#subscribed_items");
			this.subscriberAlert = $("#subscriber_alert");

			this.subscriberHeader.hide();
			this.subscriberListing.hide();
		},
		
		addItem: function (item) {
			var view = new RiverBucketItemView({model: item});
			this.itemViews.push(view);

			<?php if ($owner): ?>

			if (item.get("subscribed")) {
				
				// Unhide
				if (this.subscriberHeader.css("display") == "none") {
					this.subscriberHeader.show();
					this.subscriberListing.show();
					this.subscriberAlert.hide();
				}
				$("section#subscribed_items").append(view.render().el);
			} else {
				$("section#owner_items").append(view.render().el);
			}

			<?php else: ?>
				$("section#owner_items").append(view.render().el);
			<?php endif; ?>

			// Show the activity
			// if (typeof item.get("activity_data") != "undefined") {
			// 	activityData = item.get("activity_data");
			// 	view.$("span.activity-chart").sparkline(activityData, 
			// 		{type: 'bar', barColor: '#888', barWidth: 5});
			// }
		},
		
		addItems: function() {
			if (this.items.length > 0) {
				$("#owner_alert").hide();
				this.items.each(this.addItem, this);
			} else {
				// Hide the ownership section
				$("header#owner_header").hide();
				$("section#owner_items").hide();
			}
		},
         
		toggleFollow: function() {
			userItem.toggleSubscribe();
		},

		/**
		 * Event handler for the "subscribe to all" action
		 */
		handleSubscription: function(e) {
			views = e.data;
			z = views.length;
			for (var i=0; i<z; i++) {
				if (!views[i].model.get("subscribed") && !views[i].model.get("is_owner")) {
					$("p.button-white > a", views[i].$el).trigger("click");
				}
			}
		}


	});

	// Bootstrap
	var profile = new ProfileView;
	profile.items.url = "<?php echo $fetch_url ?>";
	profile.items.reset(<?php echo $list_items ?>);
});
</script>