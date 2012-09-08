<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_8">
			<h1 class="<?php echo ($bucket->bucket_publish == 0) ? "private" : "public"; ?>">
				<?php $bucket_name = $bucket->bucket_name; ?>
				<?php if ($bucket->account->user->id == $user->id): ?>
					<span><?php echo $bucket->bucket_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$bucket->account->account_path ?>">
						<?php $bucket_name = $bucket->account->account_path.'/'.$bucket_name; ?>
						<span><?php echo $bucket->account->account_path; ?></a> / <?php echo $bucket->bucket_name; ?></span>
				<?php endif; ?>
			</h1>		
		</div>
		<?php if ($owner): ?>
		<div class="page-action col_4">
			<span>
			<ul class="dual-buttons">
				<li class="button-blue"><a href="<?php echo $discussion_url; ?>"><i class="icon-comment"></i><?php echo __("Discussion"); ?></a></li>
				<li class="button-blue"><a href="<?php echo $settings_url; ?>"><i class="icon-settings"></i><?php echo __("Settings"); ?></a></li>
			</ul>
			</span>
		</div>		
		<?php elseif ( ! $anonymous AND ! $is_collaborator ): ?>
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
			<a href="drops">
				<?php echo __("Drops"); ?>
			</a>
		</li>
		<li id="list-navigation-link">
			<a href="list">
				<?php echo __("List"); ?>
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