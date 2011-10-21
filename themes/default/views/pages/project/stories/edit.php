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
		<legend><?php echo __('Story Details');?></legend>

		<p class="nomt">
			<label for="story_title" class="req"><?php echo __('Story Title');?>:</label><br />
			<?php echo Form::input("story_title", $post['story_title'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="story_summary" class="req"><?php echo __('Story Summary');?>:</label><br />
			<?php echo Form::textarea("story_summary", $post['story_summary'], array("cols" => 70, "rows" => 10,"class" => "input-text")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Story');?>" class="input-submit" /> <?php echo __('or');?> <a href="<?php echo URL::site('/project/')."/".$project->id."/stories"; ?>"><?php echo __('Cancel');?></a></p>

	</fieldset>

<?php echo Form::close(); ?>