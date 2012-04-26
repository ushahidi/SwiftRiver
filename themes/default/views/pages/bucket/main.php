<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php print $page_title; ?></h1>
			<?php if ( ! empty($collaborators)): ?>
			<div class="rundown-people">
				<h2><?php echo __("Collaborators on this bucket"); ?></h2>
				<ul>
					<?php foreach ($collaborators as $collaborator): ?>
						<li>
							<a href="<?php echo URL::site().$collaborator['account_path'] ?>" 
								class="avatar-wrap" title="<?php echo $collaborator['name']; ?>">
								<img src="<?php echo $collaborator['avatar']; ?>" />
							</a>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
			<?php endif; ?>			
		</div>

		<?php if ($owner): ?>
		<div class="page-actions col_3">
			<h2 class="settings">
				<a href="<?php echo $settings_url; ?>">
					<span class="icon"></span>
					<?php echo __("Bucket settings"); ?>
				</a>
			</h2>
			<h2 class="discussion">
				<a href="<?php echo $discussion_url; ?>">
					<span class="icon"></span>
					<?php echo __("Discussion"); ?>
				</a>
			</h2>
		</div>
		<?php else: ?>
		<div class="follow-summary col_3" id="section_follow_bucket">
		</div>
		<?php endif; ?>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<ul class="center">
		<li id="drops-navigation-link">
			<a onclick="appRouter.navigate('/drops', {trigger: true}); return false;" href="#">
				<?php echo __("Drops"); ?>
			</a>
		</li>
		<li id="list-navigation-link">
			<a onclick="appRouter.navigate('/list', {trigger: true}); return false;" href="#">
				<?php echo __("List"); ?>
			</a>
		</li>
	</ul>
</nav>


<?php if ( ! $owner): ?>
	<script type="text/template" id="bucket-item-template">
		<% if (subscribed) { %>
			<p class="button-white follow has-icon selected">
				<a href="#" title="<?php echo __("Unfollow ".$bucket_name); ?>" 
				    data-title="<?php echo __("no longer following the ".$bucket_name." bucket"); ?>">
					<span class="icon"></span>
					<?php echo __("Following"); ?>
				</a>
			</p>
		<% } else { %>
			<p class="button-white follow has-icon">
				<a href="#" title="<?php echo __("Follow ".$bucket_name); ?>" 
				    data-title="<?php echo __("now following the ".$bucket_name." bucket"); ?>">
					<span class="icon"></span>
					<?php echo __("Follow"); ?>
				</a>
			</p>
		<% } %>
	</script>

	<script type="text/javascript">
	/**
	 * Backbone JS wiring for the "Follow" button
	 */
	$(function() {
		// Model for the current bucket
		var BucketItem = Backbone.Model.extend({
			toggleSubscription: function(target) {
				// Save
				this.save({subscribed: this.get("subscribed") ? 0 : 1},
					{
						wait: true,
						success: function(model, response) {
							$(target).remove();
						}
					}
				);
			}
		});

		// View for the BucketItem model
		var BucketItemView = Backbone.View.extend({
			el: "div#section_follow_bucket",

			template: _.template($("#bucket-item-template").html()),

			initialize: function() {
				this.model.on('change', this.render, this);
			},
			events: {
				'click p.button-white > a': 'toggleSubscription'
			},

			// Event handler for follow/unfollow actions
			toggleSubscription: function(e) {
				this.model.toggleSubscription($(e.currentTarget).parent());
			},

			render: function() {
				this.$el.append(this.template(this.model.toJSON()));
				return this;
			}
		});

		// Bootstrap the follow button
		var bucketItem = new BucketItem(<?php echo $bucket_item; ?>);
		bucketItem.url = "<?php echo $action_url; ?>";
		var bucketItemView = new BucketItemView({model: bucketItem});
		bucketItemView.render();
	});
	</script>

<?php endif; ?>

<?php echo $droplets_view; ?>