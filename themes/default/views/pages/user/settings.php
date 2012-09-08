<div class="col_12">
	<?php if (isset($errors)): ?>
		<div class="alert-message red">
		<p><strong>Uh oh.</strong></p>
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
		<p><strong>Success.</strong></p>
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

	<?php echo Form::open(NULL, array('id' => 'account-settings-form')) ?>
	<article class="container base">
		<header class="cf">
			<div class="property-title col_12">
				<h1><?php echo __("About You"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __('Full name'); ?></p>
					<?php echo Form::input("name", $user->name, array('id' => 'name')); ?>
				</div>
				<div class="field">
					<p class="field-label"><?php echo __('Nickname'); ?></p>
					<?php echo Form::input("nickname", $user->account->account_path, array('id' => 'nickname')); ?>
				</div>
				<div class="field">
					<p class="field-label"><?php echo __('Email address'); ?></p>
					<?php echo Form::input("email", $user->email, array('id' => 'email')); ?>
					<?php echo Form::hidden("orig_email", $user->email, array('id' => 'orig_email')); ?>
				</div>
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="cf">
			<div class="property-title col_12">
				<h1><?php echo __("Password"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __('Password'); ?></p>
					<?php echo Form::password("password", "", array('id' => 'password')); ?>
				</div>
				<div class="field">
					<p class="field-label"><?php echo __('Confirm password'); ?></p>
					<?php echo Form::password("password_confirm", "", array('id' => 'password_confirm')); ?>
				</div>
			</div>
		</section>
	</article>

	<article class="container base">
		<header class="cf">
			<div class="property-title col_12">
				<h1><?php echo __('Photo'); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter cf">
				<a class="avatar-wrap"><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /></a>
				<p class="button-blue button-small no-icon">
					<a href="http://www.gravatar.com" target="_blank"><?php echo __('Use a different photo'); ?></a>
				</p>
			</div>
		</section>
	</article>
	<div class="save-toolbar">
		<p class="button-blue"><a href="#"><?php echo __("Save Changes"); ?></a></p>
	</div>
	<?php echo Form::hidden("current_password"); ?>
	<?php echo Form::close(); ?>
</div>

<article class="modal" style="display: none;" id="password_prompt">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Enter your current password</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon-cancel"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>
	
	<div class="modal-body">
		
		<div class="alert-message red" style="display:none">
			<p><strong>Uh oh.</strong> <span class="message"></span></p>
		</div>

		<div class="alert-message blue" style="display:none">
			<p><strong>Success</strong> <span class="message"></span></p>
		</div>
		
		<?php echo Form::open() ?>
		<article class="container base">
			<section class="property-parameters">
				<div class="parameter">
					<label for="current_password_prompt">
						<p class="field">Current Password</p>
						<?php echo Form::password("current_password_prompt"); ?>
					</label>
				</div>
			</section>
		</article>

		<div class="save-toolbar">
			<p class="button-blue"><a href="#"><?php echo __("Save"); ?></a></p>
		</div>
		<?php echo Form::close(); ?>
	</div>
</article>