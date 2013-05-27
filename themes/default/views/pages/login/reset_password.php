<div id="content">
	<div class="center">
		<article class="modal">
			<hgroup class="page-title modal-title cf">
				<h1><?php echo __('Reset your password'); ?></h1>
			</hgroup>
			<div class="modal-body">
				<div class="base">
					<div id="reset">
						<?php echo Form::open(); ?>
							<div class="modal-field">
								<h3 class="label"><?php echo __("Enter a new password"); ?></h3>
								<?php echo Form::password("password", "", array('placeholder' => 'Password')); ?>
							</div>	
							<div class="modal-field">
								<h3 class="label"><?php echo __("Confirm your password"); ?></h3>
								<?php echo Form::password("password_confirm", "", array('placeholder' => 'Confirm password')); ?>
							</div>
							<div class="modal-base-toolbar">
								<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">
									<?php echo __("Reset my password"); ?>
								</a>
							</div>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</article>
	</div>
</div>
