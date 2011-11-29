<div class="list_stream">
	<ul>
		<?php if (count($buckets)): ?>
			<li>
				<a href="/bucket/" class="title">Bucket 1</a>
				<div class="actions">
					<span class="button_delete"><a>Delete Bucket</a></span>
					<ul class="dropdown">
						<p>Are you sure you want to delete this Bucket?</p>
						<li class="confirm"><a onclick="">Yep.</a></li>
						<li class="cancel"><a onclick="">No, nevermind.</a></li>
					</ul>
				</div> 
				<span class="subscriber_count"><a href="/river/subscribers.html"><strong>22</strong> subscribers</a></span>
			</li>
		<?php else: ?>
			<li><?php echo __('No Buckets to Display Yet'); ?></li>
		<?php endif ?>
	</ul>
</div>