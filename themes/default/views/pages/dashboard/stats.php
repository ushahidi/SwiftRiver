<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="<?php echo URL::site('/projects/');?>"><?php echo __('Projects');?></a> (<?php echo $projects; ?>)</dt>
		<dd>Lorem ipsum dolor sit amet consectetur</dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="#"><?php echo __('Private Messages');?></a> (0)</dt>
		<dd>Lorem ipsum dolor sit amet consectetur</dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="#"><?php echo __('Pending Tasks');?></a> (0)</dt>
		<dd>Lorem ipsum dolor sit amet consectetur</dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<?php
// Sweeper Plugin Hook -- add button
Event::run('sweeper.dashboard.stats.button');
?>

<div class="fix"></div>

<!-- Charts -->
<div id="holder"></div>
<!-- /Charts -->