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
		<legend><?php echo __('Project Details');?></legend>

		<p class="nomt">
			<label for="project_title" class="req"><?php echo __('Project Title');?>:</label><br />
			<?php echo Form::input("project_title", $post['project_title'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p>
			<label for="project_description" class="req"><?php echo __('Project Description');?>:</label><br />
			<?php echo Form::textarea("project_description", $post['project_description'], array("cols" => 70, "rows" => 10,"class" => "input-text")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Project');?>" class="input-submit" /> <?php echo __('or');?> <a href="<?php echo URL::site().'projects'; ?>"><?php echo __('Cancel');?></a></p>

	</fieldset>

<?php echo Form::close(); ?>