<p></p>
<div class="tabs box">
	<ul>
		<li <?php if ( ! $active) echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/');?>"><span><?php echo __('Overview') ;?></span></a></li>
		<li <?php if ($active == 'stream') echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/stream');?>"><span><?php echo __('Stream') ;?></span></a></li>
		<li <?php if ($active == 'stories') echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/stories');?>"><span><?php echo __('Stories') ;?></span></a></li>
		<li <?php if ($active == 'builder') echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/builder');?>"><span><?php echo __('Feed Builder') ;?></span></a></li>
		<li <?php if ($active == 'sources') echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/sources');?>"><span><?php echo __('Sources') ;?></span></a></li>
		<li <?php if ($active == 'discussion') echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/project/'.$project_id.'/discussion');?>"><span><?php echo __('Discussion') ;?></span></a></li>
		<?php
		// Sweeper Plugin Hook -- add menu item
		Event::run('sweeper.project.menu');
		?>
	</ul>
</div>