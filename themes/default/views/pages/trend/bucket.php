<article>
	<div class="cf center page-title">
		<hgroup class="edit">
			<h1><span class="edit_trigger" title="bucket" id="edit_<?php echo $bucket->id; ?>" onclick=""><?php echo $bucket->bucket_name; ?></span></h1>
		</hgroup>
	</div>
	
	<div class="center canvas">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li class="droplets <?php ($active == 'droplets') ? 'active' : ''; ?>"><a href="<?php echo $drops_url; ?>"><?php echo __('Droplets');?></a></li>
					<?php
					// SwiftRiver Plugin Hook -- Add River Nav Item
					Swiftriver_Event::run('swiftriver.bucket.nav', $bucket);
					?>
					<li class="view_panel"><a href="<?php echo $more_url; ?>"><span class="arrow"></span>More</a></li>
				</ul>
			</nav>
			<div class="panel_body"></div>
		</section>

		<div class="trend_container cf">
		    <?php echo $trend; ?>
		</div>

	</div>
</article>	