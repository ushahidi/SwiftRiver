<?php
if (isset($errors))
{
	foreach ($errors as $message)
	{
		?><p class="msg error"><?php echo $message;?></p><?php
	}
}
?>
<?php echo Form::open(); ?>

	<fieldset>
		<legend><?php echo __('Open Calais Connection Settings');?></legend>
		
		<p class="box">
			<a href="http://www.opencalais.com/APIKey" class="btn-info"><span><?php echo __('Request An Open Calais Service Key'); ?></span></a>
		</p>

		<p class="nomt">
			<label for="service_key" class="req"><?php echo __('Service Key');?>:</label><br />
			<?php echo Form::input("service_key", $post['service_key'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>