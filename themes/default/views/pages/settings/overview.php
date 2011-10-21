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
		<legend><?php echo __('Sweeper Settings');?></legend>
		
		<p class="nomt">
			<label for="site_name" class="req"><?php echo __('Site Name');?>:</label><br />
			<?php echo Form::input("site_name", $post['site_name'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="site_locale" class="req"><?php echo __('Locale');?>:</label><br />
			<?php echo Form::input("site_locale", $post['site_locale'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

	</fieldset>

<?php echo Form::close(); ?>