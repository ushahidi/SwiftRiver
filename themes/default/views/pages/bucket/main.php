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
		<div class="page-actions col_3">
			<?php if ( ! $owner AND ! $anonymous AND ! $is_collaborator ): ?>
				<div class="follow-summary col_3" id="follow_button">
				</div>
				<?php echo $follow_button; ?>
			<?php endif;?>
			<?php if ($owner): ?>
				<h2 class="settings">
					<a href="<?php echo $settings_url; ?>">
						<span class="icon"></span>
						<?php echo __("Bucket settings"); ?>
					</a>
				</h2>
			<?php endif; ?>
			<h2 class="discussion">
				<a href="<?php echo $discussion_url; ?>">
					<span class="icon"></span>
					<?php echo __("Discussion"); ?>
				</a>
			</h2>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<ul class="center">
		<li id="list-navigation-link">
			<a href="list">
				<?php echo __("List"); ?>
			</a>
		</li>
		<li id="drops-navigation-link">
			<a href="drops">
				<?php echo __("Drops"); ?>
			</a>
		</li>		
		<li id="photos-navigation-link">
			<a href="photos">
				<?php echo __("Photos"); ?>
			</a>
		</li>
	</ul>
</nav>

<?php echo $droplets_view; ?>