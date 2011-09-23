<p></p>
<div class="innertabs">
	<ul>
		<li <?php if($active=='main') echo 'class="selected"';?>><a href="<?php echo URL::site('/project/')."/".$project->id."/builder"; ?>"><span><?php echo __('Feeds'); ?></span></a></li>
		<li <?php if($active!='main') echo 'class="selected"';?>><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/main/new"; ?>"><span><?php echo __('Create New Feed'); ?> [+]</span></a></li>
	</ul>
</div>