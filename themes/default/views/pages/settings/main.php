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
			<div class="property-title">
				<h1><?php echo __('General'); ?></h1>
			</div>
		</header>

		<section class="property-parameters">
			<div class="parameter">
				<label for="site_name">
					<p class="field"><?php echo __("Site Name"); ?></p>
					<?php echo Form::input("site_name", $settings['site_name']); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="site_locale">
					<p class="field"><?php echo __("Site Locale"); ?></p>
					<?php echo Form::input("site_locale", $settings['site_locale']); ?>
				</label>
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __('Email'); ?></h1>
			</div>
		</header>

		<section class="property-parameters">
			<div class="parameter">
				<label for="email_domain">
					<p class="field"><?php echo __("Default Domain"); ?></p>
					<?php echo Form::input("email_domain", $settings['email_domain']); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="comments_email_domain">
					<p class="field"><?php echo __("Comments Domain"); ?></p>
					<?php echo Form::input("comments_email_domain", $settings['comments_email_domain']); ?>
				</label>
			</div>
		</section>
	</article>
	
	
	<article class="container base">
		<header class="container cf">
			<div class="property-title">
				<h1><?php echo __("Access"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="public_registration_enabled">
					<?php echo Form::checkbox('public_registration_enabled', 1,  (bool)$settings['public_registration_enabled']); ?>
					<?php echo __('Allow public registration'); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="anonymous_access_enabled">
					<?php echo Form::checkbox('anonymous_access_enabled', 1,  (bool)$settings['anonymous_access_enabled']); ?>
					<?php echo __('Allow anonymous access'); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="general_invites_enabled">
					<?php echo Form::checkbox('general_invites_enabled', 1,  (bool)$settings['general_invites_enabled']); ?>
					<?php echo __('Allow general invites'); ?>
				</label>
			</div>
		</section>
	</article>
	<article class="container base">
		<header class="container cf">
			<div class="property-title">
				<h1><?php echo __("River Lifetime"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="default_river_lifetime">
					<p class="field"><?php echo __('Default lifetime (days)'); ?></p>
					<?php echo Form::input('default_river_lifetime', $settings['default_river_lifetime']); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="river_expiry_notice_period">
					<p class="field"><?php echo __('Expiry notice period (days)'); ?></p>
					<?php echo Form::input('river_expiry_notice_period', $settings['river_expiry_notice_period']); ?>
				</label>
			</div>
		</section>
	</article>
	<article class="container base">
		<header class="container cf">
			<div class="property-title">
				<h1><?php echo __("Quotas"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="default_river_quota">
					<p class="field"><?php echo __('Default max rivers per account'); ?></p>
					<?php echo Form::input('default_river_quota', $settings['default_river_quota']); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="default_river_drop_quota">
					<p class="field"><?php echo __('Default max drops per river'); ?></p>
					<?php echo Form::input('default_river_drop_quota', $settings['default_river_drop_quota']); ?>
				</label>
			</div>
		</section>
	</article>
	<div class="save-toolbar">
		<p class="button-blue"><a href="#" onclick="submitForm(this)"><?php echo __("Save Changes"); ?></a></p>
	</div>
<?php echo Form::close(); ?>
