<hgroup class="user-title dashboard cf">
	<div class="center">
		<div class="user-summary col_9">
			<a class="avatar-wrap" href="<?php echo URL::site().$account->account_path; ?>">
				<img src="<?php echo Swiftriver_Users::gravatar($account->user->email, 156); ?>" />
			</a>
			<h1><?php echo $account->user->name; ?></h1>
			<h2><?php echo $account->user->username; ?></h2>
		</div>
		<div id="follow_section" class="follow-summary col_3">
			<p class="follow-count">
				<a href="#"><strong><?php echo count($followers); ?></strong> <?php echo __("following"); ?></a>, 
				<a href="#"><strong><?php echo count($following); ?></strong> <?php echo __("following"); ?></a>
			</p>
		</div>
	</div>
</hgroup>

<?php if ($owner AND ! empty($active)): ?>
<nav class="page-navigation cf">
	<ul class="center">
		<li <?php if ($active == 'main') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().$account->account_path; ?>"><?php echo __("Dashboard"); ?></a>
		</li>
		<li <?php if ($active == 'settings') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().$account->account_path.'/settings'; ?>"><?php echo __("Account Settings"); ?></a>
		</li>

	</ul>
</nav>
<?php endif; ?>

<div id="content" class="user <?php echo $view_type ;?> cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>


<?php if ( ! $owner AND ! $anonymous): ?>
	<?php $full_name = $account->user->name; ?>
	<script type="text/template" id="user-item-template">
		<% if (subscribed) { %>
			<p class="button-white follow selected">
				<a href="#" title="<?php echo __("Unfollow ".$full_name); ?>">
					<span class="icon"></span>
					<?php echo __("Following"); ?>
				</a>
			</p>
		<% } else { %>
			<p class="button-white follow">
				<a href="#" title="<?php echo __("Follow ".$full_name); ?>">
					<span class="icon"></span>
					<?php echo __("Follow"); ?>
				</a>
			</p>
		<% } %>
	</script>

	<script type="text/javascript">
	// Bootstrap the follow button
	$(function() {
		var UserItem = Backbone.Model.extend({
			
			toggleFollow: function(target) {
				this.save(
					{subscribed: this.get("subscribed") ? 0 : 1}, 
					{
						wait: true, 
						success: function(model, response) {
							$(target).remove();
						}
					});
			}
		});
			
		var UserItemView = Backbone.View.extend({

			el: "div#follow_section",

			template: _.template($("#user-item-template").html()),
			
			events: {
				"click p.button-white > a": "toggleFollow",
			},
			
			initialize: function () {
				this.model.on('change', this.render, this);
			},
			
			render: function() {
				this.$el.append(this.template(this.model.toJSON()));
			},
			
			toggleFollow: function(e) {
				var target = $(e.currentTarget).parent();
				this.model.toggleFollow(target);
			}
		});
		
		var userItem = new UserItem(<?php echo $user_item; ?>);
		userItem.url = "<?php echo $fetch_url ?>";
		var userItemView = new UserItemView({model: userItem});
		userItemView.render();
	});
	</script>

<?php endif; ?>