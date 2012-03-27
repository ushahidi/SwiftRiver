	<hgroup class="page-title cf">
		<div class="center">
			<h1 class="<?php echo ($river->river_public == 0) ? "private" : "public"; ?>">
				<!--span class="icon"></span-->
				<?php if ($river->account->user->id == $user->id OR $river->account->user->username == 'public'): ?>
					<span id="display_river_name"><?php echo $river->river_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$river->account->account_path ?>"><span><?php echo $river->account->account_path; ?></a> / <?php echo $river->river_name; ?></span>
				<?php endif; ?>
			</h1>
			<?php if ($owner): ?>
			<h2 class="settings">
				<a href="<?php echo $settings_url; ?>">
					<span class="icon"></span>
					<span class="label"><?php echo __("River Settings"); ?></span>
				</a>
			</h2>
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

	<div id="content" class="river list cf">
		<?php echo $river_view_list; ?>
	</div>