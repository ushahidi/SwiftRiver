<div id="content">
	<div class="center">
		<article class="modal">
			<hgroup class="page-title modal-title cf">
				<h1><?php echo __('Forgot your password?'); ?></h1>
			</hgroup>
			<div class="modal-body">
				<div class="base">
					<div id="request_reset">
						<?php echo Form::open(URL::site('login/forgot_password', TRUE)); ?>
							<div class="modal-field">
								<h3 class="label"><?php echo __("Email address"); ?></h3>
								<?php echo Form::input("email", "", array("placeholder" => "Email")); ?>
							</div>
							<div class="modal-base-toolbar">
								<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">
									<?php echo __("Submit"); ?>
								</a>
							</div>
							<?php echo Form::hidden('referrer', $referrer); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</article>
	</div>
</div>