<p></p>
<div class="innertabs">
	<ul>
		<li class="selected"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder"; ?>"><span><?php echo __('Feeds'); ?></span></a></li>
		<li><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/edit/"; ?>"><span><?php echo __('Create New Feed'); ?> [+]</span></a></li>
	</ul>
</div>

<table width="100%">
	<tr>
		<th>Feed</th>
		<th>Service</th>
		<th>Action</th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/edit/"; ?>"><?php echo __('There are no feeds in the system. Add Some.'); ?></span></td>
		</tr>	
		<?php
	}
	
	
	$i = 0;
	foreach ($feeds as $feed)
	{
		$items = $feed->items->count_all();
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><a href="<?php echo URL::site('/project/')."/".$project->id; ?>"><?php echo $project->project_title; ?></a></td>
				<td><?php echo $stories; ?></td>
				<td><a href="<?php echo URL::site('/project/')."/".$project->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-show.gif" class="ico" alt="Show" /></a>  <a href="<?php echo URL::site('/projects/edit/')."/".$project->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="#"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
			</tr>
		<?php
		if ($i == 1)
		{
			$i = 0;
		}
		else
		{
			$i++;
		}
	}
	?>
</table>

<?php echo $paging; ?>