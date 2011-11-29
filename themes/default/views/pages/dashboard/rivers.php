<div class="list_stream">
	<ul>
		<?php if (count($rivers))
		{
			foreach ($rivers as $river)
			{
				?>
				<li>
					<a href="<?php echo URL::site().'river/index/'.$river->id; ?>" class="title"><?php echo $river->river_name; ?></a>
					<div class="actions">
						<span class="button_delete"><a>Delete River</a></span>
						<ul class="dropdown">
							<p>Are you sure you want to delete this River?</p>
							<li class="confirm"><a onclick="">Yep.</a></li>
							<li class="cancel"><a onclick="">No, nevermind.</a></li>
						</ul>
					</div>
					<span class="subscriber_count"><a href="/river/subscribers.html"><strong>22</strong> subscribers</a></span>
				</li>
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