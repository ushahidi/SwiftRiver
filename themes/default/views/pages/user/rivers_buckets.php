<div class="col_12">
	<?php if ($owner): ?>
		<article class="container action-list base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo $owner_header ?></h1>
				</div>
			</header>
			<section id="owner_items" class="property-parameters">
			</section>
		</article>
		<article class="container action-list base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo $subscriber_header; ?></h1>
				</div>
			</header>
			<section id="subscribed_items" class="property-parameters">
			</section>
		</article>
	<?php else: ?>
		<article class="container action-list base">
			<header class="cf">
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
		<p class="follow-count"><strong><%= subscriber_count %></strong> <?php echo __("Followers"); ?></p>
		
		<?php
		/*
		<% if (subscribed) { %>
			<p id="unsubscribe_single"class="button-white follow selected">
				<a href="#" title="<?php echo __("unsubscribe"); ?>">
					<span class="icon"></span>
				</a>
			</p>
		<% } %>
		*/
		?>

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
					if (!model.get("subscribed"))
						$(target).fadeOut(); 
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
			// "click section.actions .delete-item .confirm": "delete"
		},
		
		initialize: function () {
			this.model.on('change', this.render, this);
			this.model.on('destroy', this.removeView, this);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleSubscription: function(e) {
			this.model.toggleSubscribe(this.$el);
		},
		
		delete: function() {
			this.model.destroy({wait: true});
		},
		
		removeView: function() {
			this.$el.fadeOut("slow");
		}
	});
		
		
	var ProfileView = Backbone.View.extend({
		
		initialize: function() {
			this.items = new RiverBucketItemList;
			this.items.on('add', this.addItem, this);
			this.items.on('reset', this.addItems, this);
			
		},
		
		addItem: function (item) {
			var view = new RiverBucketItemView({model: item}).render().el;
			<?php if ($owner): ?>
			if (item.get("subscribed") == true) {
				$("section#subscribed_items").append(view);
			} else {
				$("section#owner_items").append(view);
			}
			<?php else: ?>
				$("section#owner_items").append(view);
			<?php endif; ?>

			// Show the activity
			// if (typeof item.get("activity_data") != "undefined") {
			// 	activityData = item.get("activity_data");
			// 	view.$("span.activity-chart").sparkline(activityData, 
			// 		{type: 'bar', barColor: '#888', barWidth: 5});
			// }
		},
		
		addItems: function() {
			this.items.each(this.addItem, this);
		},
         
		toggleFollow: function() {
			userItem.toggleSubscribe();
		}

	});

	// Bootstrap
	var profile = new ProfileView;
	profile.items.url = "<?php echo $fetch_url ?>";
	profile.items.reset(<?php echo $list_items ?>);
});
</script>