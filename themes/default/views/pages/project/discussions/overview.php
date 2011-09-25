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
		<th><?php echo __('Discussion Topic'); ?></th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/project/')."/".$project->id."/discussions/new"; ?>"><?php echo __('This project has no discussion topics. Create one.'); ?></span></td>
		</tr>	
		<?php
	}
	
	
	$i = 0;
	foreach ($discussions as $discussion)
	{
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><h4><?php echo $discussion->discussion_title; ?></h4></td>
			</tr>
		<?php
		$i = ($i == 1) ? 0 : $i++;
	}
	?>
</table>

<?php echo Form::close(); ?>
<?php echo $paging; ?>