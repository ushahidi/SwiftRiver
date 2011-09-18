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
		<legend><?php echo __('Twitter Connection Settings');?></legend>
		
		<?php if ( ! $post['consumer_key'] AND ! $post['consumer_secret']): ;?>
			<p class="msg info"><a href="http://twitter.com/oauth_clients/" target="_blank"><?php echo __('Register your Sweeper application on Twitter'); ?></a></p>
			
			<p class="msg info"><?php echo __('Set the callback URL to'); ?> <?php echo HTML::anchor(URL::base('http', TRUE)."twitter/auth"); ?></p>
			<p class="msg info"><?php echo __('Set the application Default Access type to "Read-only"'); ?></p>
			<p class="msg info"><?php echo __('Enter the Twitter-provided consumer key and secret here'); ?></p>
		<?php else : ; 
			if ($authorized)
			{
				?>
				<p class="msg done"><?php echo __('Twitter Authorization is Complete!'); ?></p>
				<?php
			}
			?>
			<p class="box">
				<a href="<?php echo $auth_url; ?>" class="btn-info"><span><?php echo __('Authorize on Twitter'); ?></span></a>
			</p>
		<?php endif; ?>

		<p></p>
		
		<p class="nomt">
			<label for="consumer_key" class="req"><?php echo __('Consumer Key');?>:</label><br />
			<?php echo Form::input("consumer_key", $post['consumer_key'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>
		
		<p>
			<label for="consumer_secret" class="req"><?php echo __('Consumer Secret');?>:</label><br />
			<?php echo Form::input("consumer_secret", $post['consumer_secret'], array("size" => 60, "class" => "input-text-02 required")); ?>
		</p>

		<p><input type="submit" value="<?php echo __('Save Settings');?>" class="input-submit" /></p>

	</fieldset>

<?php echo Form::close(); ?>