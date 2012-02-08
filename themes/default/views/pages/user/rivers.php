<div class="container list select data">
	<?php if (count($rivers)) : ?>
		<?php foreach ($rivers as $river): ?>
			<article class="item cf">
				<div class="content">
					<h1><a href="<?php echo URL::site().$river->account->account_path.'/river/index/'.$river->id; ?>" class="title"><?php echo $river->river_name ?></a></h1>
				</div>
				<div class="summary">
					<section class="actions">
						<div class="button">
							<p class="button-change"><a class="subscribe" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Subscribe'); ?></span></a></p>
							<div class="clear"></div>
							<div class="dropdown container">
								<p><?php echo __('Are you sure you want to subscribe to this river?'); ?></p>
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
		<h2 class="null"><?php echo __('No rivers to display'); ?></h2>
	<?php endif; ?>
</div>

