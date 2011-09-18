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
		<legend><?php echo __('Email Connection Settings');?></legend>
		
		<p class="nomt">
			<label for="username" class="req"><?php echo __('Mail Server Username');?>:</label><br />
			<?php echo Form::input("username", $post['username'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="password" class="req"><?php echo __('Mail Server Password');?>:</label><br />
			<?php echo Form::input("password", $post['password'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="server_host" class="req"><?php echo __('Mail Server Host');?>:</label><br />
			<?php echo Form::input("server_host", $post['server_host'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="server_port" class="req"><?php echo __('Mail Server Port');?>:</label><br />
			<?php echo Form::input("server_port", $post['server_port'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="server_host_type" class="req"><?php echo __('Mail Server Type');?>:</label><br />
			<?php echo Form::input("server_host_type", $post['server_host_type'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="server_ssl" class="req"><?php echo __('Mail Server SSL Support?');?>:</label><br />
			<?php echo Form::input("server_ssl", $post['server_ssl'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>