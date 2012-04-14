<?php
	$page_title = "Create an account";
	$template_type = "user";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>
	
	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1>Get started</h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/login">Log in</a></li>
			<li class="active"><a href="/markup/user/create.php">Create an account</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_9">
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Enter your account information</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="new_email">
								<p class="field">Email</p>
								<input type="email" placeholder="Enter your email address" />
							</label>
						</div>
					</section>
				</article>

				<div class="save-toolbar">
					<p class="button-blue" onclick="submitForm(this)"><a>Create your account</a></p>
				</div>
			</div>

			<div class="col_3">
				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>About your new account</h3>
					<div class="meta-data-content">
						<p>Text</p>
					</div>
				</section>
			</div>
		</div>
	</div>

<div id="zoom-container">
	<div class="modal-window"></div>
</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body> 
</html>