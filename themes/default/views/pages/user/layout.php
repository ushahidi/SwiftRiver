<article class="<?php echo $template_type; ?>">
	<div class="center page-title cf">
		<hgroup class="user">
			<img src="<?php echo Swiftriver_Users::gravatar($account->user->email, 80); ?>" />
			<h1>
				<span>
					<?php echo $account->user->name; ?>
				</span>
			</h1>
		</hgroup>
		<?php if ( ! $owner and ! $anonymous ): ?>
			<section class="actions" id="follow-button">
			</section>
		<?php endif; ?>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().$account->account_path;?>"><?php echo __('Activity'); ?></a>
					</li>
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().$account->account_path.'/rivers';?>"><?php echo __('Rivers'); ?></a>
					</li>
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().$account->account_path.'/buckets';?>"><?php echo __('Buckets'); ?></a>
					</li>
				</ul>
				<?php if ($owner): ?>
				<ul class="actions">
					<li class="view-panel">
						<a href="<?php echo URL::site().$account->account_path.'/settings';?>" class="settings">
							<span class="icon"></span><span class="label"><?php echo __('Account settings'); ?></span>
						</a>
					</li>
				</ul>
				<?php endif; ?>
			</nav>
			<div class="drawer"></div>
		</section>
		
		<?php echo $sub_content; ?>
		
	</div>	
</article>

<script type="text/template" id="user-item-template">
	<% if (subscribed) { %>
		<p class="button-change follow-user"><a class="subscribe"><span class="icon"></span><span class="label">Unfollow <?php echo $account->user->name ?></span></a></p>
	<% } else { %>
		<p class="button-change follow-user"><a class="subscribe"><span class="icon"></span><span class="label">Follow <?php echo $account->user->name ?></span></a></p>
	<% } %>
	<div class="clear"></div>
</script>

<script type="text/javascript">
// Bootstrap the follow button
$(function() {
	var UserItem = Backbone.Model.extend({
		
		toggleFollow: function() {
			this.save({
				subscribed: this.get("subscribed") ? 0 : 1
			});
		}
	});
		
	var UserItemView = Backbone.View.extend({
		tagName: "div",
		
		className: "button",
		
		template: _.template($("#user-item-template").html()),
		
		events: {
			"click section.actions .button-change a.subscribe": "toggleFollow",
		},
		
		initialize: function () {
			this.model.on('change', this.render, this);
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		toggleFollow: function() {
			this.model.toggleFollow();
		}
	});
	
	var userItem = new UserItem(<?php echo $user_item; ?>);
	userItem.url = "<?php echo $fetch_url ?>";
	var userItemView = new UserItemView({model: userItem});
	$("#follow-button").html(userItemView.render().el);
});
</script>