<div id="content">
	<div class="center">
		
		<article class="modal">
			<hgroup class="page-title modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><i class="icon-login"></i><?php echo __('Get Started'); ?></h1>
			</hgroup>
	
			<div class="modal-body modal-tabs-container">
				<div class="base">
					<ul class="modal-tabs-menu">
						<li class="<?php echo 'login'== $active ? 'active' : ''; ?>">
							<a href="#login"><span class="label"><?php echo __("Current users"); ?></span></a>
						</li>
						<li class="<?php echo 'register'== $active ? 'active' : ''; ?>">
							<a href="#signup"><span class="label"><?php echo __("New users"); ?></span></a>
						</li>
					</ul>
					<div class="modal-tabs-window">
						<!-- Log in -->
						<div id="login" class="<?php echo 'login'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(URL::site('login', TRUE)); ?>
								<div class="modal-field">
									<h3 class="label"><?php echo __("Email address"); ?></h3>
									<?php echo Form::input("username", "", array("placeholder" => "Email")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label"><?php echo __("Password"); ?></h3>
									<?php echo Form::password("password", "", array('placeholder' => 'Password')); ?>
								</div>	
								<div class="modal-field">
									<?php echo Form::checkbox('remember', 1); ?>
									<?php echo __('Remember me'); ?>
								</div>
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">Log in</a>
									<a href="<?php echo URL::site('login/forgot_password', TRUE); ?>" class="button-destruct button-secondary modal-transition">Forgot your password?</a>
								</div>
								<?php echo Form::hidden('referrer', $referrer); ?>
							<?php echo Form::close(); ?>
						</div>

						<!-- Sign up -->
						<div id="signup" class="<?php echo 'register'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(URL::site('login/register')); ?>
								<div class="modal-field">
									<h3 class="label">Name</h3>
									<?php echo Form::input("fullname", isset($fullname) ? $fullname : '', array("placeholder" => "Full name")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Email</h3>
									<?php echo Form::input("email", isset($email) ? $email : '', array("placeholder" => "Email")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Username</h3>
									<?php echo Form::input("username", isset($username) ? $username : '', array("placeholer" => "Username")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Create your password</h3>
									<?php echo Form::password("password", "", array('placeholder' => 'Password')); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Confirm your password</h3>
									<?php echo Form::password("password_confirm", "", array('placeholder' => 'Confirm password')); ?>
								</div>
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">Create account and log in</a>
								</div>	
							<?php echo Form::close(); ?>
						</div>
					</div>
				</div>
			</div>
		</article>

	</div>
</div>
