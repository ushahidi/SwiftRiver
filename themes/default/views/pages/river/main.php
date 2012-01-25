<article>
	<div class="cf center page-title">
		<hgroup class="edit">
			<h1><span class="edit-trigger" title="river" id="edit_<?php echo $river->id; ?>" onclick=""><?php echo $river->river_name; ?></span></h1>
		</hgroup>
		<?php if (count($droplets)): ?>
			<section class="meter">
				<p style="padding-left:<?php echo $meter; ?>%;"><strong><?php echo $filtered_total; ?></strong> <?php echo __('Droplets'); ?></p>
				<div><span style="width:<?php echo $meter; ?>%;"></span></div>
			</section>		
		<?php endif; ?>
	</div>
	
	<div class="center canvas">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li class="droplets active"><a href="<?php echo URL::site().'river/index/'.$river->id; ?>"><?php echo __('Droplets');?></a></li>
					<?php
					// SwiftRiver Plugin Hook -- Add River Nav Item
					Swiftriver_Event::run('swiftriver.river.nav', $river);
					?>
					<li class="view-panel"><a href="<?php echo $more_url; ?>"><span class="arrow"></span>More</a></li>
				</ul>
				<ul class="actions">
					<li class="view-panel">
						<a href="<?php echo $filters_url; ?>" class="channels">
							<span class="icon"></span>
							<span class="label"><?php echo __("Edit Filter"); ?></span>
						</a>
					</li>
					<li class="view-panel">
						<a href="<?php echo $settings_url; ?>" class="filter">
							<span class="icon"></span>
							<span class="label"><?php echo __("River Settings"); ?></span>
						</a>
					</li>
				</ul>
			</nav>
			<div class="panel-body"></div>
		</section>

		<div class="container stream insights">
		    <?php echo $droplets_list; ?>
		</div>

	</div>
</article>	