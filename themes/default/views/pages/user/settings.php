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
			<div class="property-title">
				<h1><?php echo __("ui.section.user.about"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('ui.account.settings.fullname'); ?></p>
					<?php echo Form::input("name", $user->name, array('id' => 'name')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('ui.account.settings.nickname'); ?></p>
					<?php echo Form::input("nickname", $user->account->account_path, array('id' => 'nickname')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('ui.account.settings.email'); ?></p>
					<?php echo Form::input("email", $user->email, array('id' => 'email')); ?>
					<?php echo Form::hidden("orig_email", $user->email, array('id' => 'orig_email')); ?>
				</label>
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("ui.section.password"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('ui.account.settings.password'); ?></p>
					<?php echo Form::password("password", "", array('id' => 'password')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('ui.account.settings.password.confirm'); ?></p>
					<?php echo Form::password("password_confirm", "", array('id' => 'password_confirm')); ?>
				</label>
			</div>
		</section>
	</article>

	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __('ui.section.photo'); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter cf">
				<a class="avatar-wrap"><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /></a>
				<p class="button-blue button-small no-icon">
					<a href="http://www.gravatar.com" target="_blank"><?php echo __('ui.button.photo.change'); ?></a>
				</p>
			</div>
		</section>
	</article>
	<div class="save-toolbar">
		<p class="button-blue"><a href="#"><?php echo __("ui.button.save.changes"); ?></a></p>
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
					<span class="icon"></span>
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
						<p class="field"><?php echo __('ui.account.settings.password.current'); ?></p>
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