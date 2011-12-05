<div class="list_stream">
	<ul>
		<?php if ($plugins->count()):
			foreach ($plugins as $plugin)
			{
				?>
				<li id="item_<?php echo $plugin->id; ?>">
					<a href="#" class="title"><?php echo $plugin->plugin_name;; ?></a>
					<div class="actions">
						<span class="button_delete"><a><?php echo __('Activate Plugin'); ?></a></span>
						<ul class="dropdown">
							<p><?php echo __('Are you sure you want to activate this Plugin?'); ?></p>
							<li class="confirm"><a onclick="deleteItem(<?php echo $plugin->id; ?>,'river')"><?php echo __('Yep.'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
						</ul>
					</div>
				</li>
			<?php } 
		else:?>
			<li><?php echo __('No Plugins in the System'); ?></li>
		<?php endif; ?>
	</ul>
</div>