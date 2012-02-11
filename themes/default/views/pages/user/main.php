<article id="droplet_full" class="<?php echo $template_type; ?> droplet dashboard cf">
	<div class="center page-title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1>
				<span class="edit-trigger" title="dashboard" id="edit_1" onclick="">
					<?php echo $user->name ?>
				</span>
			</h1>
		</hgroup>
		<section class="actions">
			<p class="button_change follow_user"><a class="subscribe"><span></span><strong>follow</strong></a></p>
		</section>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'user/'.$account_path;?>"><?php echo __('Rivers'); ?></a>
					</li>
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'user/'.$account_path.'/buckets';?>"><?php echo __('Buckets'); ?></a>
					</li>
				</ul>
			</nav>
			<div class="drawer"></div>
		</section>
		
		<div class="container list select data">
			<h2 class="null"><?php echo "No $active to display"; ?></h2>
		</div>
		
	</div>	
</article>

<script type="text/template" id="profile-row-item-template">
<div class="content">
	<h1><a href="<%= url %>" class="title"><%= name %></a></h1>
</div>
<div class="summary">
	<section class="actions">
	    <% if ( ! is_owner ) { %>
		    <% class_name = ""; %>
			<%if (subscribed) class_name = "active"; %>
		    <div class="button">
		    	<p class="button-change <%= class_name %>"><a class="subscribe" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Subscribe'); ?></span></a></p>
		    	<div class="clear"></div>
		    </div>
		<% } %>
	</section>
	<section class="meta">
	</section>
</div>
</script>

<script type="text/javascript">

// Start the app on jQuery .ready
$(function() {
	
	var RiverBucketItem = Backbone.Model.extend({
		
		toggleSubscribe: function() {
			this.save({subscribed: this.get("subscribed") ? 0 : 1});
		}
	});
	
	var RiverBucketItemList = Backbone.Collection.extend({
		model: RiverBucketItem,
		url: "<?php echo $fetch_url ?>"
	});
	
	var RiverBucketItemView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "item cf",
		
		template: _.template($("#profile-row-item-template").html()),
		
		events: {
			"click section.actions .button-change a.subscribe": "toggleSubscription"
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleSubscription: function() {
			this.model.toggleSubscribe();
		}
	});
	
	var Items = new RiverBucketItemList;
	var ProfileView = Backbone.View.extend({
		
		el: "#droplet_full",
		
		events: {
			"click section.actions .follow_user a.subscribe": "toggleFollow"
		},
		
		initialize: function() {
			Items.on('add',	 this.addItem, this);
			Items.on('reset', this.addItems, this);
			
		},
		
		addItem: function (item) {
			var view = new RiverBucketItemView({model: item});
			this.$(".data").append(view.render().el);
		},
		
		addItems: function() {
			Items.each(this.addItem, this);
			if (Items.length) {
				this.$(".data h2.null").hide();
			}
		},
         
		toggleFollow: function() {
			userItem.toggleSubscribe();
		}

	});
	
	// Bootstrap
	var profile = new ProfileView;
	Items.reset(<?php echo $list_items ?>);
	var userItem = new RiverBucketItem(<?php echo $user_item; ?>);
	userItem.urlRoot = "<?php echo $fetch_url ?>";
});

</script>