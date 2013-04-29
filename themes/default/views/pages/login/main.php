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
						<li class="<?php echo 'login'== $active ? 'active' : ''; ?>"><a href="#login"><span class="label">Current users</span></a></li>
						<li class="<?php echo 'register'== $active ? 'active' : ''; ?>"><a href="#signup"><span class="label">New users</span></a></li>
					</ul>
					<div class="modal-tabs-window">
						<!-- Log in -->
						<div id="login" class="<?php echo 'login'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(URL::site('login', TRUE)); ?>
								<div class="modal-field">
									<h3 class="label">Email address</h3>
									<?php echo Form::input("username", "", array("placeholer" => "Enter your email address...")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Password</h3>
									<?php echo Form::password("password", ""); ?>
								</div>	
								<div class="modal-field">
									<?php echo Form::checkbox('remember', 1); ?>
									<?php echo __('Remember me'); ?>
								</div>
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">Log in</a>
									<a href="<?php echo URL::site('login/request_reset', TRUE); ?>" class="button-destruct button-secondary modal-transition">Forgot your password?</a>
								</div>
								<?php echo Form::hidden('referrer', $referrer); ?>
							<?php echo Form::close(); ?>
						</div>
						
						<!-- Reset password (Request token) -->
						<div id="request_reset" class="<?php echo 'request_reset'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(URL::site('login/request_reset', TRUE)); ?>
								<div class="modal-field">
									<h3 class="label">Email address</h3>
									<?php echo Form::input("email", "", array("placeholer" => "Enter your email address...")); ?>
								</div>
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">Reset my password</a>
								</div>
								<?php echo Form::hidden('referrer', $referrer); ?>
							<?php echo Form::close(); ?>
						</div>
						
						<!-- Reset password -->
						<div id="reset" class="<?php echo 'reset'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(); ?>
								<div class="modal-field">
									<h3 class="label">Enter a new password</h3>
									<?php echo Form::password("password", ""); ?>
								</div>	
								<div class="modal-field">
									<h3 class="label">Confirm your password</h3>
									<?php echo Form::password("password_confirm", ""); ?>
								</div>	
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this); return false;">Reset my password</a>
								</div>
							<?php echo Form::close(); ?>
						</div>
							

						<!-- Sign up -->
						<div id="signup" class="<?php echo 'register'== $active ? 'active' : ''; ?>">
							<?php echo Form::open(URL::site('login/register')); ?>
								<div class="modal-field">
									<h3 class="label">Name</h3>
									<?php echo Form::input("fullname", isset($fullname) ? $fullname : '', array("placeholer" => "Enter your full name...")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Email</h3>
									<?php echo Form::input("email", isset($email) ? $email : '', array("placeholer" => "Enter your email address...")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Username</h3>
									<?php echo Form::input("username", isset($username) ? $username : '', array("placeholer" => "Choose a username...")); ?>
								</div>
								<div class="modal-field">
									<h3 class="label">Create your password</h3>
									<?php echo Form::password("password"); ?>
								</div>															
								<div class="modal-field">
									<h3 class="label">Confirm your password</h3>
									<?php echo Form::password("password_confirm"); ?>
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
