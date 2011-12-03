<div class="list_stream">
	<ul>
		<?php if (count($rivers)) :
			foreach ($rivers as $river)
			{
				?>
				<li id="item_<?php echo $river->id; ?>">
					<a href="<?php echo URL::site().'river/index/'.$river->id; ?>" class="title"><?php echo $river->river_name; ?></a>
					<div class="actions">
						<span class="button_delete"><a><?php echo __('Delete River'); ?></a></span>
						<ul class="dropdown">
							<p><?php echo __('Are you sure you want to delete this River?'); ?></p>
							<li class="confirm"><a onclick="deleteItem(<?php echo $river->id; ?>,'river')"><?php echo __('Yep.'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
						</ul>
					</div>
					<span class="subscriber_count"><a href="#"><strong>22</strong> <?php echo __('subscribers'); ?></a></span>
				</li>
			<?php } 
		else:?>
			<li><?php echo __('No Rivers to Display Yet'); ?></li>
		<?php endif; ?>
	</ul>
</div>