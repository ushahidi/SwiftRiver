<article id="login">
	<div class="cf center page-title">
		<hgroup>
			<h1><?php echo __('Registration'); ?></h1>
		</hgroup>
	</div>
	
	<div class="center canvas controls">
		<?php
		if (isset($errors))
		{
			foreach ($errors as $message)
			{
				?>
				<div class="system_message system_error">
					<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
				</div>
				<?php
			}
		}
		?>	
		<?php
		if (isset($messages))
		{
			foreach ($messages as $message)
			{
				?>
				<div class="system_message system_success">
					<p><strong><?php echo __('Success!'); ?></strong> <?php echo $message; ?></p>
				</div>
				<?php
			}
		}
		?>					
		<?php echo Form::open(); ?>
			<div class="row cf">
					<h3>Please provide the profile information below to complete registration</h3>
			</div>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Nickname'); ?></h3>
					<?php echo Form::input("nickname", $user->account->account_path); ?>
				</div>
			</div>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Your Name'); ?></h3>
					<?php echo Form::input("name", $user->name); ?>
				</div>
			</div>
			<div class="row controls-buttons cf">
				<p class="button-go" onclick="submitForm(this)"><a>Get started</a></p>
				<!--p class="other"><a href="#"><span></span>Forgot your password?</a></p-->
			</div>
		<?php echo Form::close(); ?>
		</div>
		
	</div>
</article>