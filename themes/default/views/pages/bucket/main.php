<article class="list">
	<div class="cf center page-title">
		<hgroup>
			<h1 class="<?php echo ($bucket->bucket_publish == 0) ? "private" : "public"; ?>">
				<span class="icon"></span>
				<?php if ($bucket->account->user->id == $user->id): ?>
					<span><?php echo $bucket->bucket_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$bucket->account->account_path ?>"><span><?php echo $bucket->account->account_path; ?></a>/<?php echo $bucket->bucket_name; ?></span>
				<?php endif; ?>
			</h1>
		</hgroup>
	</div>
	
	<div class="center canvas">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li class="droplets active"><a><?php echo __('Drops');?></a></li>
					<?php
					// SwiftRiver Plugin Hook -- Add Bucket Nav Item
					Swiftriver_Event::run('swiftriver.bucket.nav', $bucket);
					?>
					<li class="view-panel"><a href="<?php echo $more; ?>"><span class="arrow"></span><?php echo __('Trends'); ?></a></li>
				</ul>
				<?php if ($owner): ?>
				<ul class="actions">
					<li class="view-panel">
						<a href="<?php echo $discussion_url; ?>" class="discussion">
							<span class="icon"></span>
							<span class="label"><?php echo __("Discuss"); ?></span>
						</a>
					</li>
					<li class="view-panel">
						<a href="<?php echo $settings_url; ?>" class="settings">
							<span class="icon"></span><?php echo __('Bucket Settings'); ?>
						</a>
					</li>
				</ul>
				<?php endif; ?>
			</nav>
			<div class="drawer"></div>
		</section>

		<div class="container stream">
			<?php echo $droplets_list; ?>
		</div>

	</div>
</article>	