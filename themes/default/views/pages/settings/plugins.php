<?php echo Form::open(); ?>
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="">
	<div class="container list select data">
	<?php if ($plugins->count()): ?>
		<?php
			$i = 0;
			foreach ($plugins as $plugin):
		?>
			<article class="item cf <?php if($i == 0) echo "alt_row"; ?>" id="item_<?php echo $plugin->id; ?>">
				<div class="content">
					<hgroup>
						<h3><a href="#" class="title"><?php echo $plugin->plugin_name; ?></a></h3>
						<span class="description"><?php echo $plugin->plugin_description; ?></span>
					</hgroup>
				</div>
				<div class="summary">
					<section class="actions">
						<?php if ($plugin->plugin_enabled): ?>
							<p class="button-delete"><a><?php echo __('Deactivate Plugin'); ?></a></p>
							<ul class="dropdown">
								<p><?php echo __('Are you sure you want to deactivate this Plugin?'); ?></p>
								<li class="confirm"><a onclick="pluginAction(this, 0,<?php echo $plugin->id; ?>)"><?php echo __('Yep.'); ?></a></li>
								<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
							</ul>
						<?php else: ?>
							<p class="button-delete button-delete-subtle"><a><?php echo __('Activate Plugin'); ?></a></p>
							<ul class="dropdown">
								<p><?php echo __('Are you sure you want to activate this Plugin?'); ?></p>
								<li class="confirm"><a onclick="pluginAction(this, 1,<?php echo $plugin->id; ?>)"><?php echo __('Yep.'); ?></a></li>
								<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
							</ul>
						<?php endif; ?>
					</section>
				</div>
			</article>
			<?php $i = ($i == 0) ? 1 : 0; ?>
		<?php endforeach; ?>
		
	<?php else:?>
		<li><?php echo __('No Plugins in the System'); ?></li>
	<?php endif; ?>
	</div>
<?php echo Form::close(); ?> 