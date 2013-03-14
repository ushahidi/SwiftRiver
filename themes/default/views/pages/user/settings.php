<section id="filters" class="col_3">
	<div class="modal-window">
		<div class="modal">		
			<ul class="body-tabs-menu filters-primary">
				<li class="active"><a href="#account" class="modal-close">Account</a></li>
				<!--
				<li><a href="#notifications" class="modal-close">Notifications</a></li>
				<li><a href="#services" class="modal-close">Services</a></li>
				-->
			</ul>
		</div>
	</div>
</section>

<div id="settings" class="body-tabs-window col_9">

	<!-- TAB: Account -->
	<div id="account" class="active">
		<article class="base settings-category">
			<?php echo Form::open(); ?>
			<h1><?php echo __("Profile"); ?></h1>
			<div class="body-field">
				<h3 class="label"><?php echo __('Full name'); ?></h3>
				<?php echo Form::input("name", $user['owner']['name'], array('id' => 'name')); ?>
			</div>
			<div class="body-field">
				<h3 class="label"><?php echo __('Nickname'); ?></h3>
				<?php echo Form::input("nickname", $user['account_path'], array('id' => 'nickname')); ?>
			</div>
			<div class="body-field">
				<h3 class="label"><?php echo __('Email'); ?></h3>
				<?php echo Form::input("email", $user['owner']['email'], array('id' => 'email')); ?>
			</div>
			<div class="settings-category-toolbar">
				<a href="#" class="button-submit button-primary" onClick="submitForm(this);"><?php echo __("Update Profile"); ?></a>
			</div>
			<?php echo Form::close(); ?>
		</article>

		<article class="base settings-category">
			<?php echo Form::close(); ?>
			<h1><?php echo __("Change password"); ?></h1>
			<div class="body-field">
				<h3 class="label"><?php echo __("Old password"); ?></h3>
				<?php echo Form::password('old_password'); ?>
			</div>
			<div class="body-field">
				<h3 class="label"><?php echo __("New password"); ?></h3>
				<?php echo Form::password('new_password'); ?>
			</div>
			<div class="body-field">
				<h3 class="label"><?php echo __("Confirm new password"); ?></h3>
				<?php echo Form::password('new_password_confirm')?>
			</div>
			<div class="settings-category-toolbar">
				<a href="#" class="button-submit button-primary"><?php echo __("Update Password"); ?></a>
			</div>
			<?php echo Form::close(); ?>
		</article>

	</div>
	
</div>
