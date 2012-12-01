<hgroup class="page-title river-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php echo $river->river_name; ?> <em><?php echo __("Analytics"); ?></em></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="back">
				<a href="<?php echo $river_base_url; ?>">
					<span class="icon"></span>
					<?php echo __("Return to river"); ?>
				</a>
			</h2>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<div id="page-views" class="settings touchcarousel col_12">
			<ul class="touchcarousel-container">
				<li class="touchcarousel-item <?php if ($active == 'overview' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/analytics/overview'; ?>"><?php echo __("Overview"); ?></a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'channels' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/analytics/channels'; ?>"><?php echo __("Channels"); ?></a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'sources' OR ! $active) echo 'active'; ?>">
					<a href="<?php echo $river_base_url.'/analytics/sources'; ?>"><?php echo __("Sources"); ?></a>
				</li>
			</ul>
		</div>
	</div>
</nav>

<?php echo $analytics_content; ?>