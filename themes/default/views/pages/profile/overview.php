<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="#">Projects</a> (<?php echo $projects; ?>)</dt>
		<dd><?php echo __('Projects I started') ;?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="#">Stories</a> (<?php echo $stories; ?>)</dt>
		<dd><?php echo __('Stories I created') ;?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="#">Private Messages</a> (35)</dt>
		<dd><?php echo __('Messages sent to me') ;?></dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->

<?php
// Sweeper Plugin Hook -- add button
Event::run('sweeper.profile.stats.button');
?>

<div class="fix"></div>