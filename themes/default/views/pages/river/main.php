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
			<li class="active"><a href="/river"><?php echo __('Drops');?></a></li>
			<li><a href="/river/view-list.php">List</a></li>
			<?php
			// SwiftRiver Plugin Hook -- Add River Nav Item
			Swiftriver_Event::run('swiftriver.river.nav', $river);
			?>
		</ul>
	</nav>

	<div id="content" class="river drops cf">
		<?php echo $droplet_list_view; ?>
	</div>