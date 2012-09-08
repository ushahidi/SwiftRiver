<?php
	$page_title = "Brandon Rosage";
	$template_type = "user";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="user-title cf">
		<div class="center">
			<div class="user-summary col_9">		
				<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
				<h1>Brandon Rosage</h1>
				<h2>brosage</h2>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/">Dashboard</a></li>
			<li class="active"><a href="/user/settings.php">Account settings</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>About you</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<div class="field">
								<p class="field-label">Full name</p>
								<input type="text" value="<?php print $page_title; ?>" />
							</div>
						</div>
						<div class="parameter">
							<div class="field">
								<p class="field-label">Username</p>
								<input type="text" value="brosage" />
							</div>
						</div>
						<div class="parameter">
							<div class="field">
								<p class="field-label">Email address</p>
								<input type="email" value="brandon@ushahidi.com" />
							</div>
						</div>						
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="#">Save changes</a></p>
							<p class="button-blank cancel"><a href="#">Cancel</a></p>
						</div>
					</section>
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>Change password</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<div class="field">
								<p class="field-label">New password</p>
								<input type="password" value="" />
							</div>
						</div>
						<div class="parameter">
							<div class="field">
								<p class="field-label">Confirm password</p>
								<input type="password" value="" />
							</div>
						</div>					
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="#">Save changes</a></p>
							<p class="button-blank cancel"><a href="#">Cancel</a></p>
						</div>
					</section>
				</article>
				
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>Photo</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
							<p class="button-blue button-small"><a href="http://gravatar.com">Use a different photo</a></p>
						</div>					
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="#">Save changes</a></p>
							<p class="button-blank cancel"><a href="#">Cancel</a></p>
						</div>
					</section>
				</article>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>