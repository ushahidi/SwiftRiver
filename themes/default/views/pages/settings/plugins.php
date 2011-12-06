<?php echo Form::open(); ?>
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="">
	<div class="list_stream">
		<ul>
			<?php if ($plugins->count()):
				foreach ($plugins as $plugin)
				{
					?>
					<li id="item_<?php echo $plugin->id; ?>">
						<a href="#" class="title"><?php echo $plugin->plugin_name;; ?></a>
						<div class="actions">
							<?php if ($plugin->plugin_enabled): ?>
								<span class="button_delete active"><a><?php echo __('Deactivate Plugin'); ?></a></span>
								<ul class="dropdown">
									<p><?php echo __('Are you sure you want to deactivate this Plugin?'); ?></p>
									<li class="confirm"><a onclick="pluginAction(this, 0,<?php echo $plugin->id; ?>)"><?php echo __('Yep.'); ?></a></li>
									<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
								</ul>
							<?php else: ?>
								<span class="button_delete"><a><?php echo __('Activate Plugin'); ?></a></span>
								<ul class="dropdown">
									<p><?php echo __('Are you sure you want to activate this Plugin?'); ?></p>
									<li class="confirm"><a onclick="pluginAction(this, 1,<?php echo $plugin->id; ?>)"><?php echo __('Yep.'); ?></a></li>
									<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
								</ul>
							<?php endif; ?>
						</div>
					</li>
				<?php } 
			else:?>
				<li><?php echo __('No Plugins in the System'); ?></li>
			<?php endif; ?>
		</ul>
	</div>
<?php echo Form::close(); ?>