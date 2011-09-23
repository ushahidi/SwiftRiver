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
		<legend><?php echo __('RSS Feed Builder');?></legend>

		<p class="nomt">
			<label for="url" class="req"><?php echo __('RSS/Atom URL');?>:</label><br />
			<?php echo Form::input("url", $post['url'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="keywords" class="req"><?php echo __('Keywords').' ('.__('separate with commas').')';?>:</label><br />
			<?php echo Form::input("keywords", $post['keywords'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>