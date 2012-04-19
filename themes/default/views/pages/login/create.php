
<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_12">
			<h1>Create an account</h1>
		</div>
	</div>
</hgroup>

<div id="content" class="settings cf">
	<div class="center">
		<div class="col_9">
			
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
			
			<?php echo Form::open(); ?>
			
			<article class="container base">
				<header class="cf">
					<div class="property-title">
						<h1>Account information</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('Full Name'); ?></p>
							<?php echo Form::input("name", $form_name); ?>
						</label>
					</div>
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('Nickname'); ?></p>
							<?php echo Form::input("nickname", $form_nickname); ?>
						</label>
					</div>
				</section>
			</article>
			
			<article class="container base">
				<header class="cf">
					<div class="property-title">
						<h1>Choose a password</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('Password'); ?></p>
							<?php echo Form::password("password", ""); ?>
						</label>
					</div>
					<div class="parameter">
						<label for="password">
							<p class="field"><?php echo __('Verify Password'); ?></p>
							<?php echo Form::password("password_confirm", ""); ?>
						</label>
					</div>
				</section>
			</article>

			<div class="save-toolbar">
				<p class="button-blue" onclick="submitForm(this)"><a>Create your account</a></p>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>