<div class="container list select data" id="river-bucket-list">
	<h2 class="null"><?php echo "No $active to display"; ?></h2>
</div>

<script type="text/template" id="profile-row-item-template">
<div class="content">
	<h1>
		<% if (is_other_account) { %>
			<a href="<%= item_owner_url %>"><%= account_path %>/</a>
		<% } %>

		<a href="<%= item_url %>" class="title"><%= item_name %></a>
	</h1>
</div>
<div class="summary">
	<section class="actions">
	    <% if ( ! is_owner ) { %>
		    <% class_name = ""; %>			
		    <div class="button">
		    	<p class="button-change">
					<a class="subscribe" onclick=""><span class="icon"></span>
					<% if (subscribed) { %>
						<span class="label"><?php echo __('Unsubscribe'); ?></span></a></p>
					<% } else { %>
						<span class="label"><?php echo __('Subscribe'); ?></span></a></p>
					<% } %>
		    	<div class="clear"></div>
		    </div>
		<% } else { %>
			<div class="button delete-item">
				<p class="button-change">
					<a class="delete">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __('Delete '.ucfirst($active)); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container">
					<p><?php echo __('Are you sure you want to delete this '.$active.'?'); ?></p>
					<ul>
						<li class="confirm">
							<a><?php echo __('Yep.'); ?></a>
						</li>
						<li class="cancel"><a><?php echo __('No, nevermind.'); ?></a></li>
					</ul>
				</div>
			</div>
		<% } %>
	</section>
	<section class="meta">
		<p>
			<a><strong><%= subscriber_count %></strong> <?php echo __('Subscribers'); ?></a>
			<a><strong><%= drop_count %></strong> <?php echo __('Drops'); ?></a>
		</p>
	</section>
</div>
</script>

<script type="text/javascript">
$(function() {
	var RiverBucketItem = Backbone.Model.extend({
		
		toggleSubscribe: function() {
			this.save({
				subscribed: this.get("subscribed") ? 0 : 1,
				subscriber_count: this.get("subscribed") ? parseInt(this.get("subscriber_count")) - 1 : parseInt(this.get("subscriber_count")) + 1,
			});
		}
	});
	
	var RiverBucketItemList = Backbone.Collection.extend({
		model: RiverBucketItem,
	});
	
	var RiverBucketItemView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "item cf",
		
		template: _.template($("#profile-row-item-template").html()),
		
		events: {
			"click section.actions .button-change a.subscribe": "toggleSubscription",
			"click section.actions .delete-item .confirm": "delete"
		},
		
		initialize: function () {
			this.model.on('change', this.render, this);
			this.model.on('destroy', this.removeView, this);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleSubscription: function() {
			this.model.toggleSubscribe();
		},
		
		delete: function() {
			this.model.destroy({wait: true});
		},
		
		removeView: function() {
			this.$el.fadeOut("slow");
		}
	});
		
		
	var ProfileView = Backbone.View.extend({
		
		el: "#river-bucket-list",
		
		events: {
			"click section.actions .follow-user a.subscribe": "toggleFollow"
		},
		
		initialize: function() {
			this.items = new RiverBucketItemList;
			this.items.on('add',	 this.addItem, this);
			this.items.on('reset', this.addItems, this);
			
		},
		
		addItem: function (item) {
			var view = new RiverBucketItemView({model: item});
			this.$el.append(view.render().el);
		},
		
		addItems: function() {
			this.items.each(this.addItem, this);
			if (this.items.length) {
				this.$("h2.null").hide();
			}
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