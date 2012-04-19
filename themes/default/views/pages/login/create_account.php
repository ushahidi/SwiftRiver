<div class="col_9">
	<?php echo Form::open(URL::site('login')); ?>
		<article class="container base">
			<header class="cf">
				<div class="property-title">
					<h1><?php echo __("Enter your email address below"); ?></h1>
				</div>
			</header>
			<section class="property-parameters">
				<div class="parameter">
					<label>
						<p class="field"><?php echo __('Your email address') ?></p>
						<?php echo Form::input("new_email", "", array('placeholder' => __("Email address e.g. me@example.com"))); ?>
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