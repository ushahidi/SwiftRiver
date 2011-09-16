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
<?php echo Form::open(URL::site('/project/'.$project->id.'/builder/parameters')); ?>

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
			foreach ($services as $key => $value)
			{
				?>
				<p class="nomt">
					<?php echo Form::radio('service', $key, FALSE, array('id' => $key)); ?> <label for="<?php echo $key; ?>"><?php echo $value; ?></label>
				</p>
				<?php
			}
			?>
			<p><input type="submit" value="<?php echo __('Next');?>" class="input-submit" /></p>
			<?php
		}?>
	</fieldset>

<?php echo Form::close(); ?>