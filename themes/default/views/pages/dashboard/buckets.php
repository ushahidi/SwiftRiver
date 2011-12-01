<div class="list_stream">
	<ul>
		<?php if (count($buckets))
		{
			foreach ($buckets as $bucket)
			{
				?>
				<li id="item_<?php echo $bucket->id; ?>">
					<a href="<?php echo URL::site().'bucket/index/'.$bucket->id; ?>" class="title"><?php echo $bucket->bucket_name; ?></a>
					<div class="actions">
						<span class="button_delete"><a><?php echo __('Delete Bucket'); ?></a></span>
						<ul class="dropdown">
							<p><?php echo __('Are you sure you want to delete this Bucket?'); ?></p>
							<li class="confirm"><a onclick="deleteItem(<?php echo $bucket->id; ?>,'bucket')"><?php echo __('Yep.'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
						</ul>
					</div>
					<span class="subscriber_count"><a href="#"><strong>22</strong> <?php echo __('subscribers'); ?></a></span>
				</li>
				<?php	
			}
			?>
		<?php
		}
		else
		{?>
			<li><?php echo __('No Buckets to Display Yet'); ?></li>
		<?php
		}?>
	</ul>
</div>