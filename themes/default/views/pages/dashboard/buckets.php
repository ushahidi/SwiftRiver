<div class="list_stream">
	<ul>
		<?php if (count($buckets)): ?>		
			<li><a href="/bucket/" class="title">Bucket 1</a> <span class="subscriber_count"><a href="/river/subscribers.html"><strong>22</strong> subscribers</a></span></li>
			<li><a href="/bucket/" class="title">Bucket 2</a> <span class="subscriber_count"><a href="/river/subscribers.html"><strong>4</strong> subscribers</a></span></li>
		<?php else: ?>
			<li><?php echo __('No Buckets to Display Yet'); ?></li>
		<?php endif ?>
	</ul>
</div>