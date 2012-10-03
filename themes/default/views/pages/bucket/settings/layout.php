<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php echo $bucket->bucket_name; ?> <em><?php echo __("Settings"); ?></em></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="back">
				<a href="<?php echo $bucket_base_url; ?>">
					<span class="icon"></span>
					<?php echo __("Return to bucket"); ?>
				</a>
			</h2>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<div id="page-views" class="settings touchcarousel col_12">
			<ul class="touchcarousel-container">
				<li class="touchcarousel-item <?php if ($active == 'collaborators' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $bucket_base_url.'/settings/collaborators'; ?>"><?php echo __("Collaborators"); ?></a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'display' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $bucket_base_url.'/settings/display'; ?>"><?php echo __("Display"); ?></a>
				</li>
				<?php
					// Add bucket settings nav item
					$event_params = array($bucket_base_url, $active);
					Swiftriver_Event::run("swiftriver.bucket.settings.nav", $event_params);
				?>
			</ul>
		</div>
	</div>
</nav>

<?php echo $settings_content; ?>