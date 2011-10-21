<p></p>
<div class="innertabs">
	<ul>
		<li <?php if($active=='main') echo 'class="selected"';?>><a href="<?php echo URL::site('/project/')."/".$project->id."/discussions"; ?>"><span><?php echo __('Discussions'); ?></span></a></li>
		<li <?php if($active!='main') echo 'class="selected"';?>><a href="<?php echo URL::site('/project/')."/".$project->id."/discussions/main/new"; ?>"><span><?php echo __('Create New Topic'); ?> [+]</span></a></li>
	</ul>
</div>