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
		<legend><?php echo __('Select Feed Provider');?></legend>
		
		<p class="nomt">
			<?php echo Form::radio('like_cats', 1, $cats); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>