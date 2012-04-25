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
		<div class="follow-summary col_3">
			<p class="button-score button-white follow">
				<a href="#" title="now following">
					<span class="icon"></span>
					<?php echo __("Follow"); ?>
				</a>
			</p>
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

<?php echo $droplets_view; ?>