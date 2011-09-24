<?php echo $menu; ?>
<?php
if (isset($errors))
{
	foreach ($errors as $message)
	{
		?><p class="msg error"><?php echo $message;?></p><?php
	}
}

if (isset($msgs))
{
	foreach ($msgs as $message)
	{
		?><p class="msg done"><?php echo $message;?></p><?php
	}
}
?>
<?php
echo Form::open('', array('id' => 'list'));
echo Form::hidden('action', '', array('id' => 'action'));
echo Form::hidden('id', '', array('id' => 'id'));
?>
<table width="100%">
	<tr>
		<th><?php echo __('Feed'); ?></th>
		<th><?php echo __('Options'); ?></th>
		<th><?php echo __('Items'); ?></th>
		<th><?php echo __('Action'); ?></th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/main/new"; ?>"><?php echo __('There are no feeds in the system. Add Some.'); ?></span></td>
		</tr>	
		<?php
	}
	
	
	$i = 0;
	foreach ($feeds as $feed)
	{
		$items = $feed->items->count_all();
		$service_name = $services[$feed->service];

		$options = $feed->feed_options->find_all();
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><?php echo $service_name; ?></td>
				<td><ul class="nostyle"><?php
				foreach ($options as $option)
				{
					if ($option->value)
					{
						echo '<li><strong>'.$option->key.':</strong> '.$option->value.'</li>';
					}
				}
				?></ul></td>
				<td><?php echo $items; ?></td>
				<td><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/".$feed->service."/index/".$feed->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="javascript:quickAction('d','<?php echo __('Delete'); ?>',<?php echo $feed->id; ?>)"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
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

<?php echo Form::close(); ?>
<?php echo $paging; ?>