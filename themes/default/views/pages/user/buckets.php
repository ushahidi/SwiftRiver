<div class="container list select data">
	<?php if (count($buckets)) : ?>
		<?php foreach ($buckets as $bucket): ?>
			<article class="item cf">
				<div class="content">
					<h1><a href="<?php echo URL::site().$bucket->account->account_path.'/bucket/index/'.$bucket->id; ?>" class="title"><?php echo $bucket->bucket_name ?></a></h1>
				</div>
				<div class="summary">
					<section class="actions">
						<div class="button">
							<p class="button-change"><a class="subscribe" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Subscribe'); ?></span></a></p>
							<div class="clear"></div>
							<div class="dropdown container">
								<p><?php echo __('Are you sure you want to subscribe to this bucket?'); ?></p>
								<ul>
									<li class="confirm"><a><?php echo __('Yep.'); ?></a></li>
									<li class="cancel"><a><?php echo __('No, nevermind.'); ?></a></li>
								</ul>
							</div>
						</div>
					</section>
					<section class="meta">
						<p><a href="#"><strong>4</strong> <?php echo __('subscribers'); ?></a></p>
					</section>
				</div>		
			</article>
		<?php endforeach; ?>
	<?php else: ?>
		<h2 class="null"><?php echo __('No buckets to display'); ?></h2>
	<?php endif; ?>
</div>

