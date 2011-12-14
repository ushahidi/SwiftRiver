<article>
	<div class="cf center page_title">
		<hgroup class="edit">
			<h1><span class="edit_trigger" title="river" id="edit_<?php echo $river->id; ?>" onclick=""><?php echo $river->river_name; ?></span></h1>
		</hgroup>
		<?php if ( count($droplets) ):?>
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
					<li class="droplets active"><a href="/river/index/1"><?php echo __('Droplets');?></a></li>
					<li class="view_trend"><a href="/river/trend/map/1">Map</a></li>
					<li><a href="#">Tags</a></li>
					<li><a href="#">Links</a></li>
					<li><a href="#">Sources</a></li>
					<li class="view_panel"><a href="<?php echo $more_url; ?>"><span class="arrow"></span>More</a></li>
				</ul>
				<ul class="actions">
					<li class="view_panel"><a href="<?php echo $filters_url; ?>" class="channels"><span class="icon"></span><?php echo __('Edit Filter'); ?></a></li>
					<li class="view_panel"><a href="<?php echo $settings_url; ?>" class="filter"><span class="icon"></span><?php echo __('River Settings'); ?></a></li>
				</ul>
			</nav>
			<div class="panel_body"></div>
		</section>

		<div class="trend_container cf">
		    <?php echo $droplets_list; ?>
		</div>

	</div>
</article>	