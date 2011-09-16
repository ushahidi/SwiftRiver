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
echo Form::open(URL::site('/project/'.$project->id.'/builder/confirm'));
echo Form::hidden('service', $service);
echo Form::hidden('service_option', $service_option);
?>
	
	<fieldset>
		<legend><?php echo __('Enter').' '.$service_option_name.' '.__('Details');?></legend>
		<?php
		if ( ! count($service_option_fields))
		{
			?>
			<p class="msg info"><?php echo __('This service has no options to set!');?></p>
			<p><input type="submit" value="<?php echo __('Finish');?>" class="input-submit" /></p>
			<?php
		}
		else
		{
			foreach ($service_option_fields as $key => $value)
			{
				?>
				<p class="nomt">
					<label for="<?php echo $key; ?>" class="req"><?php echo $value;?>:</label><br />
					<?php echo Form::input('options['.$key.']', '', array("size" => 60, "class" => "input-text-02 required") ); ?>
				</p>
				<?php
			}
			?>
			<p><input type="submit" value="<?php echo __('Next');?>" class="input-submit" /></p>
			<?php
		}?>	
	</fieldset>

<?php echo Form::close(); ?>