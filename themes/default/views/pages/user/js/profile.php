<script type="text/template" id="river_item_template">
	<% if (!is_owner) { %>	
		<% var selected = (subscribed) ? "selected" : ""; %>
		<% var title = (subscribed) ? "Unsubsribe" : "Subscribe"; %>
		<% var displayMessage = (subscribed)? "no longer following" : "now following"; %>
		<div class="actions">
			<p class="button-white follow only-icon has-icon <%= selected %>">
				<a href="#" title="<%= title %>" data-title="<%= displayMessage %>">
					<span class="icon"></span><span class="nodisplay"></span>
				</a>
			</p>
		</div>
	<% } %>
	
	<h2><a href="<%= river_url %>"><%= river_name %></a></h2>
</script>

<script type="text/template" id="bucket_item_template">
	<% if (!is_owner) { %>
		<% var selected = (subscribed) ? "selected" : ""; %>
		<% var title = (subscribed) ? "Unsubsribe" : "Subscribe"; %>
		<% var displayMessage = (subscribed)? "no longer following" : "now following"; %>
		<div class="actions">
			<p class="button-white follow only-icon has-icon <%= selected %>">
				<a href="#" title="<%= title %>" data-title="<%= displayMessage %>">
					<span class="icon"></span><span class="nodisplay"></span>
				</a>
			</p>
		</div>
	<% } %>

	<h2><a href="<%= bucket_url %>"><%= bucket_name %></a></h2>
</script>

<script type="text/javascript">
(function() {

	// Models
	var RiverItem = Backbone.Model.extend({
		toggleSubscribe: function(target, view) {
			this.save({subscribed: this.get("subscribed") ? 0 : 1}, {
				wait: true,
				success: function(model, response) {
					if ($(target).hasClass("selected")) {
						<?php if ($dashboard_view): ?>
							view.$el.fadeOut();
						<?php else: ?>
							$(target).removeClass("selected");
						<?php endif; ?>
					} else {
						$(target).addClass("selected");
					}
				}
			});
		}
	});

	var BucketItem = Backbone.Model.extend({
		toggleSubscribe: function(target, view) {
			this.save({subscribed: this.get("subscribed") ? 0 : 1}, {
				wait: true,
				success: function(model, response) {
					if ($(target).hasClass("selected")) {
						<?php  if ($dashboard_view): ?>
							view.$el.fadeOut();
						<?php else: ?>
							$(target).removeClass("selected");
						<?php endif; ?>
					} else {
						$(target).addClass("selected");
					}
				}
			});
		}
	});

	// Collections
	var RiverItemList = Backbone.Collection.extend({
		model: RiverItem
	});
	var BucketItemList = Backbone.Collection.extend({
		model: BucketItem
	});

	// River item view
	var RiverItemView = Backbone.View.extend({

		tagName: "div",
		
		className: "parameter",
		
		template: _.template($("#river_item_template").html()),

		initialize: function() {
			this.model.on("change", this.render, this);
		},

		events: {
			"click p.button-white > a": "toggleSubscription"
		},

		toggleSubscription: function(e) {
			var parentEl = $(e.currentTarget).parent("p");
			this.model.toggleSubscribe(parentEl, this);
		},

		render: function(event) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// Bucket item view
	var BucketItemView = Backbone.View.extend({

		tagName: "div",
		
		className: "parameter",
		
		template: _.template($("#bucket_item_template").html()),

		initialize: function() {
			this.model.on("change", this.render, this);
		},

		events: {
			"click p.button-white > a": "toggleSubscription"
		},

		toggleSubscription: function(e) {
			var parentEl = $(e.currentTarget).parent("p");
			this.model.toggleSubscribe(parentEl, this);
		},

		render: function(event) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}

	});

	var ProfileView = Backbone.View.extend({
		
		initialize: function() {
			this.rivers = new RiverItemList;
			this.buckets = new BucketItemList;

			this.rivers.on("reset", this.addRivers, this);
			this.rivers.on("add", this.addRiver, this);

			this.buckets.on("reset", this.addBuckets, this);
			this.buckets.on("add", this.addBucket, this);

			this.bucketViews = [];
			this.riverViews = [];

			// Regsiter events
			this.btnSubscribeRivers = $("p#subscribe_all_rivers > a");
			this.btnSubscribeRivers.on("click", this.riverViews, this.handleSubscription);

			this.btnSubscribeBuckets = $("p#subscribe_all_buckets > a");
			this.btnSubscribeBuckets.on("click", this.bucketViews, this.handleSubscription);
		},

		// Renders a single river
		addRiver: function(river) {
			var view = new RiverItemView({model: river});
			this.riverViews.push(view);

			<?php if ($dashboard_view): ?>
				pivotEl = $("p#subscribed_rivers");
				
				if (river.get("subscribed")) {
					pivotEl.after(view.render().el);
				} else {
					pivotEl.before(view.render().el);
				}

			<?php else: ?>
				$("section#river_listing").append(view.render().el);
			<?php endif; ?>
		},

		addRivers: function() {
			this.rivers.each(this.addRiver, this);
		},

		// Renders a single bucket
		addBucket: function(bucket) {
			var view = new BucketItemView({model: bucket});
			this.bucketViews.push(view);

			<?php if ($dashboard_view): ?>
				pivotEl = $("p#subscribed_buckets");
				
				if (bucket.get("subscribed")) {
					pivotEl.after(view.render().el);
				} else {
					pivotEl.before(view.render().el);
				}

			<?php else: ?>
				$("section#bucket_listing").append(view.render().el);
			<?php endif; ?>
		},

		addBuckets: function() {
			this.buckets.each(this.addBucket, this);
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

	// Bootstrap!
	var profile = new ProfileView;

	profile.rivers.url = "<?php echo $river_fetch_url; ?>";
	profile.buckets.url = "<?php echo $bucket_fetch_url; ?>";

	profile.rivers.reset(<?php echo $rivers_list; ?>);
	profile.buckets.reset(<?php echo $buckets_list; ?>);

})();
</script>