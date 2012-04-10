	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php echo __("Get started"); ?></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li class="active"><a href="/login"><?php echo __("Log in"); ?></a></li>
			<li><a href="#"><?php echo __("Create an account"); ?></a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="modal col_9">

			<?php if (isset($errors)): ?>
				<?php foreach ($errors as $message): ?>
					<div class="alert-message red">
						<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			
			<?php if (isset($messages)): ?>
				<?php foreach ($messages as $message): ?>
					<div class="alert-message blue">
						<p><strong><?php echo __('Success!'); ?></strong> <?php echo $message; ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php echo Form::open(); ?>
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1><?php echo __("Enter your account information"); ?></h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="username">
								<p class="field"><?php echo __('Email'); ?></p>
								<?php echo Form::input("username", ""); ?>
							</label>
						</div>
						<div class="parameter">
							<label for="password">
								<p class="field"><?php echo __('Password'); ?></p>
								<?php echo Form::password("password", ""); ?>
							</label>
						</div>
						<div class="parameter">
							<label for="remember">
								<?php echo Form::checkbox('remember', 1); ?>
								<?php echo __('Remember me'); ?>
							</label>
						</div>
					</section>
				</article>

				<div class="save-toolbar">
					<?php echo Form::hidden('referrer', $referrer); ?>
					<p class="button-blue" onclick="submitForm(this)"><a><?php echo __("Log in"); ?></a></p>
				</div>
			<?php echo Form::close(); ?>
			</div>

			<div class="col_3">
				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span><?php echo __("Forgot your password?"); ?></h3>
					<div class="meta-data-content">
					<?php echo Form::open(); ?>
						<label>
							<?php echo __('Your email address') ?>
							<?php echo Form::input("recover_email", ""); ?>
						</label>
						<p class="button-blue button-small" onclick="submitForm(this)"><a><?php echo __('Reset password');?></a></p>
					<?php echo Form::close(); ?>
					</div>
				</section>
			</div>
		</div>
	</div>