<?php echo $menu; ?>

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
		<legend><?php echo __('Twitter Feed Builder');?></legend>

		<p class="msg info"><?php echo __('This feed will use one or a combination of the following. Example: Search for all keywords containing "ushahidi" near "nairobi".'); ?></p>
		<p class="msg info"><?php echo __('Create multiple twitter feeds if you need to query separate items'); ?></p>
		
		<p class="nomt">
			<label for="keywords" class="req"><?php echo __('Keywords').' ('.__('separate with commas').')';?>:</label><br />
			<?php echo Form::input("keywords", $post['keywords'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="hashtags" class="req"><?php echo __('Hashtags').' ('.__('separate with commas').')';?>:</label><br />
			<?php echo Form::input("hashtags", $post['hashtags'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="users" class="req"><?php echo __('Twitter Users').' ('.__('separate with commas').')';?>:</label><br />
			<?php echo Form::input("users", $post['users'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="place" class="req"><?php echo __('Place').' ('.__('e.g. Nairobi, Kenya').')';?>:</label><br />
			<?php echo Form::input("place", $post['place'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>