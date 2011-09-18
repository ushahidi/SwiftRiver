<p></p>
<div class="innertabs">
	<ul>
		<li><a href="<?php echo URL::site('/project/')."/".$project->id."/builder"; ?>"><span><?php echo __('Feeds'); ?></span></a></li>
		<li class="selected"><a href="<?php echo URL::site('/project/')."/".$project->id."/builder/new/"; ?>"><span><?php echo __('Create New Feed'); ?> [+]</span></a></li>
	</ul>
</div>

<?php
if (isset($errors))
{
	foreach ($errors as $message)
	{
		?><p class="msg error"><?php echo $message;?></p><?php
	}
}
?>
<?php
echo Form::open(URL::site('/project/'.$project->id.'/builder/options')); 
echo Form::hidden('service', $service);
?>
	
	<fieldset>
		<legend><?php echo __('Available options for').' '.$service_name;?></legend>
		<?php
		if ( ! count($service_options))
		{
			?>
			<p class="msg info"><?php echo __('This service has no further options.');?></p>
			<p><input type="submit" value="<?php echo __('Finish');?>" class="input-submit" /></p>
			<?php
		}
		else
		{
			foreach ($service_options as $key => $value)
			{
				if ( ! isset($value['name']))
				{
					$value['name'] = '-- '.__('Unknown').' --';
				}
				?>
				<p class="nomt">
					<?php echo Form::radio('service_option', $key, FALSE, array('id' => $key)); ?> <label for="<?php echo $key; ?>"><?php echo $value['name']; ?></label>
				</p>
				<?php
			}
			?>
			<p><input type="submit" value="<?php echo __('Next');?>" class="input-submit" /></p>
			<?php
		}?>	
	</fieldset>

<?php echo Form::close(); ?>