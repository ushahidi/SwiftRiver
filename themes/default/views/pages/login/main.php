<?php if (isset($errors)): ?>
	<?php foreach ($errors as $message): ?>
		<article id="system-message">
			<div class="center">
				<a href="#" class="system-message-close"><span class="icon-cancel"></span></a>
				<p><?php echo $message; ?></p>
			</div>
		</article>
	<?php endforeach; ?>
<?php endif; ?>



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
						<li class="active"><a href="#login"><span class="label">Current users</span></a></li>
						<li><a href="#signup"><span class="label">New users</span></a></li>
					</ul>
					<div class="modal-tabs-window">
						<!-- Log in -->
						<div id="login" class="active">
							<?php echo Form::open(URL::site('login')); ?>
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
									<a href="#" class="button-submit button-primary modal-close" onclick="submitForm(this)">Log in</a>
									<a href="#" class="button-destruct button-secondary modal-transition">Forgot your password?</a>
								</div>
								<?php echo Form::hidden('referrer', $referrer); ?>
							<?php echo Form::close(); ?>
						</div>
							

						<!-- Sign up -->
						<div id="signup">
							<div class="modal-field">
								<h3 class="label">Name</h3>
								<input type="text" placeholder="Enter your full name..." />
							</div>
							<div class="modal-field">
								<h3 class="label">Email</h3>
								<input type="text" placeholder="Enter your email address..." />
							</div>
							<div class="modal-field">
								<h3 class="label">Create your password</h3>
								<input type="password" />
							</div>															
							<div class="modal-field">
								<h3 class="label">Confirm your password</h3>
								<input type="password" />
							</div>
							<div class="modal-base-toolbar">
								<a href="#" class="button-submit button-primary modal-close">Create account and log in</a>
							</div>							
						</div>																				
					</div>				
				</div>					
			</div>
		</article>

	</div>
</div>
