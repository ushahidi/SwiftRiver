<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="<?php echo URL::site('/projects/');?>"><?php echo __('Projects');?></a> (<?php echo $projects; ?>)</dt>
		<dd><?php echo __('Total projects in the system');?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><?php echo __('Stream');?> (<?php echo $items; ?>)</dt>
		<dd><?php echo __('Total items generated from feeds');?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><?php echo __('Tags');?> (<?php echo $tags; ?>)</dt>
		<dd><?php echo __('Total tags generated from the stream');?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><?php echo __('Links');?> (<?php echo $links; ?>)</dt>
		<dd><?php echo __('Total links generated from the stream');?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<?php
// Sweeper Plugin Hook -- add button
Event::run('sweeper.dashboard.stats.button');
?>

<div class="fix"></div>