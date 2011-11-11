<div class="list_stream">
	<ul>
		<?php if (count($rivers))
		{
			foreach ($rivers as $river)
			{
				?>
				<li><a href="<?php echo URL::site().'river/index/'.$river->id; ?>" class="title"><?php echo $river->river_name; ?></a> <span class="subscriber_count"><a href="/river/subscribers.html"><strong>0</strong> <?php echo __('subscribers'); ?></a></span></li>
				<?php	
			}
			?>
		<?php
		}
		else
		{?>
			<li><?php echo __('No Rivers to Display Yet'); ?></li>
		<?php
		}?>
	</ul>
</div>