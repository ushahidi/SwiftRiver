<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><a href="<?php echo $river_base_url; ?>"><?php echo $river->river_name; ?></a> <em><?php echo __("settings"); ?></em></h1>
		</div>
		<div class="page-action col_3">
			<span class="button-white"><a href="<?php echo $river_base_url; ?>">Return to river</a></span>
		</div>		
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<div id="page-views" class="settings touchcarousel col_12">
			<ul class="touchcarousel-container">
				<li class="touchcarousel-item <?php if ($active == 'channels' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/settings/channels'; ?>">Flow</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'collaborators' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/settings/collaborators'; ?>">Collaborators</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'display' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/settings/display'; ?>">Options</a>
				</li>
				<?php
					// Swiftriver Plugin Hook -- add river settings nav item
					Swiftriver_Event::run('swiftriver.river.settings.nav', $active);
				?>
			</ul>
		</div>
	</div>
</nav>

<?php echo $settings_content; ?>