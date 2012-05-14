<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1 class="<?php echo ($bucket->bucket_publish == 0) ? "private" : "public"; ?>">
				<?php $bucket_name = $bucket->bucket_name; ?>
				<?php if ($bucket->account->user->id == $user->id): ?>
					<span> <?php echo $bucket->bucket_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$bucket->account->account_path ?>">
						<?php $bucket_name = $bucket->account->account_path.'/'.$bucket_name; ?>
						<span><?php echo $bucket->account->account_path; ?></a> / <?php echo $bucket->bucket_name; ?></span>
				<?php endif; ?>
			</h1>
			<?php if ( ! empty($collaborators)): ?>
				<div class="rundown-people">
					<h2><?php echo __("ui.bucket.collaborators"); ?></h2>
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
					<?php echo __("ui.bucket.settings"); ?>
				</a>
			</h2>
			<h2 class="discussion">
				<a href="<?php echo $discussion_url; ?>">
					<span class="icon"></span>
					<?php echo __("ui.bucket.discussion"); ?>
				</a>
			</h2>
		</div>
		<?php elseif ( ! $anonymous): ?>
		<div class="follow-summary col_3">
			<p class="button-score button-white follow">
				<a href="#" title="now following">
					<span class="icon"></span>
					<?php echo __("ui.button.follow"); ?>
				</a>
			</p>
		</div>
		<?php endif; ?>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<ul class="center">
		<li id="list-navigation-link">
			<a onclick="appRouter.navigate('/list', {trigger: true}); return false;" href="#">
				<?php echo __("ui.nav.list"); ?>
			</a>
		</li>
		<li id="drops-navigation-link">
			<a onclick="appRouter.navigate('/drops', {trigger: true}); return false;" href="#">
				<?php echo __("ui.nav.drops"); ?>
			</a>
		</li>		
		<li id="photos-navigation-link">
			<a onclick="appRouter.navigate('/photos', {trigger: true}); return false;" href="#">
				<?php echo __("ui.nav.photos"); ?>
			</a>
		</li>
	</ul>
</nav>

<?php echo $droplets_view; ?>