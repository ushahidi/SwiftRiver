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
		<legend><?php echo __('Facebook Feed Builder');?></legend>

		<p class="msg info"><?php echo __('Searches all public Facebook posts.'); ?></p>
		
		<p class="nomt">
			<label for="keywords" class="req"><?php echo __('Keyword[s]');?>:</label><br />
			<?php echo Form::input("keywords", $post['keywords'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>