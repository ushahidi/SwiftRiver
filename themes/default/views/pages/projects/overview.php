<table width="100%">
	<tr>
		<th>Project</th>
		<th>Stories</th>
		<th>Action</th>
	</tr>
	<?php
	$i = 0;
	foreach ($projects as $project)
	{
		$stories = $project->stories->count_all();
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