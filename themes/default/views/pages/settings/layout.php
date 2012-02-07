<article id="droplet-full" class="list dashboard cf">
	<div class="cf center page-title">
		<h1><span><?php echo __('Settings') ?></span></h1>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'settings';?>"><?php echo __('Application'); ?></a>
					</li>
					<li <?php if ($active == 'plugins') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'settings/main/plugins';?>"><?php echo __('Plugins'); ?></a>
					</li>
					<?php
					// Swiftriver Plugin Hook -- add settings nav item
					Swiftriver_Event::run('swiftriver.settings.nav');
					?>
					<li><a href="#"><?php echo __('More'); ?></a></li>
				</ul>
			</nav>
			<div class="panel_body"></div>
		</section>
		
		<?php echo $settings_content; ?>
		
	</div>	
</article>