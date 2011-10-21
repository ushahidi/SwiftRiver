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
		<legend><?php echo __('User Details');?></legend>

		<p class="nomt">
			<img src="<?php echo Users::gravatar($post['email']); ?>" width="80" />
		</p>

		<p>
			<label for="username" class="req"><?php echo __('User Name');?>:</label><br />
			<?php echo Form::input("username", $post['username'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		<p>
			<label for="name" class="req"><?php echo __('Full Name');?>:</label><br />
			<?php echo Form::input("name", $post['name'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="email" class="req"><?php echo __('Email');?>:</label><br />
			<?php echo Form::input("email", $post['email'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="email" class="req"><?php echo __('Role');?>:</label><br />
			<?php echo Form::select("role", $all_roles, $post['role'], array("class" => "input-text") ); ?>
		</p>

		<p>&nbsp;</p>

		<p>
			<label for="password" class="req"><?php echo __('Password');?>:</label><br />
			<?php echo Form::password("password", '', array("size" => 60, "class" => "input-text-02 required")); ?><br />
			<label for="password_confirm" class="req"><?php echo __('Password Again');?>:</label><br />
			<?php echo Form::password("password_confirm", '', array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>		

		<p><input type="submit" value="<?php echo __('Save User');?>" class="input-submit" /> <?php echo __('or');?> <a href="<?php echo URL::site().'users'; ?>"><?php echo __('Cancel');?></a></p>

	</fieldset>

<?php echo Form::close(); ?>