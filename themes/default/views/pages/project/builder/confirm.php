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
echo Form::open(URL::site('/project/'.$project->id.'/builder/save'));
echo Form::hidden('service', $service);
echo Form::hidden('service_option', $service_option);
foreach ($options as $key => $value)
{
	echo Form::hidden('options['.$key.']', $value);
}
?>

	<fieldset>
		<legend><?php echo __('Confirm the details of this feed');?></legend>
		<dl>
			<dt><?php echo __('Feed Source'); ?></dt>
			<dd>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $service_name; ?></dd>
			<dt><?php echo __('Source Type'); ?></dt>
			<dd>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $service_option_name; ?></dd>
			<?php
			foreach ($service_option_fields as $key => $value)
			{
				?>
				<dt><?php echo $value; ?></dt>
				<dd><?php
				if ( ! isset($options[$key]) OR empty($options[$key]))
				{
					echo '&nbsp;&nbsp;&nbsp;&nbsp;--';
				}
				else
				{
					echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$options[$key];
				}
				?></dd>
				<?php
			}
			?>
		</dl>
		
		<p><input type="submit" value="<?php echo __('Confirm & Save');?>" class="input-submit" /></p>	
	</fieldset>

<?php echo Form::close(); ?>