<article>
	<div class="cf center page-title">
		<hgroup class="edit">
			<h1><span class="edit_trigger" title="river" id="edit_<?php echo $river->id; ?>" onclick=""><?php echo $river->river_name; ?></span></h1>
		</hgroup>
	</div>
	
	<div class="center canvas">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li class="droplets <?php ($active == 'droplets') ? 'active' : ''; ?>"><a href="<?php echo URL::site().'river/index/'.$river->id; ?>"><?php echo __('Drops');?></a></li>
					<?php
					// SwiftRiver Plugin Hook -- Add River Nav Item
					Swiftriver_Event::run('swiftriver.river.nav', $river);
					?>
					<li class="view-panel"><a href="<?php echo $more_url; ?>"><span class="arrow"></span>More</a></li>
				</ul>
			</nav>
			<div class="drawer"></div>
		</section>

		<div class="trend_container cf">
		    <?php echo $trend; ?>
		</div>

	</div>
</article>	