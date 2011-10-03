<fieldset>
	<legend><?php echo __('Select Feed Service Provider');?></legend>
	<?php
	if ( ! count($services))
	{
		?>
		<p class="msg warning"><?php echo __('You don\'t have any feed service providers in the system. Please activate services in the Plugins menu.');?></p>
		<?php
	}
	else
	{
		?><p class="box"><?php
		foreach ($services as $key => $value)
		{
			?><a class="btn-create" href="<?php echo URL::site('/project/')."/".$project->id."/builder/".$key; ?>"><span><?php echo $value; ?></span></a><?php
		}
		?>
		</p>
		<?php
	}?>
	<p><br /><br /><br /><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/"; ?>"><?php echo __('Cancel');?></a></p>
</fieldset>