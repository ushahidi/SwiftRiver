<?php
	$page_title = "Log in";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<div id="content">
		<div class="center">
		
			<article class="modal">
				<hgroup class="page-title modal-title cf">
					<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
					<h1><i class="icon-login"></i><?php print $page_title; ?></h1>
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
								<div class="modal-field">
									<h3 class="label">Email address</h3>
									<input type="text" placeholder="Enter your email address..." />
								</div>
								<div class="modal-field">
									<h3 class="label">Password</h3>
									<input type="password" />
								</div>	
								<div class="modal-base-toolbar">
									<a href="#" class="button-submit button-primary modal-close">Log in</a>
									<a href="#" class="button-destruct button-secondary modal-transition">Forgot your password?</a>
								</div>										
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

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-window-secondary"></div>
</div>

</body>
</html>