<div class="col_12" id="followers_container">
	<article class="container action-list base">
		<?php if ($owner): ?>
		<div class="alert-message blue" style="display: none">
			<p>
			<?php if ($following_mode): ?>
				<strong><?php echo __("Your following"); ?></strong>
				<?php echo __("You are not following anyone"); ?>
			<?php else: ?>
				<strong><?php echo __("No followers"); ?></strong>
				<?php echo __("You do have any followers"); ?>
			<?php endif; ?>
			</p>
		</div>
		<?php else: ?>
			<div class="alert-message blue" style="display: none;">
				<p>
				<?php if ($following_mode): ?>
					<strong><?php echo __(":account_owner's following", array(':account_owner' => $account_owner)); ?></strong>
					<?php echo __(":account_owner is not following anyone", 
					array(":account_owner" => $account_owner)); ?>
				<?php else: ?>
					<strong><?php echo __("No followers"); ?></strong>
					<?php echo __(":account_owner does not have any followers", 
					array(":account_owner" => $account_owner)); ?>
				<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<header class="cf">
			<div class="property-title">
				<h1><?php echo $header_title ?></h1>
			</div>
		</header>
		<section id="follower_list" class="property-parameters">
		</section>
	</article>
</div>

<script type="text/template" id="follower-template">
	<% var currentUserID = "<?php echo $user->id; ?>" %>
	
	<% if (currentUserID != id) { %>
	<div class="actions">
		<% if (subscribed) { %>
			<p class="button-white follow only-icon has-icon selected">
				<a href="#" title="">
					<span class="icon"></span>
				</a>
			</p>
		<% } else { %>
			<p class="button-white follow only-icon has-icon">
				<a href="#" title="">
					<span class="icon"></span>
				</a>
			</p>
		<% } %>
	</div>
	<% } %>

	<a class="avatar-wrap"><img src="<%= user_avatar %>" class="avatar"/></a>
	<h2><a href="<%= user_url %>"><%= user_name %></a></h2>
	<p><%= account_path %></p>
</script>

<script type="text/javascript">
	var Follower = Backbone.Model.extend({
		
		toggleFollow: function(target) {
			this.save(
				{subscribed: !this.get("subscribed")}
				<?php if ($following_mode): ?>
				,
				{
					wait: true, 
					success: function(model, response){
						$(target).fadeOut();
					}
				}
				<?php endif; ?>
			);
		}
	});
	
	var FollowersList = Backbone.Collection.extend({
		model: Follower,
	});
	
	// View for a single follower
	var FollowerItemView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter",
		
		template: _.template($("#follower-template").html()),
		
		events: {
			"click .button-white > a": "toggleFollow",
		},
		
		initialize: function () {
			this.model.on('change', this.render, this);
			this.model.on('destroy', this.removeView, this);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleFollow: function(e) {
			this.model.toggleFollow(this.$el);
			e.stopPropagation();
		}
		
	});
		
	// Followers view
	var FollowersView = Backbone.View.extend({
		
		el: $("#followers_container"),

		initialize: function() {
			this.followers = new FollowersList;
			this.followers.on('add', this.addItem, this);
			this.followers.on('reset', this.addItems, this);
		},
		
		addItem: function (item) {
			var view = new FollowerItemView({model: item});
			$("#follower_list").append(view.render().el);
		},
		
		addItems: function() {
			if (this.followers.length > 0) {
				this.followers.each(this.addItem, this);
			} else {
				// Hide the header and show the alert message
				this.$("header").hide();
				$("#follower_list").hide();
				this.$(".alert-message").show();
			}
		}
         
	});

	// Bootstrap
	var listView = new FollowersView;
	listView.followers.url = "<?php echo $fetch_url ?>";
	listView.followers.reset(<?php echo $follower_list ?>);
</script>