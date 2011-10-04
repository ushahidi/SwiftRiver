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
		<legend><?php echo __('Facebook Connection Settings');?></legend>
		
		<p class="msg info"><a href="http://developers.facebook.com/setup/" target="_blank"><?php echo __('Register your Sweeper application on Facebook'); ?></a></p>
		
		<p class="msg info"><?php echo __('Set the site URL to'); ?> <?php echo HTML::anchor(URL::base('http', TRUE)); ?></p>
		<p class="msg info"><?php echo __('Enter the Facebook-provided Application ID/API key and Application secret here'); ?></p>

		<p></p>
		
		<p class="nomt">
			<label for="application_id" class="req"><?php echo __('Application ID / API Key');?>:</label><br />
			<?php echo Form::input("application_id", $post['application_id'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="application_secret" class="req"><?php echo __('Application Secret');?>:</label><br />
			<?php echo Form::input("application_secret", $post['application_secret'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>		

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>