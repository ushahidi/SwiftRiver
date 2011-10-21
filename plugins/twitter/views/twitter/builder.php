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

		<p class="msg info"><?php echo __('This feed will use one or a combination of the following.'); ?></p>
		<p class="msg info"><?php echo __('Create multiple twitter feeds if you need to query separate items'); ?></p>
		
		<p class="nomt">
			<label for="keywords" class="req"><?php echo __('Keyword[s]');?>:</label><br />
			<?php echo Form::input("keywords", $post['keywords'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="hashtag" class="req"><?php echo __('Hashtag').' ('.__('e.g. #ushahidi').')';?>:</label><br />
			<?php echo Form::input("hashtag", $post['hashtag'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="from" class="req"><?php echo __('Tweets BY').' ('.__('e.g. @ushahidi').')';?>:</label><br />
			<?php echo Form::input("from", $post['from'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="to" class="req"><?php echo __('Tweets TO').' ('.__('e.g. @ushahidi').')';?>:</label><br />
			<?php echo Form::input("to", $post['to'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="mention" class="req"><?php echo __('Tweets that MENTION').' ('.__('e.g. @ushahidi').')';?>:</label><br />
			<?php echo Form::input("mention", $post['mention'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>		

		<p>
			<label for="place" class="req"><?php echo __('Place').' ('.__('e.g. Nairobi, Kenya').')';?>:</label><br />
			<?php echo Form::input("place", $post['place'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>