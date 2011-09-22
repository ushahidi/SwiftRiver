<p></p>
<div class="innertabs">
	<ul>
		<li class="selected"><a href="<?php echo URL::site('/project/')."/".$project->id."/stories"; ?>"><span><?php echo __('Stories'); ?></span></a></li>
		<li><a href="<?php echo URL::site('/project/')."/".$project->id."/stories/edit"; ?>"><span><?php echo __('Create New Story'); ?> [+]</span></a></li>
	</ul>
</div>

<table width="100%">
	<tr>
		<th><?php echo __('Story'); ?></th>
		<th><?php echo __('Summary'); ?></th>
		<th><?php echo __('Action'); ?></th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/stories/edit"; ?>"><?php echo __('There are no stories in the system. Create a new one.'); ?></span></td>
		</tr>	
		<?php
	}
	
	
	$i = 0;
	foreach ($stories as $story)
	{
		$items = $story->items->count_all();
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><?php echo $story->story_title; ?></td>
				<td><?php echo $story->story_summary; ?></td>
				<td><a href="#"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="#"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
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