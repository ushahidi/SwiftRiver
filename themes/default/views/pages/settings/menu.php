<p></p>
<div class="tabs box">
	<ul>
		<li <?php if ( ! $active) echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/settings');?>"><span><?php echo __('Application') ;?></span></a></li>
		<?php
		foreach ($plugin_settings as $settings)
		{
			?><li <?php if ($active == $settings) echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/settings/'.$settings);?>"><span><?php echo ucfirst($settings) ;?></span></a></li><?php
		}
		
		// Sweeper Plugin Hook -- add menu item
		Event::run('sweeper.settings.menu');
		?>
	</ul>
</div>