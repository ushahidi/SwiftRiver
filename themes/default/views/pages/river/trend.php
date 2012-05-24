<div id="content" class="center">
	<?php foreach ($trends as $type => $periods): ?>
		<div class="col_4">
			<article class="container action-list base">
				<header class="cf">
					<div class="property-title">
						<h1><?php echo $type?></h1>
					</div>
				</header>
				<section class="property-parameters">
					<?php foreach ($periods['data'] as $period => $tags): ?>
						<?php if ( ! empty($tags)): ?>
							<p class="category own-title"><?php echo $period; ?></p>
							<?php foreach ($tags as $tag): ?>
								<div class="parameter"><a href="<?php echo $tag['url']; ?>"><?php echo $tag['tag']; ?></a></div>				
							<?php endforeach; ?>
						<?php endif;?>
					<?php endforeach; ?>
					<?php if ( ! $periods['has_data']): ?>
						<section class="property-parameters">
							<div class="parameter">
								<p><?php echo __('No data'); ?></p>
							</div>
						</section>
					<?php endif; ?>
				</section>
			</article>
		</div>
	<?php endforeach; ?>
</div>