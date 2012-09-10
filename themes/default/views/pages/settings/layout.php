<hgroup class="app-title cf">
	<div class="center">
		<div class="col_12">
			<h1><?php echo __('Site Settings'); ?></h1>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<ul class="center">
		<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().'settings';?>"><?php echo __('Application'); ?></a>
		</li>
		<li <?php if ($active == 'users') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().'settings/users';?>"><?php echo __('Users'); ?></a>
		</li>
		<li <?php if ($active == 'invites') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().'settings/invites';?>"><?php echo __('Invites'); ?></a>
		</li>
		<li <?php if ($active == 'plugins') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().'settings/plugins';?>"><?php echo __('Plugins'); ?></a>
		</li>
		<li <?php if ($active == 'quotas') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site('settings/quotas'); ?>"><?php echo __('Channel Quotas'); ?></a>
		</li>
		<?php
			// Swiftriver Plugin Hook -- add settings nav item
			Swiftriver_Event::run('swiftriver.settings.nav', $active);
		?>
	</ul>
</nav>

<div id="content" class="settings cf">
	<div class="center">
		<div class="col_12">
			<?php echo $settings_content; ?>
		</div>
	</div>
</div>