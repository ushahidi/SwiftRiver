<?php
echo Form::open('', array('id' => 'list'));
echo Form::hidden('action', '', array('id' => 'action'));
echo Form::hidden('id', '', array('id' => 'id'));
?>
<table width="100%">
	<tr>
		<th><?php echo __('Plugin');?></th>
		<th><?php echo __('Description');?></th>
		<th><?php echo __('Action');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($plugins as $plugin)
	{
		$btn_style = "btn-create";
		if ($plugin->plugin_enabled)
		{
			$status = __("Disable");
			$action = 0;
			$btn_style = "btn-delete";
		}
		else
		{
			$status = __("Enable");
			$action = 1;
		}
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><?php echo $plugin->plugin_name; ?></td>
				<td><?php echo $plugin->plugin_description; ?></td>
				<td>
					<a href="javascript:quickAction(<?php echo $action; ?>,'<?php echo strtoupper($status); ?>',<?php echo $plugin->id; ?>)" class="<?php echo $btn_style; ?>"><span><?php echo $status;?></span></a>
					<?php
					if ($plugin->plugin_enabled
						AND Plugins::has_settings($plugin->plugin_path))
					{
						?><a href="<?php echo URL::site('/settings')."/".$plugin->plugin_path ;?>" class="btn"><span><?php echo __('Settings');?></span></a><?php
					}
					?>
				</td>
			</tr>
		<?php
		$i = ($i == 1) ? 0 : $i++;
	}
	?>
</table>
<?php echo Form::close(); ?>
<?php echo $paging; ?>