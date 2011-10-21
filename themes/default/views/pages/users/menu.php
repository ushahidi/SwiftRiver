<p></p>
<div class="tabs box">
	<ul>
		<li <?php if ( ! $active) echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/users');?>"><span><?php echo __('Users') ;?></span></a></li>
		<?php
		// Sweeper Plugin Hook -- add menu item
		Event::run('sweeper.users.menu');
		?>
	</ul>
</div>