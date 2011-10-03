<p class="box">
	<a href="<?php echo URL::site('/project/')."/".$project->id."/builder/main/new";?>" class="btn-create"><span><?php echo __('Create a New Feed'); ?></span></a>
</p>
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
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/main/new"; ?>"><?php echo __('This project has no feeds. Create one.'); ?></span></td>
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
				<td><h4><?php echo $service_name; ?></h4></td>
				<td><ul class="nostyle"><?php
				foreach ($options as $option)
				{
					$value = ($option->password) ? '*********' : $option->value;
					if ($option->value)
					{
						echo '<li><strong>'.$option->key.':</strong> '.$value.'</li>';
					}
				}
				?></ul></td>
				<td><?php echo $items; ?></td>
				<td><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/".$feed->service."/index/".$feed->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="javascript:quickAction('d','<?php echo __('Delete'); ?>',<?php echo $feed->id; ?>)"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
			</tr>
		<?php
		$i = ($i == 1) ? 0 : $i++;
	}
	?>
</table>

<?php echo Form::close(); ?>
<?php echo $paging; ?>