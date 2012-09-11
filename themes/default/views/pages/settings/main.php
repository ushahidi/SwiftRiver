<?php if (isset($errors)): ?>
<div class="alert-message red">
	<p><strong><?php echo __("Error"); ?></strong></p>
	<ul>
		<?php if (is_array($errors)): ?>
			<?php foreach ($errors as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
		<?php else: ?>
			<li><?php echo $errors; ?></li>
		<?php endif; ?>
	</ul>
</div>
<?php endif; ?>

<?php if (isset($messages)): ?>
	<div class="alert-message blue">
	<p><strong><?php echo __("Success"); ?></strong></p>
	<ul>
		<?php if (is_array($messages)): ?>
			<?php foreach ($messages as $message): ?>
				<li><?php echo $message; ?></li>
			<?php endforeach; ?>
		<?php else: ?>
			<li><?php echo $messages; ?></li>
		<?php endif; ?>
	</ul>
	</div>
<?php endif; ?>

<?php echo Form::open(); ?>
	<article class="container base" id="alert_messages" style="display:none">
		<div class="alert-message red">
			<p><?php echo __("Oops! Something went wrong while processing your request"); ?></p>
		</div>
	</article>

	<article class="container base">
		<header class="cf">
			<div class="property-title col_12">
				<h1><?php echo __('Name'); ?></h1>
			</div>
		</header>

		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __("Site Name"); ?></p>
					<?php echo Form::input("site_name", $settings['site_name']); ?>
				</div>
				<div class="save-toolbar">
					<p class="button-blue"><a href="#" onclick="submitForm(this)"><?php echo __("Save Changes"); ?></a></p>
				</div>				
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="container cf">
			<div class="property-title col_12">
				<h1><?php echo __("Locale"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __("Site Locale"); ?></p>
					<?php echo Form::input("site_locale", $settings['site_locale']); ?>
				</div>
				<div class="save-toolbar">
					<p class="button-blue"><a href="#" onclick="submitForm(this)"><?php echo __("Save Changes"); ?></a></p>
				</div>				
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="container cf">
			<div class="property-title col_12">
				<h1><?php echo __("Access"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<?php echo Form::checkbox('public_registration_enabled', 1,  (bool)$settings['public_registration_enabled']); ?>
					<?php echo __('Allow public registration'); ?>
				</div>
				<div class="field">
					<?php echo Form::checkbox('anonymous_access_enabled', 1,  (bool)$settings['anonymous_access_enabled']); ?>
					<?php echo __('Allow anonymous access'); ?>
				</div>
				<div class="save-toolbar">
					<p class="button-blue"><a href="#" onclick="submitForm(this)"><?php echo __("Save Changes"); ?></a></p>
				</div>				
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="container cf">
			<div class="property-title col_12">
				<h1><?php echo __("River Expiry"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __('Expiry duration (days)'); ?></p>
					<?php echo Form::input('river_active_duration', $settings['river_active_duration']); ?>
				</div>
				<div class="field">
					<p class="field-label"><?php echo __('Notice Period (days)'); ?></p>
					<?php echo Form::input('river_expiry_notice_period', $settings['river_expiry_notice_period']); ?>
				</div>
				<div class="save-toolbar">
					<p class="button-blue"><a href="#" onclick="submitForm(this)"><?php echo __("Save Changes"); ?></a></p>
				</div>			
			</div>
		</section>
	</article>
<?php echo Form::close(); ?>