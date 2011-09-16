<h3><?php echo __('Quick Stats'); ?></h3>

<!-- Button -->
<div class="btn-box">
<div class="btn-top"></div>
<div class="btn">
	<dl>
		<dt><a href="<?php echo URL::site('/project/'.$project->id.'/stream');?>"><?php echo __('Feed Stream');?></a> (<?php echo $items; ?>)</dt>
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
		<dt><a href="<?php echo URL::site('/project/'.$project->id.'/stories');?>"><?php echo __('Stories');?></a> (<?php echo $stories; ?>)</dt>
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
		<dt><a href="<?php echo URL::site('/project/'.$project->id.'/builder');?>"><?php echo __('Feeds');?></a> (<?php echo $feeds; ?>)</dt>
		<dd>Lorem ipsum dolor sit amet consectetur</dd>
	</dl>
</div> <!-- /btn -->
<div class="btn-bottom"></div>
</div> <!-- /btn-box -->