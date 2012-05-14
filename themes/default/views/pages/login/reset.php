
<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_12">
			<h1>Password Reset</h1>
		</div>
	</div>
</hgroup>

<div id="content" class="settings cf">
	<div class="center">
		<div class="col_9">
			
			<?php if (isset($errors)): ?>
				<?php foreach ($errors as $message): ?>
					<div class="alert-message red">
						<p><?php echo $message; ?></p>
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
						<h1>Enter your new password</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('ui.account.settings.password'); ?></p>
							<?php echo Form::password("password", ""); ?>
						</label>
					</div>
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('ui.account.settings.password.confirm'); ?></p>
							<?php echo Form::password("password_confirm", ""); ?>
						</label>
					</div>
				</section>
			</article>

			<div class="save-toolbar">
				<p class="button-blue" onclick="submitForm(this)"><a>Set your password</a></p>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>