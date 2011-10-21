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
		<legend><?php echo __('Yahoo Placemaker Connection Settings');?></legend>
		
		<p class="box">
			<a href="http://developer.yahoo.com/geo/placemaker/" class="btn-info"><span><?php echo __('Request A Yahoo Placemaker Application ID'); ?></span></a>
		</p>

		<p class="nomt">
			<label for="appid" class="req"><?php echo __('Application ID');?>:</label><br />
			<?php echo Form::input("appid", $post['appid'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>