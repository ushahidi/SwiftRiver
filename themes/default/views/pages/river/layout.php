	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1 class="<?php echo ($river->river_public == 0) ? "private" : "public"; ?>">
					<!--span class="icon"></span-->
					<?php if ($river->account->user->id == $user->id OR $river->account->user->username == 'public'): ?>
						<span id="display_river_name"><?php echo $river->river_name; ?></span>
					<?php else: ?>
						<a href="<?php echo URL::site().$river->account->account_path ?>">
							<span><?php echo $river->account->account_path; ?></a> / <?php echo $river->river_name; ?></span>
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
			<?php else: ?>
			<div class="follow-summary col_3">
				<p class="button-score button-white follow">
					<a href="#" title="now following">
						<span class="icon"></span><?php echo __("Follow"); ?>
					</a>
				</p>
			</div>
			<?php endif; ?>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="river touchcarousel col_9">
				<ul class="touchcarousel-container">
					<?php foreach ($nav as $item): ?>
					<li id="<?php echo $item['id']; ?>" class="touchcarousel-item <?php echo $item['active']; ?>">
						<a href="<?php echo $river_base_url.$item['url']; ?>">
							<?php echo $item['label'];?>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="filter-actions col_3">
				<p class="button-blue button-small"><a href="/markup/river/filters.php" class="zoom-trigger">Filters</a></p>
			</div>
		</div>
	</nav>
	
	<?php echo $sub_content; ?>
