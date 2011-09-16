<p></p>
<div class="innertabs">
	<ul>
		<li class="selected"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder"; ?>"><span><?php echo __('Feeds'); ?></span></a></li>
		<li><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/new"; ?>"><span><?php echo __('Create New Feed'); ?> [+]</span></a></li>
	</ul>
</div>

<table width="100%">
	<tr>
		<th><?php echo __('Feed'); ?></th>
		<th><?php echo __('Type'); ?></th>
		<th><?php echo __('Values'); ?></th>
		<th><?php echo __('Action'); ?></th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/new"; ?>"><?php echo __('There are no feeds in the system. Add Some.'); ?></span></td>
		</tr>	
		<?php
	}
	
	
	$i = 0;
	foreach ($feeds as $feed)
	{
		$items = $feed->items->count_all();
		$service_name = $services[$feed->service];
		$service_options = Plugins::get_service_options($feed->service);
		$service_option_name = $service_options[$feed->service_option]['name'];
		$service_option_fields = $service_options[$feed->service_option]['fields'];
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><?php echo $service_name; ?></td>
				<td><?php echo $service_option_name; ?></td>
				<td><?php
				$options = $feed->feed_options->find_all();
				foreach ($options as $option)
				{
					if ($option->value)
					{
						echo $option->value.'&nbsp;&nbsp;';
					}
				}
				?></td>
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