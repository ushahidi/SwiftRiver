<table width="100%">
	<tr>
		<th>Project</th>
		<th>Stories</th>
		<th>Stream</th>
		<th>Action</th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/projects/edit'); ?>"><?php echo __('There are no projects. Create one.'); ?></span></td>
		</tr>	
		<?php
	}	
	$i = 0;
	foreach ($projects as $project)
	{
		$stories = $project->stories->count_all();
		$items = $project->items->count_all();
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><h4><a href="<?php echo URL::site('/project/')."/".$project->id; ?>"><?php echo $project->project_title; ?></a></h4></td>
				<td><h4><a href="<?php echo URL::site('/project/')."/".$project->id."/stories"; ?>"><?php echo $stories; ?></a></h4></td>
				<td><h4><a href="<?php echo URL::site('/project/')."/".$project->id."/stream"; ?>"><?php echo $items; ?></a></h4></td>
				<td><a href="<?php echo URL::site('/project/')."/".$project->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-show.gif" class="ico" alt="Show" /></a>  <a href="<?php echo URL::site('/projects/edit/')."/".$project->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="#"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
			</tr>
		<?php
		$i = ($i == 1) ? 0 : $i++;
	}
	?>
</table>

<?php echo $paging; ?>