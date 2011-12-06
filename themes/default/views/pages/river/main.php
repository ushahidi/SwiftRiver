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
					<li class="droplets active"><a href="#"><?php echo __('Droplets');?></a></li>
					<li><a href="#">Map</a></li>
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

		<div class="trend_container cf" id="river_droplets">
			<?php foreach ($droplets as $droplet): ?>
				<article class="droplet cf">
					<div class="summary">
						<section class="source <?php echo $droplet['channel'] ?>">
							<a href="/user"><img src="/themes/default/media/img/content/avatar1.gif" /></a>
							<div class="actions">
								<span class="type"></span>
								<p class="button_change score"><a onclick=""><span>0</span></a><p>
								<div class="clear"></div>
								<ul class="dropdown left">
									<li class="confirm"><a onclick="">This is useful</a></li>
									<li class="not_useful"><a onclick="">This is not useful</a></li>
								</ul>
							</div>
						</section>
						<section class="content">
							<div class="title">
								<p class="date"><?php echo $droplet['droplet_date_pub'] ?></p>
								<h1><?php echo $droplet['identity_name'] ?></h1>
							</div>
							<div class="body">
								<p><?php echo $droplet['droplet_title'] ?></p>
							</div>
						</section>
						<section class="actions">
							<p class="button_view"><a href="/droplet/detail/<?php echo $droplet['id'];?>" class="detail_view"><span></span><strong>detail</strong></a></p>
							<div class="button">
								<p class="button_change bucket"><a><span></span><strong>buckets</strong></a></p>
								<div class="clear"></div>
								<ul class="dropdown">
									<li class="bucket"><a onclick=""><span class="select"></span>Bucket 1</a></li>
									<li class="bucket"><a onclick=""><span class="select"></span>Bucket 2</a></li>
									<li class="create_new"><a onclick=""><span class="create_trigger"><em>Create new</em></span></a></li>
								</ul>
							</div>
						</section>
					</div>
					<section class="detail cf"></section>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="page_buttons">
		<p class="button_view"><a href="#">View more</a></p>
		</div>
	</div>
</article>	