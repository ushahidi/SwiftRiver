<script type="text/template" id="river_item_template">
	<?php if ($owner): ?>
		<% if (subscribed == 1) { %>
		<p class="button-white follow selected">
			<a href="#" title="">
				<span class="icon"></span><span class="nodisplay"></span>
			</a>
		<p>
		<% } %>
	<?php else: ?>
	<% var selected = (subscribed == 1) ? "selected" : ""; %>
	<p class="button-white follow <%= selected %>">
		<a href="#" title="">
			<span class="icon"></span><span class="nodisplay"></span>
		</a>
	<p>
	<?php endif; ?>
	<h2><a href="<?php echo URL::site(); ?><%= river_url %>"><%= river_name %></a></h2>
</script>

<script type="text/template" id="bucket_item_template">
	<?php if ($owner): ?>
		<% if (subscribed == 1) { %>
		<p class="button-white follow selected">
			<a href="#" title="">
				<span class="icon"></span><span class="nodisplay"></span>
			</a>
		<p>
		<% } %>
	<?php else: ?>
		<% var selected = (subscribed == 1) ? "selected": ""; %>
		<p class="button-white follow <%= selected %>">
			<a href="#" title="">
				<span class="icon"></span><span class="nodisplay"></span>
			</a>
		<p>
	<?php endif; ?>

	<h2><a href="<?php echo URL::site(); ?><%= bucket_url %>"><%= bucket_name %></a></h2>
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
							view.$el.hide();
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
							view.$el.hide();
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
			var parentEl = $(e.currentTarget).parent();
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
			var parentEl = $(e.currentTarget).parent();
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
		},

		// Renders a single river
		addRiver: function(river) {
			view = new RiverItemView({model: river}).render().el;

			<?php if ($dashboard_view): ?>
				pivotEl = $("p#subscribed_rivers");
				
				if (river.get("subscribed") == 1) {
					pivotEl.after(view);
				} else {
					pivotEl.before(view);
				}

			<?php else: ?>
				$("section#river_listing").append(view);
			<?php endif; ?>
		},

		addRivers: function() {
			this.rivers.each(this.addRiver, this);
		},

		// Renders a single bucket
		addBucket: function(bucket) {
			view = new BucketItemView({model: bucket}).render().el;
			<?php if ($dashboard_view): ?>
				pivotEl = $("p#subscribed_buckets");

				if (bucket.get("subscribed") == 1) {
					pivotEl.after(view);
				} else {
					pivotEl.before(view);
				}

			<?php else: ?>
				$("section#bucket_listing").append(view);
			<?php endif; ?>
		},

		addBuckets: function() {
			this.buckets.each(this.addBucket, this);
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