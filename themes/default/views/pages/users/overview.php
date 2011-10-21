<p class="box">
	<a href="<?php echo URL::site('/users/edit');?>" class="btn-create"><span><?php echo __('Create a new User');?></span></a>
</p>
<table width="100%">
	<tr>
		<th><?php echo __('User'); ?></th>
		<th><?php echo __('Role'); ?></th>
		<th><?php echo __('Last Login'); ?></th>
		<th><?php echo __('Action'); ?></th>
	</tr>
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td colspan="3" align="center"><a href="<?php echo URL::site('/users/edit'); ?>"><?php echo __('There are no users. Create one.'); ?></span></td>
		</tr>	
		<?php
	}	
	$i = 0;
	foreach ($users as $user)
	{
		foreach ($user->roles->find_all() as $user_role)
		{
			$role = strtoupper($user_role->name);
		}
		?>
			<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
				<td><h4><a href="<?php echo URL::site('/users/edit')."/".$user->id; ?>"><?php echo $user->name; ?></a></h4></td>
				<td><?php echo $role; ?></td>
				<td><?php echo date('D M j, g:i a', $user->last_login); ?></td>
				<td><a href="<?php echo URL::site('/users/edit/')."/".$user->id; ?>"><img src="<?php echo URL::base();?>themes/default/media/img/ico-edit.gif" class="ico" alt="Edit" /></a>  <a href="#"><img src="<?php echo URL::base();?>themes/default/media/img/ico-delete.gif" class="ico" alt="Delete" /></a></td>
			</tr>
		<?php
		$i = ($i == 1) ? 0 : $i++;
	}
	?>
</table>

<?php echo $paging; ?>