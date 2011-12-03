<article id="droplet_full" class="droplet dashboard cf">
	<div class="cf center page_title">
		<h1><span><?php echo __('Settings') ?></span></h1>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>><a href="<?php echo URL::site().'settings';?>"><?php echo __('Application'); ?></a></li>
					<li <?php if ($active == 'plugins') echo 'class="active"'; ?>><a href="<?php echo URL::site().'settings/plugins';?>"><?php echo __('Plugins'); ?></a></li>
					<?php
					// Swiftriver Plugin Hook -- add menu item
					Swiftriver_Event::run('swiftriver.settings.menu');
					?>
				</ul>
			</nav>
			<div class="panel_body"></div>
		</section>
		
		<?php echo $sub_content; ?>
		
	</div>	
</article>