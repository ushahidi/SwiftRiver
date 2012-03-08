<?php echo Form::open(); ?>
	<div id="settings">
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

		<div class="controls">
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Site Name'); ?></h3>
					<?php echo Form::input("site_name", $settings['site_name']); ?>
				</div>
			</div>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Site Locale'); ?></h3>
					<?php echo Form::input("site_locale", $settings['site_locale']); ?>
				</div>
			</div>
			<div class="row cf">
				<?php echo Form::checkbox('public_registration_enabled', 1,  (bool)$settings['public_registration_enabled']); ?>
				<strong><?php echo __('Allow public registration'); ?></strong>
			</div>

		<div class="row controls-buttons cf">
			<p class="button-go" onclick="submitForm(this)"><a><?php echo __('Apply changes'); ?></a></p>
			<p class="other"><a class="close" onclick=""><?php echo __('Cancel'); ?></a></p>
		</div>
	</div>
<?php echo Form::close(); ?>