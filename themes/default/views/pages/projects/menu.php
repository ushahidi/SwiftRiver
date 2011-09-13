<p></p>
<div class="tabs box">
	<ul>
		<li <?php if ( ! $active) echo 'class="ui-tabs-selected"'; ?>><a href="<?php echo URL::site('/projects');?>"><span><?php echo __('All Projects') ;?></span></a></li>
		<li <?php if ($active == 'edit') echo 'class="ui-tabs-selected"'; ?>><a href="#"><span><?php echo __('Edit Project') ;?></span></a></li>
	</ul>
</div>