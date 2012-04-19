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
					<h1><?php echo __("Enter your email address below"); ?></h1>
				</div>
			</header>
			<section class="property-parameters">
				<div class="parameter">
					<label>
						<p class="field"><?php echo __('Email') ?></p>
						<?php echo Form::input("new_email", "", array('placeholder' => __("Enter your email address"))); ?>
					</label>
				</div>
			</section>
		</article>
		<div class="save-toolbar">
			<p class="button-blue" onclick="submitForm(this)">
				<a><?php echo __("Create your account"); ?></a>
			</p>
		</div>
	<?php echo Form::close(); ?>
</div>