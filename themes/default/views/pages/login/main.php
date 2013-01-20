<script type="text/javascript">
$(function() {
	// Enable login button if username and password are autofilled by the browser
	if ($('input[name="username"]').val() && $('input[name="username"]').val()) {
		$('.save-toolbar').addClass('visible');
	}
});
</script>
<div class="modal col_9">

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

<?php echo Form::open(URL::site('login')); ?>
	<article class="container base">
		<header class="cf">
			<div class="property-title col_12">
				<h1><?php echo __("Enter your account information"); ?></h1>
			</div>
		</header>
		<section class="property-parameters cf">
			<div class="parameter">
				<div class="field">
					<p class="field-label"><?php echo __('Email'); ?></p>
					<?php echo Form::input("username", ""); ?>
				</div>
				<div class="field">
					<p class="field-label"><?php echo __('Password'); ?></p>
					<?php echo Form::password("password", ""); ?>
				</div>
				<div class="field">
					<?php echo Form::checkbox('remember', 1); ?>
					<?php echo __('Remember me'); ?>
				</div>
			</div>
			<div class="save-toolbar col_12">
				<p class="button-blue"><a href="#">Log in</a></p>
			</div>			
		</section>
	</article>

	<div class="save-toolbar">
		<p class="button-blue" onclick="submitForm(this)"><a><?php echo __("Log in"); ?></a></p>
	</div>
	<?php echo Form::hidden('referrer', $referrer); ?>
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