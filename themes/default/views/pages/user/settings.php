<?php echo Form::open() ?>
<div class="col_12">

	<?php if (isset($success)): ?>
		<?php $css_class = ($success) ? "blue" : "red"; ?>
		<div class="alert-message <?php echo $css_class; ?>">
			<p><?php echo $messages; ?></p>
		</div>
	<?php endif; ?>

	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("About You"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('Full name'); ?></p>
					<?php echo Form::input("name", $user->name, array('id' => 'name')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('Username'); ?></p>
					<?php echo Form::input("username", $user->account->account_path, array('id' => 'username')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('Email address'); ?></p>
					<?php echo Form::input("email", $user->email, array('id' => 'email')); ?>
					<?php echo Form::hidden("orig_email", $user->email, array('id' => 'orig_email')); ?>
				</label>
			</div>
		</section>
	</article>
	
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("Password"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('Password'); ?></p>
					<?php echo Form::password("password", "", array('id' => 'password')); ?>
				</label>
			</div>
			<div class="parameter">
				<label>
					<p class="field"><?php echo __('Confirm password'); ?></p>
					<?php echo Form::password("password_confirm", "", array('id' => 'password_confirm')); ?>
				</label>
			</div>
		</section>
	</article>

	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __('Photo'); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter cf">
				<a class="avatar-wrap"><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /></a>
				<p class="button-blue button-small no-icon">
					<a href="http://www.gravatar.com" target="_blank"><?php echo __('Use a differrent photo'); ?></a>
				</p>
			</div>
		</section>
	</article>
	<div class="save-toolbar">
		<p class="button-blue" onclick="submitForm(this);"><a><?php echo __("Save Changes"); ?></a></p>
	</div>
</div>
<?php Form::close(); ?>