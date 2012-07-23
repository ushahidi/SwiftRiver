<hgroup class="user-title <?php if ($owner) { echo 'dashboard'; }  ?> cf">
	<div class="center">
		<div class="user-summary col_9">
			<a class="avatar-wrap" href="<?php echo URL::site().$account->account_path; ?>">
				<img src="<?php echo Swiftriver_Users::gravatar($account->user->email, 131); ?>" class="avatar"/>
			</a>
			<h1><?php echo $account->user->name; ?></h1>
			<h2 class="label"><?php echo $account->account_path; ?></h2>
		</div>
		<div id="follow_section" class="follow-summary col_3">
			<p class="follow-count">
				<a id="follower_count" href="<?php echo URL::site().$account->account_path.'/followers'; ?>">
					<strong><?php echo count($followers); ?></strong> <?php echo __("followers"); ?>
				</a>, 
				<a id="following_count" href="<?php echo URL::site().$account->account_path.'/following'; ?>">
					<strong><?php echo count($following); ?></strong> <?php echo __("following"); ?>
				</a>
			</p>
		</div>
	</div>
</hgroup>

<?php if ($owner AND ! empty($active)): ?>
<nav class="page-navigation cf">
	<ul class="center">
		<?php foreach ($nav as $item): ?>
		<li id="<?php echo $item['id']; ?>" class="<?php echo $item['id'] == $active ? 'active' : ''; ?>">
			<a href="<?php echo URL::site($account->account_path.$item['url']) ?>">
				<?php echo $item['label'];?>
			</a>
		</li>
		<?php endforeach; ?>
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
		<% if (following) { %>
			<p class="button-white follow has-icon selected">
				<a href="#" title="<?php echo __("Unfollow ".$full_name) ?>" 
				    data-title="<?php echo __("no longer following ".$full_name); ?>">
					<span class="icon"></span>
					<?php echo __("Following"); ?>
				</a>
			</p>
		<% } else { %>
			<p class="button-white follow has-icon">
				<a href="#" title="<?php echo __("Follow ".$full_name)?>" 
				    data-title="<?php echo __("now following ".$full_name); ?>">
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
					{following: this.get("following") ? 0 : 1}, 
					{
						wait: true, 
						success: function(model, response) {
							var count = parseInt($("#follower_count > strong").html());
							if (model.get("following") == 0) {
								count -= 1;
								$("#follower_count > strong").html(count);
							} else {
								count += 1;
								$("#follower_count > strong").html(count);
							}
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