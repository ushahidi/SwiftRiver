	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1 class="<?php echo ($river->river_public == 0) ? "private" : "public"; ?>">
					<!--span class="icon"></span-->
					<?php if ($river->account->user->id == $user->id OR $river->account->user->username == 'public'): ?>
						<span id="display_river_name"><?php echo $river->river_name; ?></span>
					<?php else: ?>
						<a href="<?php echo URL::site().$river->account->account_path ?>"><span><?php echo $river->account->account_path; ?></a> / <?php echo $river->river_name; ?></span>
					<?php endif; ?>
				</h1>
			</div>
			<?php if ($owner): ?>
			<div class="page-actions col_3">
				<h2 class="settings">
					<a href="<?php echo $settings_url; ?>">
						<span class="icon"></span>
						<?php echo __("River settings"); ?>
					</a>
				</h2>
			</div>
			<?php endif; ?>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li id="drops-navigation-link"><a onclick="appRouter.navigate('/drops', {trigger: true}); return false;" href="#"><?php echo __('Drops');?></a></li>
			<li id="list-navigation-link"><a onclick="appRouter.navigate('/list', {trigger: true}); return false;" href="#">List</a></li>
			<li><a href="#">Photos</a></li>
			<li><a href="#">Map</a></li>
			<li><a href="#">Timeline</a></li>
			<?php
			// SwiftRiver Plugin Hook -- Add River Nav Item
			Swiftriver_Event::run('swiftriver.river.nav', $river);
			?>
		</ul>
	</nav>

	
	<?php echo $droplets_view; ?>
