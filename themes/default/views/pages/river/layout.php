<hgroup class="page-title river-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1 class="<?php echo ($river->river_public == 0) ? "private" : "public"; ?>">
				<?php $river_name = $river->river_name; ?>
				<?php if ($river->account->user->id == $user->id OR $river->account->user->username == 'public'): ?>
					<span id="display_river_name"><?php echo $river->river_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$river->account->account_path ?>">
						<?php $river_name = $river->account->account_path.'/'.$river_name; ?>
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
			<h2 class="settings">
				<a href="<?php echo $settings_url.'/collaborators'; ?>">
					<span class="icon"></span>
					<?php echo __("Collaborators"); ?>
				</a>
			</h2>
		</div>
		<?php elseif ( ! $anonymous AND ! $is_collaborator): ?>
		<div class="follow-summary col_3" id="follow_button">
		</div>
		<?php echo $follow_button; ?>
		<?php endif; ?>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<div id="page-views" class="river touchcarousel col_9">
			<ul class="touchcarousel-container">
				<?php foreach ($nav as $item): ?>
				<li id="<?php echo $item['id']; ?>" class="touchcarousel-item <?php echo $item['active'] == $active ? 'active' : ''; ?>">
					<a href="<?php echo $river_base_url.$item['url']; ?>">
						<?php echo $item['label'];?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="filter-actions col_3">
			<p class="button-blue button-small"><a href="#" class="modal-trigger">Filters</a></p>
		</div>
	</div>
</nav>

<?php echo $droplets_view; ?>
