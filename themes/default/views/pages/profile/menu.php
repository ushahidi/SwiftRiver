<p></p>
<div class="tabs box">
	<ul>
		<li class="ui-tabs-selected"><a href="#"><span><?php echo __('Overview') ;?></span></a></li>
		<li><a href="#"><span><?php echo __('Edit My Profile') ;?></span></a></li>
		<li><a href="#"><span><?php echo __('Private Messages') ;?></span></a></li>
		<?php
		// Sweeper Plugin Hook -- add menu item
		Event::run('sweeper.profile.menu');
		?>
	</ul>
</div>